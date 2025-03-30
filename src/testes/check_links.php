<?php

function checkLinks($baseUrl, $paths) {
    foreach ($paths as $path) {
        $url = rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
        $status = getHttpStatus($url);
        
        echo "URL: $url - Status: $status\n";
        
        if ($status >= 400) {
            echo "[ALERTA] Link quebrado: $url\n";
        }
    }
}

function getHttpStatus($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $status;
}

$baseUrl = "http://localhost:8080/"; 
$paths = [
    "index.html",
    "src/server/index.php",
    "src/testes/ProductProcessorTest.php",
    "src/services/script.js",
    "src/services/styles.css",
    "src/App/paginacliente.html",
    "src/App/paginacliente.css"
];

checkLinks($baseUrl, $paths);