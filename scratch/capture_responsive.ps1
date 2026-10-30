$chrome = 'C:\Program Files\Google\Chrome\Application\chrome.exe'
$outDir = 'C:\Users\home\.gemini\antigravity\brain\f0fed872-b1d8-463d-8924-3f5e93a8f5eb\screenshots'
if (-not (Test-Path $outDir)) { New-Item -ItemType Directory -Force -Path $outDir }

$pages = @(
    @{ name = 'home'; url = 'http://127.0.0.1:8000/' },
    @{ name = 'category'; url = 'http://127.0.0.1:8000/categories/cables-and-adapters' },
    @{ name = 'brand'; url = 'http://127.0.0.1:8000/brands/baseus' },
    @{ name = 'product'; url = 'http://127.0.0.1:8000/products/baseus-gan5-pro-100w-dual-type-c-usb-fast-charger-with-100w-cable-model-c81e-00002' },
    @{ name = 'checkout'; url = 'http://127.0.0.1:8000/checkout' }
)

$viewports = @(
    @{ width = 320; height = 700 },
    @{ width = 360; height = 740 },
    @{ width = 390; height = 844 },
    @{ width = 412; height = 915 },
    @{ width = 768; height = 1024 }
)

foreach ($p in $pages) {
    foreach ($v in $viewports) {
        $outFile = Join-Path $outDir ("$($p.name)_$($v.width)px.png")
        $tmpProfile = Join-Path $env:TEMP ("mta_chr_" + $p.name + "_" + $v.width + "_" + [Guid]::NewGuid().ToString().Substring(0,8))
        
        $args = @(
            '--headless',
            '--disable-gpu',
            '--hide-scrollbars',
            '--virtual-time-budget=1000',
            "--window-size=$($v.width),$($v.height)",
            "--user-data-dir=$tmpProfile",
            "--screenshot=$outFile",
            $p.url
        )
        
        Write-Host "Capturing $($p.name) at $($v.width)px -> $outFile"
        $proc = Start-Process -FilePath $chrome -ArgumentList $args -PassThru -NoNewWindow
        $proc.WaitForExit(10000)
        
        Start-Sleep -Milliseconds 150
        if (Test-Path $tmpProfile) {
            Remove-Item -Path $tmpProfile -Recurse -Force -ErrorAction SilentlyContinue
        }
    }
}

Write-Host "All 25 responsive captures completed successfully."
