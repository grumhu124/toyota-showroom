param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('Status', 'Migrate', 'Test', 'Backup', 'Restore')]
    [string]$Action,
    [string]$BackupFile
)
$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$localDirectory = Join-Path $projectRoot '.setup-cache/mysql-local'
$configurationFile = Join-Path $localDirectory 'local.json'
if (!(Test-Path -LiteralPath $configurationFile)) { throw 'Prepare local MySQL as described in docs/development/MYSQL_LOCAL.md first.' }
$local = Get-Content -LiteralPath $configurationFile -Raw | ConvertFrom-Json
if ($local.host -ne '127.0.0.1' -or $local.port -ne 3307) { throw 'This helper only targets the isolated local instance on 127.0.0.1:3307.' }
$mysql = Join-Path $local.mysql_bin 'mysql.exe'
$dump = Join-Path $local.mysql_bin 'mysqldump.exe'
$role = if ($Action -eq 'Test') { 'test' } elseif ($Action -eq 'Restore') { 'restore' } else { 'dev' }
$database = 'toyota_showroom_' + $role
$clientFile = Join-Path $localDirectory ($role + '.cnf')
if (!(Test-Path -LiteralPath $clientFile)) { throw 'Missing local client configuration.' }
Push-Location $projectRoot
try {
    $actual = & $mysql "--defaults-file=$clientFile" --default-character-set=utf8mb4 --batch --raw --skip-column-names "--database=$database" --execute='SELECT DATABASE(), CURRENT_USER(), VERSION(), @@port, @@datadir;'
    if ($LASTEXITCODE -ne 0) { throw 'Cannot connect to the isolated database.' }
    $identity = ([string]$actual).Split("`t")
    $expectedDataDirectory = ([IO.Path]::GetFullPath((Join-Path $localDirectory 'data'))).Replace('\', '/').TrimEnd('/')
    if ($identity.Length -ne 5 -or $identity[0] -ne $database -or $identity[1] -ne ('toyota_' + $role + '@127.0.0.1') -or $identity[3] -ne '3307' -or $identity[4].Replace('\', '/').TrimEnd('/') -ne $expectedDataDirectory) {
        throw 'Unexpected database/user/server identity. Refusing to migrate, test or import.'
    }
    Write-Host $actual
    if ($Action -eq 'Status') { return }
    if ($Action -eq 'Backup') {
        $backupDirectory = Join-Path $localDirectory 'backups'
        New-Item -ItemType Directory -Force -Path $backupDirectory | Out-Null
        if (!$BackupFile) { $BackupFile = Join-Path $backupDirectory ('dev-' + (Get-Date -Format 'yyyyMMdd-HHmmss') + '.sql') }
        $destination = [IO.Path]::GetFullPath($BackupFile)
        if (Test-Path -LiteralPath $destination) { throw 'Refusing to overwrite an existing backup.' }
        & $dump "--defaults-file=$clientFile" --single-transaction --quick --no-tablespaces --set-gtid-purged=OFF --skip-add-locks --default-character-set=utf8mb4 "--result-file=$destination" $database
        if ($LASTEXITCODE -ne 0) { throw 'Backup failed; do not use the partial output.' }
        if ((Get-Item -LiteralPath $destination).Length -eq 0) { throw 'Backup output is empty.' }
        Write-Host "Backup written: $destination"
        Get-FileHash -LiteralPath $destination -Algorithm SHA256
        return
    }
    if ($Action -eq 'Restore') {
        if (!$BackupFile) { throw 'Provide -BackupFile pointing to a trusted SQL backup.' }
        $source = (Resolve-Path -LiteralPath $BackupFile).Path
        $tables = & $mysql "--defaults-file=$clientFile" --default-character-set=utf8mb4 --batch --skip-column-names --execute="SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'toyota_showroom_restore';"
        if ($LASTEXITCODE -ne 0 -or [int]$tables -ne 0) { throw 'Restore target must be the EMPTY toyota_showroom_restore database. No tables are dropped by this helper.' }
        $process = Start-Process -FilePath $mysql -ArgumentList @("`"--defaults-file=$clientFile`"", '--default-character-set=utf8mb4', '--database=toyota_showroom_restore') -RedirectStandardInput $source -WindowStyle Hidden -Wait -PassThru
        if ($process.ExitCode -ne 0) { throw 'Restore failed; inspect the separate restore database. The development database was not targeted.' }
        Write-Host 'Restore import completed. Verify table counts and representative records before accepting the backup.'
        return
    }
    $phpCommand = Get-Command php -ErrorAction SilentlyContinue
    $php = if ($phpCommand) { $phpCommand.Source } else { 'C:/VoMinhHoang/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe' }
    $values = @{
        APP_ENV = $(if ($Action -eq 'Test') { 'testing' } else { 'local' })
        APP_CONFIG_CACHE = (Join-Path $projectRoot 'bootstrap/cache/mysql-local-unused.php')
        DB_CONNECTION = 'mysql'; DB_HOST = '127.0.0.1'; DB_PORT = '3307'
        DB_DATABASE = $database; DB_USERNAME = ('toyota_' + $role)
        DB_PASSWORD = $local.passwords.$role; DB_URL = ''
        DEMO_PASSWORD = $local.passwords.demo_password
    }
    if (Test-Path -LiteralPath $values.APP_CONFIG_CACHE) { throw 'Unexpected configuration cache at the isolated helper path.' }
    $old = @{}
    foreach ($name in $values.Keys) {
        $old[$name] = [Environment]::GetEnvironmentVariable($name, 'Process')
        [Environment]::SetEnvironmentVariable($name, $values[$name], 'Process')
    }
    try {
        if ($Action -eq 'Test') { & $php artisan test } else { & $php artisan migrate --seed --no-interaction }
        if ($LASTEXITCODE -ne 0) { throw 'MySQL migration/test command failed.' }
    } finally {
        foreach ($name in $old.Keys) { [Environment]::SetEnvironmentVariable($name, $old[$name], 'Process') }
    }
} finally { Pop-Location }
