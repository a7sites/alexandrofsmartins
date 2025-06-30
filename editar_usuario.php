<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

$arquivo_usuarios = 'usuarios.json';
$usuarios = file_exists($arquivo_usuarios) ? json_decode(file_get_contents($arquivo_usuarios), true) : [];

$usuario = $_GET['usuario'] ?? '';
if (!isset($usuarios[$usuario])) {
   header('Location: visualizar_usuarios.php?error=Usuário não encontrado');
   exit;
}
$dados = $usuarios[$usuario];

// Carregar dados do perfil para o menu
$perfil_file = 'perfil_config.json';
$perfil = [
   'foto_perfil' => 'imgs/img_perfil.jpeg'
];
if (file_exists($perfil_file)) {
   $perfil = json_decode(file_get_contents($perfil_file), true) ?? $perfil;
}

// Carregar configurações do painel
$config_file = 'painel_config.json';
$default_config = [
   'titulo' => 'Painel A7',
   'sidebar_color' => '#23282d',
   'shrink_sidebar' => false,
   'timezone' => 'America/Sao_Paulo',
   'menus' => [
      ['id' => 'dashboard', 'nome' => 'Dashboard', 'icone' => 'fas fa-tachometer-alt'],
      ['id' => 'perfil', 'nome' => 'Meu Perfil', 'icone' => 'fas fa-user'],
      ['id' => 'editar_site', 'nome' => 'Editar Site', 'icone' => 'fas fa-edit'],
      ['id' => 'cadastrar', 'nome' => 'Cadastrar Usuário', 'icone' => 'fas fa-user-plus'],
      ['id' => 'usuarios', 'nome' => 'Ver Usuários', 'icone' => 'fas fa-users'],
      ['id' => 'ver_site', 'nome' => 'Ver Site', 'icone' => 'fas fa-home'],
      ['id' => 'configuracoes', 'nome' => 'Configurações', 'icone' => 'fas fa-cog'],
      ['id' => 'sair', 'nome' => 'Sair', 'icone' => 'fas fa-sign-out-alt'],
   ]
];
$config = $default_config;
if (file_exists($config_file)) {
   $config = json_decode(file_get_contents($config_file), true) ?? $default_config;
}

// Reordena o array de menus para colocar 'registros' logo após 'perfil'
$menus = $config['menus'];
$novo_menus = [];
foreach ($menus as $menu) {
   if ($menu['id'] === 'dashboard') $novo_menus[] = $menu;
   if ($menu['id'] === 'perfil') {
      $novo_menus[] = $menu;
      // Após perfil, inserir registros
      foreach ($menus as $m2) {
         if ($m2['id'] === 'registros') $novo_menus[] = $m2;
      }
   }
}
// Adiciona os demais menus (exceto registros, já inserido)
foreach ($menus as $menu) {
   if ($menu['id'] !== 'dashboard' && $menu['id'] !== 'perfil' && $menu['id'] !== 'registros' && $menu['id'] !== 'sair') {
      $novo_menus[] = $menu;
   }
}
$config['menus'] = $novo_menus;

define('MENU_ATIVO', 'usuarios');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($config['titulo']); ?></title>
   <link rel="stylesheet" href="css/painel.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
   <div class="admin-layout">
      <!-- Menu Lateral -->
      <?php include 'src/sidebar.php'; ?>

      <!-- Conteúdo Principal -->
      <div class="main-content">
         <div class="content-header">
            <h2>Editar Usuário: <?php echo htmlspecialchars($usuario); ?></h2>
         </div>

         <div class="content-container">
            <div class="container">
               <?php if (isset($_GET['error'])): ?>
                  <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
               <?php endif; ?>
               <?php if (isset($_GET['success'])): ?>
                  <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
               <?php endif; ?>

               <div class="profile-edit-container">
                  <form action="salvar_edicao_usuario.php" method="POST">
                     <input type="hidden" name="usuario" value="<?php echo htmlspecialchars($usuario); ?>">
                     <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($dados['nome'] ?? ''); ?>" required>
                     </div>
                     <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($dados['email'] ?? ''); ?>" required>
                     </div>
                     <div class="form-group">
                        <label for="senha">Nova Senha (opcional):</label>
                        <input type="password" id="senha" name="senha" placeholder="Deixe em branco para não alterar">
                        <div class="info">Se quiser alterar a senha, preencha este campo.</div>
                     </div>
                     <div class="btn-group">
                        <button type="submit" class="btn">Salvar Alterações</button>
                        <a href="visualizar_usuarios.php" class="btn btn-secondary">Cancelar</a>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>

   <script>
      // Ajustar main-content quando sidebar está encolhida
      document.addEventListener('DOMContentLoaded', function() {
         const sidebar = document.querySelector('.sidebar');
         const mainContent = document.querySelector('.main-content');

         if (sidebar && mainContent) {
            if (sidebar.classList.contains('shrink')) {
               mainContent.classList.add('sidebar-shrink');
            }
         }
      });
   </script>
</body>

</html>