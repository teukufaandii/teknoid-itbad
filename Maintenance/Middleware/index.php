<?php
include __DIR__ . '/../config.php';

$allowed_ips = [
    '192.168.100.7',
    '192.168.100.1',
    '127.0.0.1',
    '::1'
];

if (IS_MAINTENANCE && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips) && basename($_SERVER['PHP_SELF']) != 'maintenance.php') {
    header('Location: /teknoid-itbad/maintenance.php');
    exit;
}
