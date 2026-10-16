<?php

require __DIR__.'/project-files.php';

$root = dirname(__DIR__);
$mode = $argv[1] ?? '--check';
if (! in_array($mode, ['--check', '--write'], true)) {
    fwrite(STDERR, "Usage: php scripts/architecture-inventory.php [--write|--check]\n");
    exit(2);
}
$current = [];
foreach (controlledProjectFiles($root) as $path) {
    $current[$path] = controlledProjectFileHash($root.'/'.$path);
}
$target = $root.'/docs/architecture/INVENTORY.json';
if ($mode === '--write') {
    $document = ['format' => 2, 'algorithm' => 'sha256', 'text_line_endings' => 'lf', 'files' => $current];
    file_put_contents($target, json_encode($document, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
    echo 'Inventory updated: '.count($current)." controlled files.\n";
    exit(0);
}

if (! is_file($target)) {
    fwrite(STDERR, "Inventory missing. Run with --write after reviewing the changes.\n");
    exit(1);
}
$saved = json_decode(file_get_contents($target), true, flags: JSON_THROW_ON_ERROR)['files'];
$added = array_keys(array_diff_key($current, $saved));
$deleted = array_keys(array_diff_key($saved, $current));
$modified = [];
foreach (array_intersect_key($current, $saved) as $path => $hash) {
    if ($hash !== $saved[$path]) {
        $modified[] = $path;
    }
}
foreach (['ADDED' => $added, 'MODIFIED' => $modified, 'DELETED' => $deleted] as $label => $paths) {
    foreach ($paths as $path) {
        fwrite(STDERR, $label.' '.$path."\n");
    }
}
if ($added || $modified || $deleted) {
    fwrite(STDERR, "Review the diff, update relevant docs/CHANGELOG and regenerate inventory with --write.\n");
    exit(1);
}
echo 'Inventory matches: '.count($current)." controlled files.\n";
