<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\EmployeeLoan;
use App\Models\WorkOrderUser;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AbsensiSaya extends Page
{
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-finger-print';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'SDM & PENGGAJIAN';
    }

    public static function getNavigationLabel(): string
    {
        return 'Absensi & Tugas Saya';
    }

    public static function getNavigationSort(): ?int
    {
        return 0; // Tampil paling atas di grup SDM
    }

    public function getTitle(): string
    {
        return 'Portal Absensi & Penugasan Saya';
    }

    protected string $view = 'filament.pages.absensi-saya';

    public ?string $catatanEvent = '';

    /**
     * Action self-absensi untuk tugas event / SPK
     */
    public function checkInEvent(int $assignmentId, ?string $catatan = null): void
    {
        $assignment = WorkOrderUser::where('id', $assignmentId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $assignment) {
            Notification::make()
                ->title('Tugas tidak ditemukan atau bukan milik Anda.')
                ->danger()
                ->send();

            return;
        }

        if ($assignment->hadir) {
            Notification::make()
                ->title('Anda sudah melakukan absensi untuk tugas ini.')
                ->warning()
                ->send();

            return;
        }

        $now = now();
        $assignment->update([
            'hadir'             => true,
            'waktu_absen'       => $now,
            'catatan_kehadiran' => $catatan ?: 'Self check-in pada ' . $now->format('d M Y H:i') . ' WIB',
        ]);

        Notification::make()
            ->title('Absensi Berhasil!')
            ->body('Kehadiran Anda pada SPK ' . ($assignment->workOrder->spk_number ?? '') . ' telah berhasil dicatat.')
            ->success()
            ->send();
    }

    /**
     * Action self-absensi harian kantor (khusus karyawan bulanan)
     */
    public function checkInDaily(string $status = 'hadir', ?string $keterangan = null): void
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $existing = Attendance::where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        if ($existing) {
            Notification::make()
                ->title('Anda sudah mencatat absensi hari ini (' . ucfirst($existing->status) . ').')
                ->warning()
                ->send();

            return;
        }

        Attendance::create([
            'user_id'      => $userId,
            'tanggal'      => $today,
            'status'       => $status,
            'keterangan'   => $keterangan ?: 'Self check-in harian pada ' . now()->format('H:i') . ' WIB',
            'dicatat_oleh' => $userId,
        ]);

        Notification::make()
            ->title('Absensi Harian Berhasil!')
            ->body('Kehadiran hari ini (' . now()->translatedFormat('l, d F Y') . ') telah tercatat.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        $user = auth()->user();
        $profile = $user->employeeProfile;
        $today = now()->toDateString();

        // Absensi harian hari ini
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        // Daftar penugasan SPK
        $assignments = WorkOrderUser::with(['workOrder.order.customer'])
            ->where('user_id', $user->id)
            ->join('work_orders', 'work_orders.id', '=', 'work_order_users.work_order_id')
            ->select('work_order_users.*')
            ->orderBy('work_orders.tanggal_persiapan', 'desc')
            ->take(20)
            ->get();

        // Statistik bulan ini
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $eventHadirBulanIni = WorkOrderUser::where('work_order_users.user_id', $user->id)
            ->where('work_order_users.hadir', true)
            ->join('work_orders', 'work_orders.id', '=', 'work_order_users.work_order_id')
            ->whereBetween('work_orders.tanggal_persiapan', [$startOfMonth, $endOfMonth])
            ->count();

        $hariHadirBulanIni = Attendance::where('user_id', $user->id)
            ->where('status', 'hadir')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->count();

        $kasbonAktif = EmployeeLoan::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where('sisa_hutang', '>', 0)
            ->sum('sisa_hutang');

        return [
            'user'                => $user,
            'profile'             => $profile,
            'todayAttendance'     => $todayAttendance,
            'assignments'         => $assignments,
            'eventHadirBulanIni'  => $eventHadirBulanIni,
            'hariHadirBulanIni'   => $hariHadirBulanIni,
            'kasbonAktif'         => $kasbonAktif,
        ];
    }
}
