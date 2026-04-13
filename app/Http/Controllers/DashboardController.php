<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perencanaan;
use App\Models\PerencanaanDetail;
use App\Models\Realisasi;
use App\Models\MutasiKas;
use App\Models\Kategori;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $license = $user->license()->where('status', 'active')->first();
        $notification = null;

        if ($user->role !== 'super_admin') {
            if (!$license) {
                $notification = [
                    'type' => 'danger',
                    'icon' => 'feather-alert-triangle',
                    'title' => 'Belum Memiliki Lisensi',
                    'message' => 'Anda belum memiliki lisensi aktif. Silakan beli lisensi untuk mengakses fitur input data.',
                    'button_text' => 'Beli Lisensi Sekarang',
                    'button_url' => route('pricing'),
                    'dismissible' => false,
                ];
            } elseif ($license->isExpired()) {
                $notification = [
                    'type' => 'danger',
                    'icon' => 'feather-clock',
                    'title' => 'Lisensi Telah Habis',
                    'message' => 'Lisensi Anda sudah habis pada ' . $license->end_date->format('d/m/Y') . '. Anda hanya bisa melihat laporan, tidak bisa input data baru.',
                    'button_text' => 'Perpanjang Lisensi',
                    'button_url' => route('pricing'),
                    'dismissible' => false,
                ];
            } else {
                $daysLeft = $license->daysLeft();
                
                if ($daysLeft <= 7) {
                    $notification = [
                        'type' => 'danger',
                        'icon' => 'feather-alert-octagon',
                        'title' => 'Lisensi Akan Segera Habis!',
                        'message' => "Lisensi Anda akan habis dalam {$daysLeft} hari ({$license->end_date->format('d/m/Y')}). Segera perpanjang agar tidak kehilangan akses input data.",
                        'button_text' => 'Perpanjang Sekarang',
                        'button_url' => route('pricing'),
                        'dismissible' => true,
                    ];
                } elseif ($daysLeft <= 30) {
                    $notification = [
                        'type' => 'warning',
                        'icon' => 'feather-alert-circle',
                        'title' => 'Pengingat Perpanjangan Lisensi',
                        'message' => "Lisensi Anda akan habis dalam {$daysLeft} hari ({$license->end_date->format('d/m/Y')}). Jangan lupa perpanjang untuk kelancaran operasional.",
                        'button_text' => 'Lihat Paket',
                        'button_url' => route('pricing'),
                        'dismissible' => true,
                    ];
                } elseif ($daysLeft <= 60) {
                    $notification = [
                        'type' => 'info',
                        'icon' => 'feather-info',
                        'title' => 'Info Lisensi',
                        'message' => "Lisensi Anda masih aktif hingga {$license->end_date->format('d/m/Y')} (sisa {$daysLeft} hari).",
                        'button_text' => null,
                        'button_url' => null,
                        'dismissible' => true,
                    ];
                }
            }
        }

        $userId = Auth::id();

        // ============ FILTER ============
        $selectedYear  = $request->get('tahun', date('Y'));
        $selectedMonth = $request->get('bulan', 0);

        if ($selectedYear == 'all') {
            $selectedMonth = 0;
        }

        $selectedMonthName = $selectedMonth > 0
            ? Carbon::create()->month($selectedMonth)->translatedFormat('F')
            : 'Semua Bulan';

        // ============ TAHUN TERSEDIA (gabungan perencanaan + mutasi_kas) ============
        $yearFromPerencanaan = Perencanaan::where('user_id', $userId)
            ->select('tahun')
            ->distinct()
            ->pluck('tahun')
            ->toArray();

        $yearFromMutasi = MutasiKas::where('user_id', $userId)
            ->selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->pluck('tahun')
            ->toArray();

        $availableYears = collect(array_merge($yearFromPerencanaan, $yearFromMutasi))
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

        // ============ STATISTIK PERENCANAAN (tanpa filter = grand total) ============
        $totalRencana = PerencanaanDetail::join('perencanaans', 'perencanaan_details.perencanaan_id', '=', 'perencanaans.perencanaan_id')
            ->where('perencanaans.user_id', $userId)
            ->count();

        // Realisasi selesai (status_target = 'sesuai') — grand total
        $selesai = Realisasi::where('user_id', $userId)
            ->where('status_target', 'sesuai')
            ->count();

        $progressPersen = $totalRencana > 0 ? round(($selesai / $totalRencana) * 100) : 0;

        // ============ STATISTIK PERENCANAAN (dengan filter) ============
        $queryRencanaFiltered = PerencanaanDetail::join('perencanaans', 'perencanaan_details.perencanaan_id', '=', 'perencanaans.perencanaan_id')
            ->where('perencanaans.user_id', $userId);

        if ($selectedYear != 'all') {
            $queryRencanaFiltered->where('perencanaans.tahun', $selectedYear);
        }
        if ($selectedMonth > 0 && $selectedYear != 'all') {
            $queryRencanaFiltered->where('perencanaans.bulan', $selectedMonth);
        }

        $totalRencanaFiltered = $queryRencanaFiltered->count();

        // Realisasi selesai — dengan filter (join ke perencanaans untuk filter tahun/bulan)
        $querySelesaiFiltered = Realisasi::join('perencanaans', 'realisasi.perencanaan_id', '=', 'perencanaans.perencanaan_id')
            ->where('perencanaans.user_id', $userId)
            ->where('realisasi.status_target', 'sesuai');

        if ($selectedYear != 'all') {
            $querySelesaiFiltered->where('perencanaans.tahun', $selectedYear);
            if ($selectedMonth > 0) {
                $querySelesaiFiltered->where('perencanaans.bulan', $selectedMonth);
            }
        }

        $selesaiFiltered = $querySelesaiFiltered->count();
        $persenRencana   = $totalRencana > 0 ? round(($totalRencanaFiltered / $totalRencana) * 100) : 0;

        // ============ STATISTIK KAS ============
        $queryKas = MutasiKas::where('user_id', $userId);

        if ($selectedYear != 'all') {
            $queryKas->whereYear('tanggal', $selectedYear);
            if ($selectedMonth > 0) {
                $queryKas->whereMonth('tanggal', $selectedMonth);
            }
        }

        $stats = [
            'total_mutasi_debit'  => (clone $queryKas)->sum('debit')  ?? 0,
            'total_mutasi_kredit' => (clone $queryKas)->sum('kredit') ?? 0,
        ];

        $totalTransaksiDebit  = (clone $queryKas)->where('debit',  '>', 0)->count();
        $totalTransaksiKredit = (clone $queryKas)->where('kredit', '>', 0)->count();
        $totalTransaksi       = $totalTransaksiDebit + $totalTransaksiKredit;

        $totalAmount       = $stats['total_mutasi_debit'] + $stats['total_mutasi_kredit'];
        $persenPemasukan   = $totalAmount > 0 ? round(($stats['total_mutasi_debit']  / $totalAmount) * 100) : 0;
        $persenPengeluaran = $totalAmount > 0 ? round(($stats['total_mutasi_kredit'] / $totalAmount) * 100) : 0;

        // ============ CHART TREND PERENCANAAN ============
        $chartJumlahRencana = $this->getChartJumlahRencana($userId, $selectedYear);

        // ============ AKTIVITAS TERATAS (gabungan perencanaan + realisasi) ============
        $topAktivitas = $this->getTopAktivitas($userId, $selectedYear, (int) $selectedMonth);

        // ============ 5 PERENCANAAN TERBARU ============
        $recentPerencanaan = PerencanaanDetail::with('perencanaan')
            ->join('perencanaans', 'perencanaan_details.perencanaan_id', '=', 'perencanaans.perencanaan_id')
            ->where('perencanaans.user_id', $userId)
            ->select(
                'perencanaan_details.*',
                'perencanaans.bulan',
                'perencanaans.tahun',
                'perencanaans.judul'
            )
            ->orderBy('perencanaans.tahun', 'desc')
            ->orderBy('perencanaans.bulan', 'desc')
            ->limit(5)
            ->get();

        // ============ CASH FLOW CHART ============
        $chartMutasiKas = $this->getChartMutasiKas($userId, $selectedYear);

        // ============ KATEGORI — global (user_id NULL) + milik user ============
        $kategoriAnggaran = Kategori::with([
                'kodeTransaksi' => fn($q) => $q->visibleToUser()
            ])
            ->visibleToUser()
            ->get();

        $kategoriColors = [
            'Penerimaan Pendapatan'     => ['bg' => 'success', 'icon' => 'trending-up'],
            'Penerimaan Non Pendapatan' => ['bg' => 'info',    'icon' => 'download'],
            'Pengeluaran Biaya'         => ['bg' => 'danger',  'icon' => 'trending-down'],
            'Pengeluaran Non Biaya'     => ['bg' => 'warning', 'icon' => 'upload'],
        ];

        // ============ DISTRIBUSI KATEGORI CHART DATA ============
        $chartKategoriData = $this->getChartKategoriData($userId, $selectedYear, (int) $selectedMonth);

        // ============ SUPER ADMIN DATA ============
        $sa = [];
        if (Auth::user()->role == 'super_admin') {
            $sa = $this->getSuperAdminData();
        }

        // ============ DATA INSTANSI ============
        $schoolSetting = DB::table('school_settings')->where('user_id', $userId)->first();
        $instansi = [
            'nama'   => $schoolSetting->nama_sekolah ?? Auth::user()->name ?? 'Instansi',
            'logo'   => $schoolSetting->logo_sekolah ?? null,   // path relatif dari storage/app/public
            'alamat' => $schoolSetting->alamat       ?? null,
        ];

        return view('dashboard', compact(
            'selectedYear',
            'selectedMonth',
            'selectedMonthName',
            'availableYears',
            'totalRencana',
            'selesai',
            'progressPersen',
            'totalRencanaFiltered',
            'selesaiFiltered',
            'persenRencana',
            'stats',
            'totalTransaksi',
            'totalTransaksiDebit',
            'totalTransaksiKredit',
            'persenPemasukan',
            'persenPengeluaran',
            'chartJumlahRencana',
            'topAktivitas',
            'recentPerencanaan',
            'chartMutasiKas',
            'kategoriAnggaran',
            'kategoriColors',
            'chartKategoriData',
            'sa',
            'instansi',
            'notification',
            'license',
        ));
    }

    // ─────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Chart trend perencanaan per bulan
     */
    private function getChartJumlahRencana(int $userId, $tahun): array
    {
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        if ($tahun == 'all') {
            $data = PerencanaanDetail::join('perencanaans', 'perencanaan_details.perencanaan_id', '=', 'perencanaans.perencanaan_id')
                ->where('perencanaans.user_id', $userId)
                ->select('perencanaans.tahun', 'perencanaans.bulan', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('perencanaans.tahun', 'perencanaans.bulan')
                ->orderBy('perencanaans.tahun')
                ->orderBy('perencanaans.bulan')
                ->get();

            $result = [];
            foreach ($data as $item) {
                $result[] = [
                    'bulan'  => $months[$item->bulan - 1] . ' ' . $item->tahun,
                    'tahun'  => $item->tahun,
                    'jumlah' => (int) $item->jumlah,
                ];
            }

            return $result;
        }

        $data = PerencanaanDetail::join('perencanaans', 'perencanaan_details.perencanaan_id', '=', 'perencanaans.perencanaan_id')
            ->where('perencanaans.user_id', $userId)
            ->where('perencanaans.tahun', $tahun)
            ->select('perencanaans.bulan', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('perencanaans.bulan')
            ->get()
            ->keyBy('bulan');

        $result = [];
        foreach ($months as $index => $monthName) {
            $bulan    = $index + 1;
            $result[] = [
                'bulan'  => $monthName,
                'jumlah' => isset($data[$bulan]) ? (int) $data[$bulan]->jumlah : 0,
            ];
        }

        return $result;
    }

    /**
     * Aktivitas Teratas — gabungan jumlah perencanaan + realisasi per bulan,
     * diurutkan descending, top 6.
     */
    private function getTopAktivitas(int $userId, $tahun, int $bulan): array
    {
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        $qPerencanaan = PerencanaanDetail::join('perencanaans', 'perencanaan_details.perencanaan_id', '=', 'perencanaans.perencanaan_id')
            ->where('perencanaans.user_id', $userId)
            ->select('perencanaans.tahun', 'perencanaans.bulan', DB::raw('COUNT(*) as jumlah_rencana'));

        $qRealisasi = Realisasi::join('perencanaans', 'realisasi.perencanaan_id', '=', 'perencanaans.perencanaan_id')
            ->where('perencanaans.user_id', $userId)
            ->select('perencanaans.tahun', 'perencanaans.bulan', DB::raw('COUNT(*) as jumlah_realisasi'));

        if ($tahun != 'all') {
            $qPerencanaan->where('perencanaans.tahun', $tahun);
            $qRealisasi->where('perencanaans.tahun', $tahun);

            if ($bulan > 0) {
                $qPerencanaan->where('perencanaans.bulan', $bulan);
                $qRealisasi->where('perencanaans.bulan', $bulan);
            }
        }

        $dataRencana   = $qPerencanaan->groupBy('perencanaans.tahun', 'perencanaans.bulan')->get()->keyBy(fn($r) => $r->tahun . '-' . $r->bulan);
        $dataRealisasi = $qRealisasi->groupBy('perencanaans.tahun', 'perencanaans.bulan')->get()->keyBy(fn($r) => $r->tahun . '-' . $r->bulan);

        $allKeys = collect($dataRencana->keys())->merge($dataRealisasi->keys())->unique();

        $result = [];
        foreach ($allKeys as $key) {
            [$yr, $mn] = explode('-', $key);
            $jmlRencana   = isset($dataRencana[$key])   ? (int) $dataRencana[$key]->jumlah_rencana   : 0;
            $jmlRealisasi = isset($dataRealisasi[$key]) ? (int) $dataRealisasi[$key]->jumlah_realisasi : 0;

            $result[] = [
                'bulan'            => $months[(int)$mn - 1],
                'tahun'            => (int) $yr,
                'jumlah'           => $jmlRencana + $jmlRealisasi,
                'jumlah_rencana'   => $jmlRencana,
                'jumlah_realisasi' => $jmlRealisasi,
            ];
        }

        return collect($result)
            ->sortByDesc('jumlah')
            ->take(6)
            ->values()
            ->toArray();
    }

    /**
     * Cash Flow chart per bulan
     */
    private function getChartMutasiKas(int $userId, $tahun): array
    {
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $data   = [];

        if ($tahun == 'all') {
            $mutasiData = MutasiKas::where('user_id', $userId)
                ->select(
                    DB::raw('YEAR(tanggal) as tahun'),
                    DB::raw('MONTH(tanggal) as bulan'),
                    DB::raw('SUM(debit) as total_debit'),
                    DB::raw('SUM(kredit) as total_kredit')
                )
                ->groupBy(DB::raw('YEAR(tanggal)'), DB::raw('MONTH(tanggal)'))
                ->orderBy(DB::raw('YEAR(tanggal)'))
                ->orderBy(DB::raw('MONTH(tanggal)'))
                ->get();

            foreach ($mutasiData as $item) {
                $data[] = [
                    'bulan'       => $months[$item->bulan - 1] . ' ' . $item->tahun,
                    'pemasukan'   => (float) $item->total_debit,
                    'pengeluaran' => (float) $item->total_kredit,
                ];
            }
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $base = MutasiKas::where('user_id', $userId)
                    ->whereMonth('tanggal', $i)
                    ->whereYear('tanggal', $tahun);

                $data[] = [
                    'bulan'       => $months[$i - 1],
                    'pemasukan'   => (float) (clone $base)->sum('debit'),
                    'pengeluaran' => (float) (clone $base)->sum('kredit'),
                ];
            }
        }

        return $data;
    }

    /**
     * Distribusi Kategori — nominal total per kategori (untuk chart donut)
     */
    private function getChartKategoriData(int $userId, $tahun, int $bulan): array
    {
        $kategoriList = Kategori::visibleToUser()->get();

        $result = [];
        foreach ($kategoriList as $kategori) {
            $query = DB::table('mutasi_kas')
                ->join('kode_transaksi', 'mutasi_kas.kode_transaksi_id', '=', 'kode_transaksi.kode_transaksi_id')
                ->where('mutasi_kas.user_id', $userId)
                ->where('kode_transaksi.kategori_id', $kategori->kategori_id)
                ->where(function ($q) use ($userId) {
                    // kode_transaksi global (null) atau milik user
                    $q->whereNull('kode_transaksi.user_id')
                      ->orWhere('kode_transaksi.user_id', $userId);
                });

            if ($tahun != 'all') {
                $query->whereYear('mutasi_kas.tanggal', $tahun);
                if ($bulan > 0) {
                    $query->whereMonth('mutasi_kas.tanggal', $bulan);
                }
            }

            $nominal = $query->sum(DB::raw('mutasi_kas.debit + mutasi_kas.kredit'));

            $result[] = [
                'nama'    => $kategori->nama_kategori,
                'nominal' => (float) $nominal,
            ];
        }

        return array_values(array_filter($result, fn($r) => $r['nominal'] > 0));
    }

    /**
     * Data khusus Super Admin Dashboard
     */
    private function getSuperAdminData(): array
    {
        $now    = Carbon::now();
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        // ── User stats ──────────────────────────────────────────────
        $totalUser        = DB::table('users')->where('role', '!=', 'super_admin')->count();
        $totalUserAktif   = DB::table('licenses')->where('status', 'active')->where('end_date', '>=', $now)->distinct('user_id')->count();
        $userSuspended    = DB::table('licenses')->where('status', 'suspended')->distinct('user_id')->count();
        $userBaruBulanIni = DB::table('users')
            ->where('role', '!=', 'super_admin')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        // ── Lisensi stats (field: end_date) ─────────────────────────
        $lisensiAktif = DB::table('licenses')
            ->where('status', 'active')
            ->where('end_date', '>', $now)
            ->count();

        $lisensiExpired = DB::table('licenses')
            ->where(function ($q) use ($now) {
                $q->where('status', 'expired')->orWhere('end_date', '<=', $now);
            })
            ->count();

        $lisensiAkanExpired = DB::table('licenses')
            ->where('status', 'active')
            ->where('end_date', '>', $now)
            ->where('end_date', '<=', $now->copy()->addDays(30))
            ->count();

        // ── Revenue stats (tabel: payments, status: settlement/capture) ─
        $totalRevenue             = (float) DB::table('payments')->whereIn('status', ['settlement', 'capture'])->sum('amount');
        $revenueBulanIni          = (float) DB::table('payments')
            ->whereIn('status', ['settlement', 'capture'])
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('amount');
        $totalTransaksiPembayaran = DB::table('payments')->whereIn('status', ['settlement', 'capture'])->count();

        // ── User growth chart (tahun ini per bulan) ─────────────────
        $userGrowthChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $userGrowthChart[] = [
                'bulan'  => $months[$i - 1],
                'jumlah' => DB::table('users')
                    ->where('role', '!=', 'super_admin')
                    ->whereMonth('created_at', $i)
                    ->whereYear('created_at', $now->year)
                    ->count(),
            ];
        }

        // ── Revenue chart (tahun ini per bulan) ─────────────────────
        $revenueChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueChart[] = [
                'bulan'   => $months[$i - 1],
                'nominal' => (float) DB::table('payments')
                    ->whereIn('status', ['settlement', 'capture'])
                    ->whereMonth('created_at', $i)
                    ->whereYear('created_at', $now->year)
                    ->sum('amount'),
            ];
        }

        // ── Distribusi paket lisensi ────────────────────────────────
        $distribusiRaw = DB::table('licenses')
            ->select('package_type', DB::raw('COUNT(*) as jumlah'))
            ->where('status', 'active')
            ->groupBy('package_type')
            ->get();

        $distribusiPaket = $distribusiRaw->count()
            ? $distribusiRaw->map(fn($r) => ['nama' => $r->package_type ?? 'Unknown', 'jumlah' => (int) $r->jumlah])->toArray()
            : [['nama' => 'Belum ada data', 'jumlah' => 1]];

        // ── User terbaru (dengan status lisensi terbaru) ────────────
        $userTerbaru = DB::table('users')
            ->leftJoin('licenses', function ($join) {
                $join->on('licenses.user_id', '=', 'users.id')
                     ->whereRaw('licenses.id = (SELECT id FROM licenses l2 WHERE l2.user_id = users.id ORDER BY l2.created_at DESC LIMIT 1)');
            })
            ->where('users.role', '!=', 'super_admin')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'licenses.status as license_status')
            ->orderBy('users.created_at', 'desc')
            ->limit(8)
            ->get();

        // ── Lisensi mau expired (field: end_date) ───────────────────
        $lisensiMauExpired = DB::table('licenses')
            ->join('users', 'licenses.user_id', '=', 'users.id')
            ->select('licenses.*', 'users.name as user_name', 'users.email as user_email')
            ->where('licenses.status', 'active')
            ->where('licenses.end_date', '>', $now)
            ->where('licenses.end_date', '<=', $now->copy()->addDays(30))
            ->orderBy('licenses.end_date')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $row->user = (object) ['name' => $row->user_name, 'email' => $row->user_email];
                return $row;
            });

        return compact(
            'totalUser', 'totalUserAktif', 'userSuspended', 'userBaruBulanIni',
            'lisensiAktif', 'lisensiExpired', 'lisensiAkanExpired',
            'totalRevenue', 'revenueBulanIni', 'totalTransaksiPembayaran',
            'userGrowthChart', 'revenueChart', 'distribusiPaket',
            'userTerbaru', 'lisensiMauExpired'
        );
    }
}