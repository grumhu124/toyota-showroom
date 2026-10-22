<?php
// Xem toàn bộ lịch hẹn trong DB dev hiện tại.
use App\Models\Appointment;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'Tong so lich hen: '.Appointment::count().PHP_EOL;
foreach (Appointment::query()->orderBy('id')->with(['customer', 'assignedStaff'])->get() as $a) {
    echo '#'.$a->id
        .' | type='.$a->type->value
        .' | status='.$a->status->value
        .' | khach='.($a->customer?->email ?? 'guest('.$a->contact_name.')')
        .' | phan_cong='.($a->assignedStaff?->email ?? 'chua')
        .' | requested='.$a->requested_at->format('d/m H:i')
        .' | confirmed='.($a->confirmed_at?->format('d/m H:i') ?? '-').PHP_EOL;
}
