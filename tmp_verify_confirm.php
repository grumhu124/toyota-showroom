<?php
// Tìm lịch confirmed gần nhất và so sánh confirmed_at với requested_at.
use App\Models\Appointment;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$confirmed = Appointment::query()->where('status', 'confirmed')->latest('updated_at')->first();
if ($confirmed === null) {
    echo "khong co lich confirmed nao\n";
    exit;
}
echo 'id='.$confirmed->id
    .' requested='.$confirmed->requested_at->format('Y-m-d H:i')
    .' confirmed='.($confirmed->confirmed_at?->format('Y-m-d H:i') ?? '-')
    .' khop_gio='.($confirmed->confirmed_at?->equalTo($confirmed->requested_at) ? 'YES' : 'NO').PHP_EOL;
