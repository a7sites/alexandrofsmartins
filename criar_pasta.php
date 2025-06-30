<?php
header('Content-Type: application/json');
$base = __DIR__ . '/uploads/' . date('Y') . '/' . date('m') . '/';
$folder = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['folder'] ?? '');
if ($folder && !is_dir($base . $folder)) {
   mkdir($base . $folder, 0777, true);
   echo json_encode(['success' => true]);
} else {
   echo json_encode(['success' => false, 'error' => 'Nome inválido ou pasta já existe']);
}
