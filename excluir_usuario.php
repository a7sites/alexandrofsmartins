<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

$usuario = $_GET['usuario'] ?? '';
$usuario_logado = $_SESSION['usuario'] ?? '';

if ($usuario === $usuario_logado) {
   header('Location: visualizar_usuarios.php?error=Você não pode excluir o usuário atualmente logado!');
   exit;
}

$arquivo_usuarios = 'usuarios.json';
$usuarios = file_exists($arquivo_usuarios) ? json_decode(file_get_contents($arquivo_usuarios), true) : [];

if (!isset($usuarios[$usuario])) {
   header('Location: visualizar_usuarios.php?error=Usuário não encontrado');
   exit;
}

unset($usuarios[$usuario]);
file_put_contents($arquivo_usuarios, json_encode($usuarios, JSON_PRETTY_PRINT));
header('Location: visualizar_usuarios.php?success=Usuário excluído com sucesso!');
exit;
