<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <!-- Hero / Header Title Section -->
    <div class="relative overflow-hidden rounded-3xl ocean-gradient text-white p-6 sm:p-10 shadow-xl shadow-sky-900/10">
        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-sky-100 text-xs font-semibold mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Monitoring Real-Time Pasang Surut Air Laut</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">Pesisir & Pelabuhan Jawa Timur</h1>
            <p class="text-sm sm:text-base text-sky-100/90 mt-2 leading-relaxed">
                Informasi prediksi dan dinamika tinggi muka air laut (Mean Sea Level) di 5 titik stasiun pengamatan BMKG Perak Surabaya untuk keselamatan pelayaran, nelayan, dan aktivitas maritim.
            </p>

            <div class="flex flex-wrap gap-3 mt-6">
                <a href="<?php echo e(route('user.kondisi')); ?>" class="px-5 py-2.5 rounded-xl bg-white text-sky-800 font-bold text-xs hover:bg-sky-50 shadow-md transition">
                    Lihat Grafik Detail &rarr;
                </a>
                <a href="<?php echo e(route('user.monitoring')); ?>" class="px-5 py-2.5 rounded-xl bg-sky-500/30 hover:bg-sky-500/40 text-white font-semibold text-xs border border-white/20 backdrop-blur-sm transition">
                    Buka Peta Interaktif
                </a>
            </div>
        </div>

        <!-- Decorative Water Wave Vector in Background -->
        <div class="absolute right-0 bottom-0 opacity-15 translate-x-12 translate-y-12 pointer-events-none">
            <svg class="w-96 h-96" viewBox="0 0 200 200" fill="currentColor">
                <path d="M42.7,-62.9C50.9,-52.8,50.1,-34.4,51.7,-19.2C53.4,-4,57.5,7.9,56.5,21.5C55.4,35,49.2,50.1,38.1,58.8C27.1,67.5,11.2,69.7,-4.8,75.9C-20.8,82.1,-37,92.3,-48.9,86.2C-60.8,80.1,-68.5,57.7,-74.6,38.8C-80.7,19.9,-85.2,4.6,-80.7,-8.4C-76.2,-21.3,-62.7,-31.9,-49.9,-41.2C-37.1,-50.5,-25,-58.5,-10.8,-62.5C3.4,-66.4,26.8,-66.3,42.7,-62.9Z" transform="translate(100 100)" />
            </svg>
        </div>
    </div>

    <!-- Banner Notifikasi Aktif dari Admin (Kelola Notif) -->
    <?php if($notifications->isNotEmpty()): ?>
        <div class="space-y-3">
            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="p-4 sm:p-5 rounded-2xl border flex items-start gap-3.5 transition shadow-xs
                    <?php echo e($notif->type === 'warning' ? 'bg-amber-50/80 border-amber-200 text-amber-900' : ''); ?>

                    <?php echo e($notif->type === 'danger' ? 'bg-rose-50/80 border-rose-200 text-rose-900' : ''); ?>

                    <?php echo e($notif->type === 'info' ? 'bg-sky-50/80 border-sky-200 text-sky-900' : ''); ?>

                    <?php echo e($notif->type === 'success' ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : ''); ?>">
                    
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5
                        <?php echo e($notif->type === 'warning' ? 'bg-amber-100 text-amber-700' : ''); ?>

                        <?php echo e($notif->type === 'danger' ? 'bg-rose-100 text-rose-700' : ''); ?>

                        <?php echo e($notif->type === 'info' ? 'bg-sky-100 text-sky-700' : ''); ?>

                        <?php echo e($notif->type === 'success' ? 'bg-emerald-100 text-emerald-700' : ''); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-bold"><?php echo e($notif->title); ?></h2>
                            <span class="text-[10px] text-slate-500 font-medium"><?php echo e($notif->created_at->diffForHumans()); ?></span>
                        </div>
                        <p class="text-xs mt-1 leading-relaxed opacity-90"><?php echo e($notif->message); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <!-- 5 Cards Titik Lokasi Monitoring -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Status 5 Titik Pengamatan</h2>
                <p class="text-xs text-slate-500">Ketinggian air laut relatif terhadap Mean Sea Level (MSL) jam <?php echo e($currentHour); ?>:00</p>
            </div>
            <a href="<?php echo e(route('user.kondisi')); ?>" class="text-xs font-semibold text-sky-600 hover:text-sky-700">Lihat Semua Grafik &rarr;</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php $__currentLoopData = $locationCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-sky-300 transition group flex flex-col justify-between">
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                                    <?php echo e($card['code']); ?>

                                </span>
                                <h3 class="text-base font-bold text-slate-900 mt-1 group-hover:text-sky-600 transition">
                                    <?php echo e($card['name']); ?>

                                </h3>
                            </div>
                            <!-- Status Badge -->
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                <?php echo e($card['status_color'] === 'amber' ? 'bg-amber-50 text-amber-700 border border-amber-200' : ''); ?>

                                <?php echo e($card['status_color'] === 'blue' ? 'bg-sky-50 text-sky-700 border border-sky-200' : ''); ?>

                                <?php echo e($card['status_color'] === 'emerald' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ''); ?>">
                                <span class="w-1.5 h-1.5 rounded-full
                                    <?php echo e($card['status_color'] === 'amber' ? 'bg-amber-500' : ''); ?>

                                    <?php echo e($card['status_color'] === 'blue' ? 'bg-sky-500' : ''); ?>

                                    <?php echo e($card['status_color'] === 'emerald' ? 'bg-emerald-500' : ''); ?>">
                                </span>
                                <?php echo e($card['status']); ?>

                            </span>
                        </div>

                        <!-- Main Metric Value -->
                        <div class="mt-4 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold tracking-tight <?php echo e($card['current_level'] >= 0 ? 'text-sky-600' : 'text-emerald-600'); ?>">
                                <?php echo e($card['current_level'] > 0 ? '+' : ''); ?><?php echo e($card['current_level']); ?>

                            </span>
                            <span class="text-xs font-bold text-slate-500">cm (MSL)</span>
                        </div>

                        <!-- Sparkline Mini Chart -->
                        <div class="mt-2 h-14">
                            <div id="sparkline-<?php echo e($card['id']); ?>" class="w-full h-full"></div>
                        </div>

                        <!-- HHW and LLW summary -->
                        <div class="mt-3 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                            <div class="bg-slate-50 p-2 rounded-xl">
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Pasang Tertinggi</span>
                                <span class="font-bold text-sky-700">+<?php echo e($card['hhw']); ?> cm</span>
                            </div>
                            <div class="bg-slate-50 p-2 rounded-xl">
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Surut Terendah</span>
                                <span class="font-bold text-emerald-700"><?php echo e($card['llw']); ?> cm</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Card Button -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <a href="<?php echo e(route('user.kondisi', ['location_id' => $card['id']])); ?>" class="font-semibold text-sky-600 hover:underline flex items-center gap-1">
                            <span>Grafik Lengkap</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <a href="<?php echo e(route('user.monitoring')); ?>" class="text-slate-400 hover:text-slate-600">
                            Peta Lokasi &rarr;
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Quick Info & Edukasi Pasang Surut Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-2">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Tipe Semi-Diurnal</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Perairan Jawa Timur mengalami 2 kali siklus pasang dan 2 kali surut dalam kurun waktu 24 jam.</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">HHW (High High Water)</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Ketinggian pasang air laut tertinggi yang dapat memicu genangan atau potensi banjir rob pesisir.</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">LLW (Low Low Water)</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Titik surut air laut paling rendah, menjadi acuan penting untuk kedalaman draft kapal pelayaran.</p>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cardsData = <?php echo json_encode($locationCards, 15, 512) ?>;

        cardsData.forEach(function(card) {
            const el = document.getElementById('sparkline-' + card.id);
            if (!el) return;

            const options = {
                series: [{
                    name: 'Ketinggian (cm)',
                    data: card.sparkline
                }],
                chart: {
                    type: 'area',
                    height: 55,
                    sparkline: { enabled: true },
                    animations: { enabled: true }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                colors: [card.current_level >= 0 ? '#0284c7' : '#10b981'],
                tooltip: {
                    fixed: { enabled: false },
                    x: { show: false },
                    y: {
                        title: { formatter: () => 'Level: ' },
                        formatter: (val) => val + ' cm'
                    },
                    marker: { show: false }
                }
            };

            new ApexCharts(el, options).render();
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\minah\SipasutBMKG-app\resources\views/user/dashboard.blade.php ENDPATH**/ ?>