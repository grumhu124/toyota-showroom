<?php

// Presentation-only examples. Not a specification, quotation or stock feed.
$base = ['year' => 2026, 'version' => 'Phiên bản minh họa', 'color' => 'Trắng', 'status' => 'available', 'status_label' => 'Có sẵn'];
$catalog = require __DIR__.'/collection-preview.php';
$cars = [
    1 => ['name' => 'Toyota Camry', 'category' => 'camry', 'code' => 'DEMO-CAM-001', 'image' => 'camry.png', 'price' => 1200000000],
    3 => ['name' => 'Toyota Corolla Cross', 'category' => 'corolla-cross', 'code' => 'DEMO-CC-001', 'image' => 'corolla.png', 'price' => 900000000],
    5 => ['name' => 'Toyota Innova', 'category' => 'innova', 'code' => 'DEMO-INN-001', 'image' => 'innova.png', 'price' => 1000000000, 'status' => 'sold', 'status_label' => 'Đã bán'],
];
foreach ($catalog['cars'] as $previewCar) {
    $cars[$previewCar['id']] = $previewCar;
}
foreach ($cars as $previewId => &$car) {
    $car = ['id' => $previewId] + $car + $base;
    if ($previewId === 1) {
        $car['color'] = 'Xám';
    }
}
unset($car);

return $cars;
