param([int]$Port = 8020)
$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$phpCommand = Get-Command php -ErrorAction SilentlyContinue
$php = if ($phpCommand) { $phpCommand.Source } else { 'C:/VoMinhHoang/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe' }
if (!(Test-Path -LiteralPath $php)) { throw 'PHP 8.2+ must be available in PATH.' }
if (!(Test-Path -LiteralPath "$projectRoot/vendor/autoload.php")) { throw 'Run composer install first.' }
if (!(Test-Path -LiteralPath "$projectRoot/public/build/manifest.json")) { throw 'Run npm ci and npm run build first.' }
if ($Port -lt 1024 -or $Port -gt 65535) { throw 'Use a port between 1024 and 65535.' }
$demoDatabase = Join-Path $projectRoot 'database/demo-progress-20261020.sqlite'
$variables = @('APP_ENV','APP_DEBUG','APP_KEY','APP_URL','DB_CONNECTION','DB_DATABASE','SESSION_DRIVER','CACHE_STORE','QUEUE_CONNECTION','DEMO_PASSWORD','APP_CONFIG_CACHE')
$previous = @{}
foreach ($name in $variables) { $previous[$name] = [Environment]::GetEnvironmentVariable($name, 'Process') }
Push-Location $projectRoot
try {
    if (!$env:DEMO_PASSWORD) {
        $secret = Read-Host 'Local demo password (8+ characters; existing users keep their password)' -AsSecureString
        $env:DEMO_PASSWORD = [System.Net.NetworkCredential]::new('', $secret).Password
    }
    if ($env:DEMO_PASSWORD.Length -lt 8) { throw 'Demo password must have at least 8 characters.' }
    $env:APP_ENV = 'local'
    $env:APP_DEBUG = 'false'
    # New key per start deliberately expires previous demo sessions; no .env edits.
    $keyBytes = New-Object byte[] 32
    $random = [Security.Cryptography.RandomNumberGenerator]::Create()
    try { $random.GetBytes($keyBytes) } finally { $random.Dispose() }
    $env:APP_KEY = 'base64:' + [Convert]::ToBase64String($keyBytes)
    $env:APP_URL = "http://127.0.0.1:$Port"
    $env:DB_CONNECTION = 'sqlite'
    $env:DB_DATABASE = $demoDatabase
    $env:SESSION_DRIVER = 'database'
    $env:CACHE_STORE = 'database'
    $env:QUEUE_CONNECTION = 'database'
    # Ignore a pre-existing cached configuration without clearing another setup's cache.
    $env:APP_CONFIG_CACHE = Join-Path $projectRoot 'bootstrap/cache/progress-demo-unused.php'
    if (Test-Path -LiteralPath $env:APP_CONFIG_CACHE) { throw 'Unexpected demo configuration cache; inspect before continuing.' }
    if (!(Test-Path -LiteralPath $demoDatabase)) { New-Item -ItemType File -Path $demoDatabase | Out-Null }
    & $php artisan migrate --seed --no-interaction
    if ($LASTEXITCODE -ne 0) { throw 'Demo database preparation failed.' }
    Write-Host "Demo: $env:APP_URL | admin@example.test | dedicated SQLite only; Ctrl+C stops server."
    & $php artisan serve --host=127.0.0.1 --port=$Port --no-reload
    if ($LASTEXITCODE -ne 0) { throw 'Demo server stopped with an error.' }
} finally {
    foreach ($name in $variables) { [Environment]::SetEnvironmentVariable($name, $previous[$name], 'Process') }
    Pop-Location
}
