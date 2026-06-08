<?php
$base = 'http://127.0.0.1:8765';
$jar = __DIR__ . '/cookies.txt';

// Login
$ch = curl_init("$base/login");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $jar,
    CURLOPT_COOKIEFILE => $jar,
]);
$html = curl_exec($ch);
curl_close($ch);
preg_match('/name="_token"\s+value="([^"]+)"/', $html, $m);
$csrf = $m[1];

$ch = curl_init("$base/login");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query(['_token' => $csrf, 'email' => 'admin@test.com', 'password' => 'password']),
    CURLOPT_COOKIEJAR => $jar,
    CURLOPT_COOKIEFILE => $jar,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_HEADER => true,
]);
$resp = curl_exec($ch);
curl_close($ch);
echo "Login response:\n$resp\n\n";

function fetch($url, $jar) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR => $jar,
        CURLOPT_COOKIEFILE => $jar,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $body = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return ['body' => $body, 'code' => $info['http_code']];
}

foreach (['/productos', '/productos/create', '/productos/pdf', '/productos/import', '/productos/exportar'] as $path) {
    $r = fetch($base . $path, $jar);
    $placeholder = preg_match_all('/producto-placeholder\.svg/', $r['body']);
    $storageImg = preg_match_all('/storage\/img\/productos/', $r['body']);
    $imgs = preg_match_all('/<img[^>]*src="([^"]+)"/', $r['body'], $mm);
    $srcs = array_slice($mm[1], 0, 3);
    echo "GET $path -> HTTP {$r['code']} | imgs={$imgs} placeholder={$placeholder} storage={$storageImg}\n";
    foreach ($srcs as $s) {
        echo "    src: $s\n";
    }
}

$ph = fetch("$base/img/producto-placeholder.svg", $jar);
echo "\nPlaceholder: HTTP {$ph['code']}, size=" . strlen($ph['body']) . " bytes\n";

unlink($jar);
