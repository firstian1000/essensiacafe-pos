<?php

// 1. Buat direktori sementara (tmp) yang diwajibkan oleh Laravel
$directories = [
    '/tmp/bootstrap/cache',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 2. Teruskan request ke file public/index.php milik Laravel
require __DIR__ . '/../public/index.php';