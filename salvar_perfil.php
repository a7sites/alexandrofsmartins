<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

$perfil_file = 'perfil_config.json';
$usuarios_file = 'usuarios.json';

// Carregar dados atuais do perfil
$perfil = [
   'nome' => 'Alexandro Martins',
   'email' => 'alexandro@exemplo.com',
   'biografia' => 'Desenvolvedor web apaixonado por criar soluções digitais inovadoras e experiências únicas para meus clientes.',
   'telefone' => '(11) 99999-9999',
   'github' => 'https://github.com/alexandrofmartins',
   'linkedin' => 'https://linkedin.com/in/alexandrofmartins',
   'instagram' => 'https://instagram.com/alexandrofmartins',
   'facebook' => 'https://facebook.com/alexandrofmartins',
   'twitter' => 'https://twitter.com/alexandrofmartins',
   'youtube' => 'https://youtube.com/@alexandrofmartins',
   'foto_perfil' => 'imgs/img_perfil.jpeg'
];

if (file_exists($perfil_file)) {
   $perfil = json_decode(file_get_contents($perfil_file), true) ?? $perfil;
}

// Processar upload de foto
$foto_perfil = $perfil['foto_perfil']; // Manter foto atual por padrão
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
   $file = $_FILES['foto_perfil'];
   $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
   $max_size = 2 * 1024 * 1024; // 2MB

   if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
      $upload_dir = 'imgs/';
      $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
      $new_filename = 'perfil_' . time() . '.' . $file_extension;
      $upload_path = $upload_dir . $new_filename;

      if (move_uploaded_file($file['tmp_name'], $upload_path)) {
         $foto_perfil = $upload_path;
      }
   }
}

// Validar alteração de senha
$senha_atual = $_POST['senha_atual'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';

if (!empty($nova_senha)) {
   // Verificar senha atual
   if (file_exists($usuarios_file)) {
      $usuarios = json_decode(file_get_contents($usuarios_file), true) ?? [];
      $usuario_atual = null;

      foreach ($usuarios as $usuario) {
         if ($usuario['email'] === $_SESSION['email']) {
            $usuario_atual = $usuario;
            break;
         }
      }

      if ($usuario_atual && password_verify($senha_atual, $usuario_atual['senha'])) {
         // Atualizar senha no arquivo de usuários
         foreach ($usuarios as &$usuario) {
            if ($usuario['email'] === $_SESSION['email']) {
               $usuario['senha'] = password_hash($nova_senha, PASSWORD_DEFAULT);
               break;
            }
         }
         file_put_contents($usuarios_file, json_encode($usuarios, JSON_PRETTY_PRINT));
      } else {
         header('Location: meu_perfil.php?error=Senha atual incorreta');
         exit;
      }
   }
}

// Atualizar dados do perfil
$perfil_atualizado = [
   'nome' => $_POST['nome'] ?? $perfil['nome'],
   'email' => $_POST['email'] ?? $perfil['email'],
   'biografia' => $_POST['biografia'] ?? $perfil['biografia'],
   'telefone' => $_POST['telefone'] ?? $perfil['telefone'],
   'github' => $_POST['github'] ?? $perfil['github'],
   'linkedin' => $_POST['linkedin'] ?? $perfil['linkedin'],
   'instagram' => $_POST['instagram'] ?? $perfil['instagram'],
   'facebook' => $_POST['facebook'] ?? $perfil['facebook'],
   'twitter' => $_POST['twitter'] ?? $perfil['twitter'],
   'youtube' => $_POST['youtube'] ?? $perfil['youtube'],
   'foto_perfil' => $foto_perfil
];

// Salvar no arquivo JSON
if (file_put_contents($perfil_file, json_encode($perfil_atualizado, JSON_PRETTY_PRINT))) {
   header('Location: meu_perfil.php?success=Perfil atualizado com sucesso!');
} else {
   header('Location: meu_perfil.php?error=Erro ao salvar perfil');
}
