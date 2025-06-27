<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

$usuario = $_POST['usuario'] ?? '';
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$nova_senha = $_POST['senha'] ?? '';

$arquivo_usuarios = 'usuarios.json';
$usuarios = file_exists($arquivo_usuarios) ? json_decode(file_get_contents($arquivo_usuarios), true) : [];

if (!isset($usuarios[$usuario])) {
   header('Location: visualizar_usuarios.php?error=Usuário não encontrado');
   exit;
}

// Validações
if (empty($nome) || empty($email)) {
   header('Location: editar_usuario.php?usuario=' . urlencode($usuario) . '&error=Preencha todos os campos obrigatórios');
   exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
   header('Location: editar_usuario.php?usuario=' . urlencode($usuario) . '&error=E-mail inválido');
   exit;
}
// Não permitir e-mail duplicado
foreach ($usuarios as $user => $dados) {
   if ($user !== $usuario && isset($dados['email']) && $dados['email'] === $email) {
      header('Location: editar_usuario.php?usuario=' . urlencode($usuario) . '&error=E-mail já cadastrado para outro usuário');
      exit;
   }
}

$usuarios[$usuario]['nome'] = $nome;
$usuarios[$usuario]['email'] = $email;

if (!empty($nova_senha)) {
   if (strlen($nova_senha) < 6 || !preg_match('/[A-Z]/', $nova_senha) || !preg_match('/[0-9]/', $nova_senha)) {
      header('Location: editar_usuario.php?usuario=' . urlencode($usuario) . '&error=Senha deve ter pelo menos 6 caracteres, uma letra maiúscula e um número');
      exit;
   }
   $usuarios[$usuario]['senha'] = password_hash($nova_senha, PASSWORD_DEFAULT);
}

file_put_contents($arquivo_usuarios, json_encode($usuarios, JSON_PRETTY_PRINT));
header('Location: editar_usuario.php?usuario=' . urlencode($usuario) . '&success=Usuário atualizado com sucesso!');
exit;
