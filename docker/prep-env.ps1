# Mengisi secret produksi di .env bila masih placeholder/kosong (dipakai run.bat deploy).
# Output (stdout) baris terakhir = password admin yang dipakai bila baru digenerate, selain itu kosong.
param([string]$EnvFile = ".env")

function New-Secret([int]$Len = 32) {
    $bytes = New-Object byte[] $Len
    [System.Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($bytes)
    return ([System.BitConverter]::ToString($bytes) -replace '-', '').ToLower().Substring(0, $Len)
}

if (-not (Test-Path $EnvFile)) { Write-Error "$EnvFile tidak ada"; exit 1 }
$content = Get-Content $EnvFile -Raw

function Set-IfPlaceholder([string]$key, [string]$value, [bool]$treatPasswordLiteral = $false) {
    $pattern = "(?m)^$key=(.*)$"
    $m = [regex]::Match($script:content, $pattern)
    $cur = if ($m.Success) { $m.Groups[1].Value.Trim() } else { "" }
    $isPlaceholder = ($cur -eq "") -or ($cur -like "__*")
    if ($treatPasswordLiteral -and $cur -eq "password") { $isPlaceholder = $true }
    if ($isPlaceholder) {
        if ($m.Success) {
            $script:content = [regex]::Replace($script:content, $pattern, "$key=$value")
        } else {
            $script:content = $script:content.TrimEnd() + "`n$key=$value`n"
        }
        return $true
    }
    return $false
}

[void](Set-IfPlaceholder "DB_PASSWORD" (New-Secret))
[void](Set-IfPlaceholder "DB_ROOT_PASSWORD" (New-Secret))

$adminPw = New-Secret 18
$adminChanged = Set-IfPlaceholder "ADMIN_PASSWORD" $adminPw $true

Set-Content -Path $EnvFile -Value $content -NoNewline -Encoding utf8

if ($adminChanged) { Write-Output $adminPw } else { Write-Output "" }
