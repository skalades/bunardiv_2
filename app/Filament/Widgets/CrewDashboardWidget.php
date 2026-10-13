<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\EmployeeLoan;
use App\Models\WorkOrderUser;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class CrewDashboardWidget extends Widget
{
    protected string $view = 'filament.widgets.crew-dashboard-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'crew';
    }

    public function checkInEvent(int $assignmentId): void
    {
        $assignment = WorkOrderUser::where('id', $assignmentId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $assignment) {
            Notification::make()->title('Tugas tidak ditemukan')->danger()->send();

            return;
        }

        if ($assignment->hadir) {
            Notification::make()->title('Anda sudah absen untuk tugas ini')->warning()->send();

            return;
        }

        $now = now();
        $assignment->update([
            'hadir' => true,
            'waktu_absen' => $now,
            'catatan_kehadiran' => 'Self check-in dashboard pada '.$now->format('d M Y H:i').' WIB',
        ]);

        Notification::make()
            ->title('Absensi Berhasil!')
            ->body('Kehadiran Anda pada SPK '.($assignment->workOrder->spk_number ?? '').' telah berhasil dicatat.')
            ->success()
            ->send();
    }

    public function checkInDaily(string $status = 'hadir'): void
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $existing = Attendance::where('user_id', $userId)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existing) {
            Notification::make()->title('Anda sudah mencatat absensi hari ini ('.ucfirst($existing->status).')')->warning()->send();

            return;
        }

        try {
            Attendance::create([
                'user_id' => $userId,
                'tanggal' => $today,
                'status' => $status,
                'keterangan' => 'Self check-in harian dashboard pada '.now()->format('H:i').' WIB',
                'dicatat_oleh' => $userId,
            ]);

            Notification::make()
                ->title('Absensi Harian Berhasil!')
                ->body('Kehadiran kantor hari ini telah dicatat.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Anda sudah mencatat absensi hari ini')->warning()->send();
        }
    }

    protected function getViewData(): array
    {
        $user = auth()->user();
        $profile = $user?->employeeProfile;
        $today = now()->toDateString();

        $todayAttendance = Attendance::where('user_id', $user?->id)
            ->whereDate('tanggal', $today)
            ->first();

        $assignments = WorkOrderUser::with(['workOrder.order.customer'])
            ->where('user_id', $user?->id)
            ->join('work_orders', 'work_orders.id', '=', 'work_order_users.work_order_id')
            ->select('work_order_users.*')
            ->orderBy('work_orders.tanggal_persiapan', 'desc')
            ->take(10)
            ->get();

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $eventHadirBulanIni = WorkOrderUser::where('work_order_users.user_id', $user?->id)
            ->where('work_order_users.hadir', true)
            ->join('work_orders', 'work_orders.id', '=', 'work_order_users.work_order_id')
            ->whereBetween('work_orders.tanggal_persiapan', [$startOfMonth, $endOfMonth])
            ->count();

        $hariHadirBulanIni = Attendance::where('user_id', $user?->id)
            ->where('status', 'hadir')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->count();

        $kasbonAktif = EmployeeLoan::where('user_id', $user?->id)
            ->where('status', 'disetujui')
            ->where('sisa_hutang', '>', 0)
            ->sum('sisa_hutang');

        return [
            'user' => $user,
            'profile' => $profile,
            'todayAttendance' => $todayAttendance,
            'assignments' => $assignments,
            'eventHadirBulanIni' => $eventHadirBulanIni,
            'hariHadirBulanIni' => $hariHadirBulanIni,
            'kasbonAktif' => $kasbonAktif,
        ];
    }
}
