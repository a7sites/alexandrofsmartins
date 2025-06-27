<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   header('Location: cadastrar.php');
   exit;
}

// Função para validar senha
function validarSenha($senha)
{
   if (strlen($senha) < 6) {
      return false;
   }

   if (!preg_match('/[A-Z]/', $senha)) {
      return false;
   }

   if (!preg_match('/[0-9]/', $senha)) {
      return false;
   }

   return true;
}

// Função para validar e-mail
function validarEmail($email)
{
   return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Captura e valida os dados
$usuario = trim($_POST['usuario'] ?? '');
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

// Validações
$erros = [];

if (empty($usuario)) {
   $erros[] = 'O campo usuário é obrigatório';
}

if (empty($nome)) {
   $erros[] = 'O campo nome é obrigatório';
}

if (empty($email)) {
   $erros[] = 'O campo e-mail é obrigatório';
} elseif (!validarEmail($email)) {
   $erros[] = 'E-mail inválido';
}

if (empty($senha)) {
   $erros[] = 'O campo senha é obrigatório';
} elseif (!validarSenha($senha)) {
   $erros[] = 'A senha deve ter pelo menos 6 caracteres, uma letra maiúscula e um número';
}

if ($senha !== $confirmar_senha) {
   $erros[] = 'As senhas não coincidem';
}

// Se há erros, redireciona com as mensagens
if (!empty($erros)) {
   $erro_msg = implode(', ', $erros);
   header('Location: cadastrar.php?error=' . urlencode($erro_msg));
   exit;
}

// Carrega o arquivo de usuários
$arquivo_usuarios = 'usuarios.json';

if (!file_exists($arquivo_usuarios)) {
   $usuarios = [];
} else {
   $usuarios = json_decode(file_get_contents($arquivo_usuarios), true) ?? [];
}

// Verifica se o usuário já existe
if (isset($usuarios[$usuario])) {
   header('Location: cadastrar.php?error=Este usuário já existe');
   exit;
}

// Verifica se o e-mail já existe
foreach ($usuarios as $user_data) {
   if (isset($user_data['email']) && $user_data['email'] === $email) {
      header('Location: cadastrar.php?error=Este e-mail já está cadastrado');
      exit;
   }
}

// Cria o hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Adiciona o novo usuário
$usuarios[$usuario] = [
   'senha' => $senha_hash,
   'nome' => $nome,
   'email' => $email,
   'data_criacao' => date('Y-m-d H:i:s'),
   'criado_por' => $_SESSION['usuario'] ?? 'admin'
];

// Salva no arquivo JSON
$resultado = file_put_contents($arquivo_usuarios, json_encode($usuarios, JSON_PRETTY_PRINT));

if ($resultado === false) {
   header('Location: cadastrar.php?error=Erro ao salvar usuário. Verifique as permissões do arquivo.');
   exit;
}

// Sucesso
header('Location: cadastrar.php?success=Usuário cadastrado com sucesso!');
exit;
