<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   http_response_code(401);
   exit('Não autorizado');
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   http_response_code(405);
   exit('Método não permitido');
}

// Validar dados recebidos
$whatsapp_antigo = preg_replace('/[^0-9]/', '', $_POST['whatsapp_antigo'] ?? '');
$nome = trim($_POST['nome'] ?? '');
$whatsapp = preg_replace('/[^0-9]/', '', $_POST['whatsapp'] ?? '');

if (empty($whatsapp_antigo) || empty($nome) || empty($whatsapp)) {
   http_response_code(400);
   header('Content-Type: application/json');
   echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
   exit;
}

// Verificar se o WhatsApp tem pelo menos 10 dígitos
if (strlen($whatsapp) < 10) {
   http_response_code(400);
   header('Content-Type: application/json');
   echo json_encode(['success' => false, 'message' => 'WhatsApp inválido']);
   exit;
}

// Carregar contatos existentes
$contatos_file = 'whatsapp.json';
$contatos = [];
if (file_exists($contatos_file)) {
   $contatos = json_decode(file_get_contents($contatos_file), true) ?? [];
}

// Buscar contato pelo whatsapp_antigo
$encontrado = false;
foreach ($contatos as $i => $contato) {
   if (isset($contato['whatsapp']) && preg_replace('/[^0-9]/', '', $contato['whatsapp']) === $whatsapp_antigo) {
      $encontrado = $i;
      break;
   }
}
if ($encontrado === false) {
   http_response_code(404);
   header('Content-Type: application/json');
   echo json_encode(['success' => false, 'message' => 'Contato não encontrado']);
   exit;
}

// Verificar se o novo WhatsApp já existe em outro contato
foreach ($contatos as $i => $contato) {
   if ($i !== $encontrado && isset($contato['whatsapp']) && preg_replace('/[^0-9]/', '', $contato['whatsapp']) === $whatsapp) {
      http_response_code(409);
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'message' => 'Já existe um contato com este WhatsApp']);
      exit;
   }
}

// Atualizar o contato
$contatos[$encontrado]['nome'] = $nome;
$contatos[$encontrado]['whatsapp'] = $whatsapp;
$contatos[$encontrado]['data_hora'] = date('Y-m-d H:i:s'); // Atualizar data/hora da edição

// Salvar contatos atualizados
if (file_put_contents($contatos_file, json_encode($contatos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
   header('Content-Type: application/json');
   echo json_encode([
      'success' => true,
      'message' => 'Contato atualizado com sucesso!',
      'contato_atualizado' => $contatos[$encontrado]
   ]);
} else {
   http_response_code(500);
   header('Content-Type: application/json');
   echo json_encode(['success' => false, 'message' => 'Erro ao atualizar contato']);
   exit;
}
