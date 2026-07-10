param(
    [Parameter(Mandatory=$true)][string]$Url,
    [string]$SiteName,
    [string]$WpUrl = "http://opulentia.local/wp-admin/admin-ajax.php",
    [string]$Nonce,
    [switch]$Apply
)

if (-not $SiteName) {
    $SiteName = $Url -replace 'https?://','' -replace '/','-' -replace '\..*',''
}

$outDir = "output/$SiteName"
New-Item -ItemType Directory -Force -Path $outDir | Out-Null

Write-Host "=== Dembrandt -> Opulentia Pipeline ===" -ForegroundColor Cyan
Write-Host "URL: $Url"
Write-Host "Output: $outDir"
Write-Host ""

# Step 1: Extract with Dembrandt
Write-Host "[1/3] Extracting design tokens with Dembrandt..." -ForegroundColor Yellow
$env:DEMBRANDT_NO_SANDBOX = "1"
$jsonOutput = & "C:\Users\hamma\AppData\Roaming\npm\dembrandt.cmd" --json-only --no-sandbox $Url 2>$null
$jsonOutput | Set-Content -Path "$outDir/dembrandt.json"
Write-Host "  Saved to $outDir/dembrandt.json" -ForegroundColor Green

# Also get DESIGN.md
& "C:\Users\hamma\AppData\Roaming\npm\dembrandt.cmd" --design-md --no-sandbox $Url 2>$null | Set-Content -Path "$outDir/DESIGN.md"
Write-Host "  Saved to $outDir/DESIGN.md" -ForegroundColor Green

# Step 2: Send to WordPress (if nonce provided)
if ($Nonce) {
    Write-Host "[2/3] Importing into Opulentia Cloner..." -ForegroundColor Yellow
    $body = @{
        action = "opulentia_cloner_dembrandt"
        dembrandt_json = $jsonOutput
        nonce = $Nonce
    }
    $response = Invoke-RestMethod -Uri $WpUrl -Method Post -Body $body
    if ($response.success) {
        Write-Host "  Import successful!" -ForegroundColor Green
        $response.data.theme_mods | ConvertTo-Json -Depth 5 | Set-Content -Path "$outDir/theme_mods.json"

        # Step 3: Apply (if flag set)
        if ($Apply) {
            Write-Host "[3/3] Applying design..." -ForegroundColor Yellow
            $applyBody = @{
                action = "opulentia_cloner_apply"
                nonce = $Nonce
            }
            $applyResponse = Invoke-RestMethod -Uri $WpUrl -Method Post -Body $applyBody
            if ($applyResponse.success) {
                Write-Host "  Design applied!" -ForegroundColor Green
            } else {
                Write-Host "  Apply failed: $($applyResponse.data.message)" -ForegroundColor Red
            }
        } else {
            Write-Host "[3/3] Skipped. Pass -Apply to auto-apply." -ForegroundColor Gray
        }
    } else {
        Write-Host "  Import failed: $($response.data.message)" -ForegroundColor Red
    }
} else {
    Write-Host "[2/3] Skipped (no nonce). Open WP Admin -> Opulentia -> Site Cloner -> paste dembrandt.json" -ForegroundColor Gray
}

Write-Host ""
Write-Host "=== Pipeline complete ===" -ForegroundColor Cyan
Write-Host "Output files in $outDir"
