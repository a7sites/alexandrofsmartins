<?php
header('Content-Type: application/json');
$base = __DIR__ . '/uploads/';
$path = $_POST['path'] ?? '';
$newName = $_POST['newName'] ?? '';
$file = realpath($base . ltrim($path, '/'));
$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
if ($file && strpos($file, realpath($base)) === 0 && is_file($file) && $newName) {
   $dir = dirname($file);
   $newPath = "$dir/$newName";
   if (strtolower(pathinfo($newPath, PATHINFO_EXTENSION)) !== $ext) {
      $newPath .= ".$ext";
   }
   rename($file, $newPath);
   echo json_encode(['success' => true]);
} else {
   echo json_encode(['success' => false, 'error' => 'Arquivo não encontrado ou nome inválido']);
}
