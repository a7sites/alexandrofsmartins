<?php
header('Content-Type: application/json');
session_start();

// Verifica se está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   http_response_code(401);
   echo json_encode(['success' => false, 'message' => 'Não autorizado']);
   exit;
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   http_response_code(405);
   echo json_encode(['success' => false, 'message' => 'Método não permitido']);
   exit;
}

$whatsapp = $_POST['whatsapp'] ?? '';
if (empty($whatsapp)) {
   http_response_code(400);
   echo json_encode(['success' => false, 'message' => 'WhatsApp não informado']);
   exit;
}

// Carregar contatos existentes
$contatos_file = 'whatsapp.json';
$contatos = [];
if (file_exists($contatos_file)) {
   $contatos = json_decode(file_get_contents($contatos_file), true) ?? [];
}

$encontrado = false;
foreach ($contatos as $i => $contato) {
   if ($contato['whatsapp'] === $whatsapp) {
      $contato_removido = $contatos[$i];
      array_splice($contatos, $i, 1);
      $encontrado = true;
      break;
   }
}

if (!$encontrado) {
   http_response_code(404);
   echo json_encode(['success' => false, 'message' => 'Contato não encontrado']);
   exit;
}

// Salvar contatos atualizados
if (file_put_contents($contatos_file, json_encode($contatos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
   echo json_encode([
      'success' => true,
      'message' => 'Contato excluído com sucesso!',
      'contato_removido' => $contato_removido
   ]);
} else {
   http_response_code(500);
   echo json_encode(['success' => false, 'message' => 'Erro ao excluir contato']);
}
 