$ErrorActionPreference = 'Stop'
$junk = @('LPT1','USB002','VID_0000','localhost')

function SplitNul($file) {
    $bytes = [System.IO.File]::ReadAllBytes($file)
    $list = New-Object System.Collections.Generic.List[string]
    $start = 0
    for ($i=0; $i -lt $bytes.Length; $i++) {
        if ($bytes[$i] -eq 0) {
            if ($i -gt $start) {
                $s = [System.Text.Encoding]::UTF8.GetString($bytes, $start, $i - $start)
                $list.Add($s)
            }
            $start = $i + 1
        }
    }
    if ($start -lt $bytes.Length) {
        $s = [System.Text.Encoding]::UTF8.GetString($bytes, $start, $bytes.Length - $start)
        $list.Add($s)
    }
    return $list
}

function WriteNul($records, $file) {
    $ms = New-Object System.IO.MemoryStream
    for ($i=0; $i -lt $records.Count; $i++) {
        $b = [System.Text.Encoding]::UTF8.GetBytes($records[$i])
        $ms.Write($b,0,$b.Length)
        if ($i -lt $records.Count - 1) { $ms.WriteByte(0) }
    }
    [System.IO.File]::WriteAllBytes($file, $ms.ToArray())
}

function BuildCleanTree($commit) {
    $pubfile = [System.IO.Path]::GetTempFileName()
    git ls-tree -z $commit public > $pubfile 2>$null
    $pubRecs = SplitNul $pubfile
    Remove-Item $pubfile
    $keep = @()
    foreach ($rec in $pubRecs) {
        if ($rec -eq '') { continue }
        $idx = $rec.IndexOf("`t")
        $name = $rec.Substring($idx+1)
        $skip = $false
        foreach ($j in $junk) { if ($name -like "*$j*") { $skip = $true; break } }
        if (-not $skip) { $keep += $rec }
    }
    $pubTmp = [System.IO.Path]::GetTempFileName()
    WriteNul $keep $pubTmp
    $T_pub = (cmd /c "git mktree -z < $pubTmp").Trim()
    Remove-Item $pubTmp

    $rootfile = [System.IO.Path]::GetTempFileName()
    git ls-tree -z $commit > $rootfile 2>$null
    $rootRecs = SplitNul $rootfile
    Remove-Item $rootfile
    $rkeep = @()
    foreach ($rec in $rootRecs) {
        if ($rec -eq '') { continue }
        $idx = $rec.IndexOf("`t")
        $meta = $rec.Substring(0,$idx)
        $name = $rec.Substring($idx+1)
        if ($name -eq 'public') {
            $parts = $meta -split ' '
            $rkeep += "$($parts[0]) $($parts[1]) $T_pub`tpublic"
        } else { $rkeep += $rec }
    }
    $rootTmp = [System.IO.Path]::GetTempFileName()
    WriteNul $rkeep $rootTmp
    $T_root = (cmd /c "git mktree -z < $rootTmp").Trim()
    Remove-Item $rootTmp
    return $T_root
}

foreach ($c in @('b0a76e43e6509ec380338e90babf1918af1ebb20','726943bda0a8e1b4de156dcdc98f8dd8b3cde9d1','4f151a110d04eaeb2c63b08b33ff20d7f372d581')) {
    $parent = (git rev-parse "$c^").Trim()
    $msgfile = [System.IO.Path]::GetTempFileName()
    git log -1 --format=%B $c > $msgfile 2>$null
    $T = BuildCleanTree $c
    $newc = (git commit-tree $T -p $parent -F $msgfile).Trim()
    Remove-Item $msgfile
    git replace $c $newc
    Write-Output "Replaced $c -> $newc (tree $T)"
}
Write-Output "DONE"
