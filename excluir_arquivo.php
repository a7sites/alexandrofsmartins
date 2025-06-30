<?php
header('Content-Type: application/json');
$base = __DIR__ . '/uploads/';
$path = $_POST['path'] ?? '';
$file = realpath($base . ltrim($path, '/'));
if ($file && strpos($file, realpath($base)) === 0 && is_file($file)) {
   unlink($file);
   echo json_encode(['success' => true]);
} else {
   echo json_encode(['success' => false, 'error' => 'Arquivo não encontrado']);
}
