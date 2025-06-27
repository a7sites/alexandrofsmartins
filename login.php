<?php
session_start();

// Verifica se já está logado
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
   header('Location: painel.php');
   exit;
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $usuario = trim($_POST['usuario'] ?? '');
   $senha = $_POST['senha'] ?? '';

   // Validações básicas
   if (empty($usuario) || empty($senha)) {
      header('Location: admin.php?error=Preencha todos os campos');
      exit;
   }

   // Carrega o arquivo de usuários
   $arquivo_usuarios = 'usuarios.json';

   if (!file_exists($arquivo_usuarios)) {
      // Cria o arquivo com um usuário padrão se não existir
      $usuario_padrao = [
         'admin' => [
            'senha' => password_hash('admin123', PASSWORD_DEFAULT),
            'nome' => 'Administrador',
            'email' => 'admin@exemplo.com',
            'data_criacao' => date('Y-m-d H:i:s'),
            'criado_por' => 'sistema'
         ]
      ];
      file_put_contents($arquivo_usuarios, json_encode($usuario_padrao, JSON_PRETTY_PRINT));
   }

   $usuarios = json_decode(file_get_contents($arquivo_usuarios), true);

   // Verifica se o usuário existe
   if (!isset($usuarios[$usuario])) {
      header('Location: admin.php?error=Usuário ou senha incorretos');
      exit;
   }

   // Verifica a senha
   if (password_verify($senha, $usuarios[$usuario]['senha'])) {
      // Login bem-sucedido
      $_SESSION['logado'] = true;
      $_SESSION['usuario'] = $usuario;
      $_SESSION['nome'] = $usuarios[$usuario]['nome'];
      $_SESSION['data_login'] = date('Y-m-d H:i:s');

      // Definir timezone do painel
      $config_file = 'painel_config.json';
      $timezone = 'America/Sao_Paulo';
      if (file_exists($config_file)) {
         $config = json_decode(file_get_contents($config_file), true);
         if (!empty($config['timezone'])) {
            $timezone = $config['timezone'];
         }
      }
      date_default_timezone_set($timezone);

      // Salvar último login no JSON
      $usuarios[$usuario]['ultimo_login'] = date('Y-m-d H:i:s');
      file_put_contents($arquivo_usuarios, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

      header('Location: painel.php');
      exit;
   } else {
      header('Location: admin.php?error=Usuário ou senha incorretos');
      exit;
   }
} else {
   // Se não foi POST, redireciona para a página de login
   header('Location: admin.php');
   exit;
}
