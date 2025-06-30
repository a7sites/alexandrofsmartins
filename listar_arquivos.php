<?php
header('Content-Type: application/json');
$base = __DIR__ . '/uploads';
$search = $_GET['search'] ?? '';
$date = $_GET['date'] ?? '';
$sort = $_GET['sort'] ?? 'date';
$allowed = ['svg', 'webp', 'png', 'jpg', 'jpeg', 'gif'];
$files = [];
function scan($dir)
{
   global $allowed, $files, $base, $search, $date;
   foreach (scandir($dir) as $f) {
      if ($f === '.' || $f === '..') continue;
      $path = "$dir/$f";
      if (is_dir($path)) scan($path);
      else {
         $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
         if (!in_array($ext, $allowed)) continue;
         $stat = stat($path);
         $rel = str_replace($base, 'uploads', $path);
         $fileDate = date('Y-m', $stat['mtime']);
         if ($search && stripos($f, $search) === false) continue;
         if ($date && strpos($fileDate, $date) !== 0) continue;
         $files[] = [
            'name' => $f,
            'url' => $rel,
            'thumb' => $rel,
            'type' => $ext,
            'size' => round($stat['size'] / 1024, 1) . ' KB',
            'date' => date('d/m/Y H:i', $stat['mtime']),
            'path' => $rel
         ];
      }
   }
}
if (is_dir($base)) scan($base);
// Ordenação
usort($files, function ($a, $b) use ($sort) {
   if ($sort === 'name') return strcmp($a['name'], $b['name']);
   if ($sort === 'size') return (float)$b['size'] <=> (float)$a['size'];
   return strtotime(str_replace('/', '-', $b['date'])) <=> strtotime(str_replace('/', '-', $a['date']));
});
echo json_encode($files);
