param([Parameter(Mandatory = $true)][string]$Destination)
$ErrorActionPreference = 'Stop'
$starterSource = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$starterDestination = [System.IO.Path]::GetFullPath($Destination)
if (Test-Path -LiteralPath $starterDestination) { throw 'Destination must be a new directory.' }
if ($starterDestination.StartsWith($starterSource + [System.IO.Path]::DirectorySeparatorChar, [System.StringComparison]::OrdinalIgnoreCase)) { throw 'Choose a destination outside the starter folder.' }
$starterExcluded = @('.git', '.env', '.env.backup', '.env.production', 'vendor', 'node_modules', '.phpunit.cache', '.phpunit.result.cache', 'auth.json', 'hot')
function Copy-StarterDirectory([string]$From, [string]$To) {
    New-Item -ItemType Directory -Path $To | Out-Null
    foreach ($starterItem in Get-ChildItem -LiteralPath $From -Force) {
        if ($starterExcluded -contains $starterItem.Name -or ($starterItem.Name.StartsWith('.env.') -and $starterItem.Name -ne '.env.example')) { continue }
        if ($starterItem.Name -match '\.(sqlite|db)(-wal|-shm|-journal)?$') { continue }
        $starterRelative = $starterItem.FullName.Substring($starterSource.Length).Replace('\', '/')
        if ($starterRelative -match '^/(public/build|public/storage|bootstrap/cache|bootstrap/ssr|storage)/') { continue }
        if ($starterRelative -in @('/public/build', '/public/storage', '/bootstrap/cache', '/bootstrap/ssr', '/storage')) { continue }
        $starterTarget = Join-Path $To $starterItem.Name
        if ($starterItem.PSIsContainer) { Copy-StarterDirectory $starterItem.FullName $starterTarget }
        else { Copy-Item -LiteralPath $starterItem.FullName -Destination $starterTarget }
    }
}
Copy-StarterDirectory $starterSource $starterDestination
foreach ($starterDirectory in @('bootstrap/cache', 'storage/app/private', 'storage/app/public', 'storage/framework/cache/data', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs')) {
    $starterPath = Join-Path $starterDestination $starterDirectory
    New-Item -ItemType Directory -Path $starterPath -Force | Out-Null
    Set-Content -LiteralPath (Join-Path $starterPath '.gitignore') -Value "*`n!.gitignore" -Encoding utf8
}
Write-Output "Clean starter exported to $starterDestination"
