<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Notification;
use App\Models\TidalData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /**
     * 1. Dashboard - USER
     */
    public function dashboard()
    {
        $locations = Location::where('is_active', true)->get();
        $notifications = Notification::where('is_active', true)->latest()->take(3)->get();

        // Ambil data ketinggian air jam sekarang (atau jam default 12 jika data bulanan)
        $currentHour = (int)date('G'); // 0-23
        if ($currentHour === 0) $currentHour = 24;

        $targetDate = '2026-07-15'; // Default tanggal acuan dataset BMKG Perak

        $locationCards = [];
        foreach ($locations as $loc) {
            // Ambil data hari ini
            $dayData = TidalData::where('location_id', $loc->id)
                ->where('record_date', $targetDate)
                ->orderBy('hour', 'asc')
                ->get();

            if ($dayData->isEmpty()) {
                // Fallback jika belum ada data di targetDate
                $dayData = TidalData::where('location_id', $loc->id)
                    ->orderBy('record_date', 'desc')
                    ->take(24)
                    ->get();
            }

            $currentRecord = $dayData->firstWhere('hour', $currentHour) ?? $dayData->first();
            $currentLevel = $currentRecord?->water_level ?? 0;

            $maxLevel = $dayData->max('water_level') ?? 0;
            $minLevel = $dayData->min('water_level') ?? 0;

            // Status pasang / surut berdasarkan level
            $status = $currentLevel >= 50 ? 'Pasang Tinggi' : ($currentLevel >= 0 ? 'Pasang Sedang' : 'Surut');
            $statusColor = $currentLevel >= 50 ? 'amber' : ($currentLevel >= 0 ? 'blue' : 'emerald');

            $sparkline = $dayData->pluck('water_level')->toArray();

            $locationCards[] = [
                'id' => $loc->id,
                'name' => $loc->name,
                'code' => $loc->code,
                'current_level' => $currentLevel,
                'status' => $status,
                'status_color' => $statusColor,
                'hhw' => $maxLevel,
                'llw' => $minLevel,
                'sparkline' => $sparkline,
                'latitude' => $loc->latitude,
                'longitude' => $loc->longitude,
            ];
        }

        return view('user.dashboard', compact('locations', 'locationCards', 'notifications', 'targetDate', 'currentHour'));
    }

    /**
     * 2. Kondisi Pasang Surut - USER
     */
    public function tideConditions(Request $request)
    {
        $locations = Location::where('is_active', true)->get();
        $selectedLocationId = $request->query('location_id', $locations->first()?->id);
        $selectedDate = $request->query('date', '2026-07-15');

        $selectedLocation = Location::find($selectedLocationId) ?? $locations->first();

        // 24 jam data untuk tanggal terpilih
        $hourlyData = TidalData::where('location_id', $selectedLocation?->id)
            ->where('record_date', $selectedDate)
            ->orderBy('hour', 'asc')
            ->get();

        // Hitung HHW dan LLW hari itu
        $hhwRecord = $hourlyData->sortByDesc('water_level')->first();
        $llwRecord = $hourlyData->sortBy('water_level')->first();
        $avgLevel = $hourlyData->isNotEmpty() ? round($hourlyData->avg('water_level'), 1) : 0;

        // Data 30 hari untuk monthly overview chart
        $parsedDate = Carbon::parse($selectedDate);
        $monthlyData = TidalData::where('location_id', $selectedLocation?->id)
            ->whereYear('record_date', $parsedDate->year)
            ->whereMonth('record_date', $parsedDate->month)
            ->selectRaw('record_date, MAX(water_level) as max_level, MIN(water_level) as min_level, AVG(water_level) as avg_level')
            ->groupBy('record_date')
            ->orderBy('record_date', 'asc')
            ->get();

        return view('user.kondisi-pasang-surut', compact(
            'locations',
            'selectedLocation',
            'selectedDate',
            'hourlyData',
            'hhwRecord',
            'llwRecord',
            'avgLevel',
            'monthlyData'
        ));
    }

    /**
     * 3. Lokasi Monitoring - USER
     */
    public function monitoring(Request $request)
    {
        $locations = Location::where('is_active', true)->get();
        $targetDate = '2026-07-15';
        $currentHour = 12;

        $geoLocations = [];
        foreach ($locations as $loc) {
            $record = TidalData::where('location_id', $loc->id)
                ->where('record_date', $targetDate)
                ->where('hour', $currentHour)
                ->first();

            $level = $record?->water_level ?? 0;
            $status = $level >= 50 ? 'Pasang Tinggi' : ($level >= 0 ? 'Pasang Normal' : 'Surut');

            $geoLocations[] = [
                'id' => $loc->id,
                'name' => $loc->name,
                'code' => $loc->code,
                'lat' => (float)$loc->latitude,
                'lng' => (float)$loc->longitude,
                'level' => $level,
                'status' => $status,
                'institution' => $loc->institution,
                'description' => $loc->description,
            ];
        }

        return view('user.lokasi-monitoring', compact('locations', 'geoLocations', 'targetDate'));
    }

    /**
     * 4. Kalender - USER
     */
    public function calendar(Request $request)
    {
        $locations = Location::where('is_active', true)->get();
        $selectedLocationId = $request->query('location_id', $locations->first()?->id);
        $month = (int)$request->query('month', 7);
        $year = (int)$request->query('year', 2026);

        $selectedLocation = Location::find($selectedLocationId) ?? $locations->first();

        // Ambil data ringkasan per hari untuk kalender
        $calendarDays = TidalData::where('location_id', $selectedLocation?->id)
            ->whereYear('record_date', $year)
            ->whereMonth('record_date', $month)
            ->selectRaw('record_date, MAX(water_level) as max_level, MIN(water_level) as min_level')
            ->groupBy('record_date')
            ->orderBy('record_date', 'asc')
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->record_date)->format('j'); // day number 1..31
            });

        return view('user.kalender', compact(
            'locations',
            'selectedLocation',
            'month',
            'year',
            'calendarDays'
        ));
    }

    /**
     * API untuk mengambil data grafik hari tertentu saat kalender diklik
     */
    public function apiDayDetails(Request $request)
    {
        $locationId = $request->query('location_id');
        $date = $request->query('date');

        $data = TidalData::where('location_id', $locationId)
            ->where('record_date', $date)
            ->orderBy('hour', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'date' => $date,
            'hours' => $data->pluck('hour'),
            'levels' => $data->pluck('water_level'),
        ]);
    }
}
