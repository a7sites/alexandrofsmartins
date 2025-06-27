<?php
date_default_timezone_set('America/Sao_Paulo');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   http_response_code(405);
   exit('Método não permitido');
}

$nome = trim($_POST['nome'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

if (empty($nome) || empty($whatsapp)) {
   http_response_code(400);
   exit('Nome e WhatsApp são obrigatórios');
}

$dados = [
   'nome' => $nome,
   'whatsapp' => $whatsapp,
   'mensagem' => $mensagem,
   'data_hora' => date('Y-m-d H:i:s'),
   'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
   'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
];

$arquivo = 'whatsapp.json';
$registros = file_exists($arquivo) ? json_decode(file_get_contents($arquivo), true) : [];
$registros[] = $dados;
file_put_contents($arquivo, json_encode($registros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(['success' => true]);
