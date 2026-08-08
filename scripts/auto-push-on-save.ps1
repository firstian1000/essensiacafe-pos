param(
    [int] $DebounceSeconds = 5,
    [string] $Remote = "origin"
)

$ErrorActionPreference = "Stop"
$repo = (git rev-parse --show-toplevel).Trim()
Set-Location $repo

$branch = (git rev-parse --abbrev-ref HEAD).Trim()
if ($branch -eq "HEAD") {
    Write-Host "Auto-push skipped: detached HEAD."
    exit 1
}

$lastChange = Get-Date
$hasChange = $false
$isPushing = $false

function Invoke-AutoPush {
    if ($script:isPushing) {
        return
    }

    $script:isPushing = $true
    try {
        Set-Location $script:repo

        $status = git status --porcelain
        if (-not $status) {
            return
        }

        git add -A

        $staged = git diff --cached --name-only
        if (-not $staged) {
            return
        }

        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        git commit -m "Auto save: $timestamp"
        git push $script:Remote $script:branch
    }
    catch {
        Write-Host "Auto-push failed: $($_.Exception.Message)"
    }
    finally {
        $script:isPushing = $false
    }
}

$watcher = New-Object System.IO.FileSystemWatcher
$watcher.Path = $repo
$watcher.IncludeSubdirectories = $true
$watcher.EnableRaisingEvents = $true

$ignoredPathParts = @(
    "\.git\",
    "\node_modules\",
    "\vendor\",
    "\storage\framework\",
    "\storage\logs\",
    "\public\build\"
)

$onChange = {
    $path = $Event.SourceEventArgs.FullPath

    foreach ($part in $script:ignoredPathParts) {
        if ($path -like "*$part*") {
            return
        }
    }

    $script:lastChange = Get-Date
    $script:hasChange = $true
}

$events = @(
    Register-ObjectEvent $watcher Changed -Action $onChange
    Register-ObjectEvent $watcher Created -Action $onChange
    Register-ObjectEvent $watcher Deleted -Action $onChange
    Register-ObjectEvent $watcher Renamed -Action $onChange
)

Write-Host "Watching $repo"
Write-Host "Auto-commit and push to $Remote/$branch after $DebounceSeconds seconds of no file changes."
Write-Host "Press Ctrl+C to stop."

try {
    while ($true) {
        Start-Sleep -Seconds 1

        if ($hasChange -and ((Get-Date) - $lastChange).TotalSeconds -ge $DebounceSeconds) {
            $hasChange = $false
            Invoke-AutoPush
        }
    }
}
finally {
    foreach ($event in $events) {
        Unregister-Event -SubscriptionId $event.Id -ErrorAction SilentlyContinue
    }

    $watcher.Dispose()
}
