<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['usuario'])) {
   $usuario = $_POST['usuario'];
   // Caminho padrão das sessões no PHP
   $sessao_path = ini_get('session.save_path') ?: sys_get_temp_dir();
   $desconectado = false;

   // Tenta encontrar e remover arquivos de sessão do usuário
   foreach (glob($sessao_path . '/sess_*') as $sess_file) {
      $sess_data = file_get_contents($sess_file);
      if (strpos($sess_data, '"usuario";s:' . strlen($usuario) . ':"' . $usuario . '"') !== false) {
         @unlink($sess_file);
         $desconectado = true;
      }
   }
   $msg = $desconectado ? 'Usuário desconectado com sucesso!' : 'Sessão do usuário não encontrada.';
   header('Location: visualizar_usuarios.php?success=' . urlencode($msg));
   exit;
}
header('Location: visualizar_usuarios.php?error=Requisição inválida');
exit;
