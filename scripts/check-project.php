<?php

require __DIR__.'/project-files.php';

$root = dirname(__DIR__);
$failures = [];
foreach (controlledProjectFiles($root) as $path) {
    if (preg_match('/\.(md|json|php|js|css|yml|yaml)$/', $path) && ! mb_check_encoding(file_get_contents($root.'/'.$path), 'UTF-8')) {
        $failures[] = 'Invalid UTF-8: '.$path;
    }
    if (! str_ends_with($path, '.php') || str_ends_with($path, '.blade.php')) {
        continue;
    }
    $process = proc_open([PHP_BINARY, '-l', $root.'/'.$path], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    if (! is_resource($process)) {
        $failures[] = 'Could not lint: '.$path;

        continue;
    }
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    if (proc_close($process) !== 0) {
        $failures[] = 'PHP syntax: '.$path.' '.$stdout.$stderr;
    }
}

if (is_dir($root.'/.git')) {
    $process = proc_open(['git', 'ls-files', '-z'], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $root);
    $tracked = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    if (proc_close($process) !== 0) {
        $failures[] = 'Could not inspect tracked paths: '.$stderr;
    }
    foreach (explode("\0", $tracked) as $path) {
        if ($path === '') {
            continue;
        }
        if (($path === '.env' || str_starts_with($path, '.env.')) && $path !== '.env.example') {
            $failures[] = 'Tracked environment file: '.$path;
        }
        if (str_starts_with($path, 'vendor/') || str_starts_with($path, 'node_modules/') || str_ends_with($path, '.sqlite') || preg_match('/(^|\/)(auth\.json|id_rsa|id_ed25519)$/', $path)) {
            $failures[] = 'Tracked local dependency/data/credential file: '.$path;
        }
    }
}
if ($failures) {
    fwrite(STDERR, implode("\n", $failures)."\n");
    exit(1);
}
echo "Source encoding, PHP syntax and tracked-path checks passed.\n";
