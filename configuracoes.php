<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

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
$timezones = [
   'America/Sao_Paulo',
   'America/Argentina/Buenos_Aires',
   'America/New_York',
   'Europe/Lisbon',
   'Europe/London',
   'Asia/Tokyo',
   'UTC'
];
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
$sidebar_color = !empty($config['sidebar_color']) ? $config['sidebar_color'] : '#23282d';

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
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Configurações do Painel</title>
   <link rel="stylesheet" href="css/painel.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
   <div class="admin-layout">
      <!-- Sidebar -->
      <div class="sidebar" style="background: <?php echo htmlspecialchars($sidebar_color); ?>;">
         <div class="sidebar-header">
            <div class="profile-section">
               <div class="profile-photo">
                  <img src="imgs/img_perfil.jpeg" alt="Foto de Perfil" id="profile-photo">
               </div>
               <h1><?php echo htmlspecialchars($config['titulo']); ?></h1>
            </div>
         </div>
         <div class="sidebar-menu">
            <?php foreach ($config['menus'] as $menu): if ($menu['id'] === 'sair') continue; ?>
               <a href="<?php
                        switch ($menu['id']) {
                           case 'dashboard':
                              echo 'painel.php';
                              break;
                           case 'perfil':
                              echo 'meu_perfil.php';
                              break;
                           case 'editar_site':
                              echo 'editar_site.php';
                              break;
                           case 'cadastrar':
                              echo 'cadastrar.php';
                              break;
                           case 'usuarios':
                              echo 'visualizar_usuarios.php';
                              break;
                           case 'registros':
                              echo 'registros.php';
                              break;
                           case 'ver_site':
                              echo 'index.php';
                              break;
                           case 'configuracoes':
                              echo 'configuracoes.php';
                              break;
                        } ?>" class="menu-item<?php echo $menu['id'] === 'configuracoes' ? ' active' : ''; ?>">
                  <i class="<?php echo htmlspecialchars($menu['icone']); ?>"></i>
                  <span><?php echo htmlspecialchars($menu['nome']); ?></span>
               </a>
            <?php endforeach; ?>
            <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
         </div>
      </div>
      <!-- Conteúdo Principal -->
      <div class="main-content">
         <!-- Botão mobile para menu -->
         <div class="mobile-menu-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
         </div>

         <div class="content-header">
            <h2>Configurações do Painel</h2>
         </div>

         <div class="content-container">
            <div class="container">
               <div class="form-card">
                  <h2>⚙️ Configurações do Sistema</h2>

                  <?php if (isset($_GET['error'])): ?>
                     <div class="error-message">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                     </div>
                  <?php endif; ?>

                  <?php if (isset($_GET['success'])): ?>
                     <div class="success-message">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                     </div>
                  <?php endif; ?>

                  <form action="salvar_configuracoes.php" method="POST">
                     <div class="form-group">
                        <label for="titulo">Título do Painel:</label>
                        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($config['titulo']); ?>" required>
                     </div>

                     <div class="form-group">
                        <label for="sidebar_color">Cor da Barra Lateral:</label>
                        <input type="color" id="sidebar_color" name="sidebar_color" value="<?php echo htmlspecialchars($config['sidebar_color']); ?>">
                     </div>

                     <div class="form-group">
                        <label for="timezone">Fuso Horário:</label>
                        <select id="timezone" name="timezone">
                           <option value="America/Sao_Paulo" <?php echo $config['timezone'] === 'America/Sao_Paulo' ? 'selected' : ''; ?>>Brasília (GMT-3)</option>
                           <option value="America/Manaus" <?php echo $config['timezone'] === 'America/Manaus' ? 'selected' : ''; ?>>Manaus (GMT-4)</option>
                           <option value="America/Belem" <?php echo $config['timezone'] === 'America/Belem' ? 'selected' : ''; ?>>Belém (GMT-3)</option>
                           <option value="America/Fortaleza" <?php echo $config['timezone'] === 'America/Fortaleza' ? 'selected' : ''; ?>>Fortaleza (GMT-3)</option>
                           <option value="America/Recife" <?php echo $config['timezone'] === 'America/Recife' ? 'selected' : ''; ?>>Recife (GMT-3)</option>
                           <option value="America/Salvador" <?php echo $config['timezone'] === 'America/Salvador' ? 'selected' : ''; ?>>Salvador (GMT-3)</option>
                           <option value="America/Maceio" <?php echo $config['timezone'] === 'America/Maceio' ? 'selected' : ''; ?>>Maceió (GMT-3)</option>
                           <option value="America/Aracaju" <?php echo $config['timezone'] === 'America/Aracaju' ? 'selected' : ''; ?>>Aracaju (GMT-3)</option>
                           <option value="America/Noronha" <?php echo $config['timezone'] === 'America/Noronha' ? 'selected' : ''; ?>>Fernando de Noronha (GMT-2)</option>
                        </select>
                     </div>

                     <div class="form-group">
                        <label>
                           <input type="checkbox" name="shrink_sidebar" <?php echo $config['shrink_sidebar'] ? 'checked' : ''; ?>>
                           Modo Encolhido da Barra Lateral
                        </label>
                     </div>

                     <div class="btn-group">
                        <button type="submit" class="btn">Salvar Configurações</button>
                        <a href="painel.php" class="btn btn-secondary">Voltar ao Painel</a>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>

   <script>
      function toggleSidebar() {
         const sidebar = document.querySelector('.sidebar');
         sidebar.classList.toggle('open');
      }

      // Fechar sidebar ao clicar fora dela em mobile
      document.addEventListener('click', function(e) {
         const sidebar = document.querySelector('.sidebar');
         const mobileBtn = document.querySelector('.mobile-menu-btn');

         if (window.innerWidth <= 768 &&
            sidebar.classList.contains('open') &&
            !sidebar.contains(e.target) &&
            !mobileBtn.contains(e.target)) {
            sidebar.classList.remove('open');
         }
      });

      // Fechar sidebar ao clicar em links do menu em mobile
      document.querySelectorAll('.menu-item').forEach(link => {
         link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
               document.querySelector('.sidebar').classList.remove('open');
            }
         });
      });
   </script>
</body>

</html>