<?php
// Create cache directory if it doesn't exist
$cacheDir = __DIR__ . '../cache';
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}
?>      