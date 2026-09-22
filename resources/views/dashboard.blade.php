<x-app-layout>
    <div class="content">

        <!-- Welcome Banner -->
        <div class="banner">
            <div>
                <h2>Selamat datang, {{ Auth::user()->name }}!</h2>
                <p>Kelola informasi pasang surut air laut saat ini</p>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon blue">📍</div>
                <div>
                    <div class="label">Total Stasiun</div>
                    <div class="value">{{ $totalStasiun }}</div>
                    <div class="sub">● Stasiun Aktif</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="icon indigo">🗄️</div>
                <div>
                    <div class="label">Data Pasang Surut</div>
                    <div class="value">{{ $totalData }}</div>
                    <div class="sub muted">Data Tersimpan</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="icon red">⚠️</div>
                <div>
                    <div class="label">Peringatan Aktif</div>
                    <div class="value">{{ $totalPeringatan }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="icon sky">🌤️</div>
                <div>
                    <div class="label">Surabaya</div>
                    <div class="value">28°C</div>
                    <div class="sub muted">Cerah Berawan</div>
                </div>
            </div>
        </div>

        <!-- Menu Section -->
        <div class="section-title">Kelola Data Sistem</div>
        <div class="menu-grid">

            <div class="menu-card">
                <div class="icon-circle">🌊</div>
                <div>
                    <h3>Kelola Data Pasang Surut</h3>
                    <p>Tambah, ubah, hapus, dan kelola data pasang surut dari setiap stasiun.</p>
                    <a href="#" class="btn">Kelola Data ›</a>
                </div>
            </div>

            <div class="menu-card">
                <div class="icon-circle">🗺️</div>
                <div>
                    <h3>Kelola Lokasi Stasiun</h3>
                    <p>Tambah, ubah, hapus, dan kelola informasi lokasi stasiun monitoring.</p>
                    <a href="#" class="btn">Kelola Data ›</a>
                </div>
            </div>

            <div class="menu-card">
                <div class="icon-circle">📅</div>
                <div>
                    <h3>Kelola Prediksi</h3>
                    <p>Kelola perkiraan pasang dan surut untuk waktu mendatang.</p>
                    <a href="#" class="btn">Kelola Data ›</a>
                </div>
            </div>

            <div class="menu-card">
                <div class="icon-circle">🚨</div>
                <div>
                    <h3>Kelola Peringatan</h3>
                    <p>Kelola peringatan dini terkait kondisi pasang surut.</p>
                    <a href="#" class="btn">Kelola Data ›</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>