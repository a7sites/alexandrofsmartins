<?php

/**
 * Salvar Páginas do Editor Visual
 */

session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Content-Type: application/json');
   echo json_encode(['success' => false, 'error' => 'Não autorizado']);
   exit;
}

// Receber dados JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
   header('Content-Type: application/json');
   echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
   exit;
}

$page_id = $data['page_id'] ?? 'new_page';
$page_data = $data['data'] ?? [];

// Criar diretório de páginas se não existir
$pages_dir = 'pages';
if (!is_dir($pages_dir)) {
   mkdir($pages_dir, 0755, true);
}

// Gerar ID único se for nova página
if ($page_id === 'new_page') {
   $page_id = 'page_' . time() . '_' . rand(1000, 9999);
}

// Salvar arquivo da página
$page_file = $pages_dir . '/' . $page_id . '.json';
$success = file_put_contents($page_file, json_encode($page_data, JSON_PRETTY_PRINT));

if ($success) {
   // Salvar metadados da página
   $metadata = [
      'id' => $page_id,
      'title' => $page_data['title'] ?? 'Página sem título',
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
      'elements_count' => count($page_data['elements'] ?? [])
   ];

   $metadata_file = $pages_dir . '/' . $page_id . '_meta.json';
   file_put_contents($metadata_file, json_encode($metadata, JSON_PRETTY_PRINT));

   header('Content-Type: application/json');
   echo json_encode([
      'success' => true,
      'page_id' => $page_id,
      'message' => 'Página salva com sucesso!'
   ]);
} else {
   header('Content-Type: application/json');
   echo json_encode(['success' => false, 'error' => 'Erro ao salvar página']);
}
