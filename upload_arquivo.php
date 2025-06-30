<?php
header('Content-Type: application/json');
$base = __DIR__ . '/uploads';
$year = date('Y');
$month = date('m');
$targetDir = "$base/$year/$month";
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
$allowed = ['svg', 'webp', 'png', 'jpg', 'jpeg', 'gif'];
$results = [];
foreach ($_FILES['files']['tmp_name'] as $i => $tmp) {
   $name = $_FILES['files']['name'][$i];
   $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
   if (!in_array($ext, $allowed)) continue;
   $newName = uniqid('file_', true) . '.' . $ext;
   $dest = "$targetDir/$newName";
   if (move_uploaded_file($tmp, $dest)) {
      $results[] = [
         'name' => $newName,
         'url' => "uploads/$year/$month/$newName"
      ];
   }
}
echo json_encode(['success' => true, 'files' => $results]);
