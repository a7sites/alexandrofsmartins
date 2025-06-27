<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
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
// Definir timezone do painel
if (!empty($config['timezone'])) {
   date_default_timezone_set($config['timezone']);
}

$nome_usuario = $_SESSION['nome'] ?? 'Usuário';
$usuario = $_SESSION['usuario'] ?? '';
$data_login = $_SESSION['data_login'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($config['titulo']); ?></title>
   <link rel="stylesheet" href="css/painel.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
   <div class="admin-layout">
      <!-- Menu Lateral -->
      <div class="sidebar" style="background: <?php echo htmlspecialchars($config['sidebar_color']); ?>;">
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
                           case 'ver_site':
                              echo 'index.html';
                              break;
                           case 'configuracoes':
                              echo 'configuracoes.php';
                              break;
                        } ?>" class="menu-item<?php echo $menu['id'] === 'dashboard' ? ' active' : ''; ?>">
                  <i class="<?php echo htmlspecialchars($menu['icone']); ?>"></i>
                  <span><?php echo htmlspecialchars($menu['nome']); ?></span>
               </a>
            <?php endforeach; ?>
            <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
         </div>
      </div>

      <!-- Conteúdo Principal -->
      <div class="main-content">
         <div class="content-header">
            <h2>Dashboard</h2>
         </div>

         <div class="content-container">
            <div class="welcome-card">
               <h2>👋 Bem-vindo ao Painel de Controle</h2>
               <p>Este é o seu centro de administração. Aqui você pode gerenciar usuários, visualizar estatísticas e controlar o acesso ao sistema.</p>
            </div>

            <div class="stats-grid">
               <div class="stat-card">
                  <h3><?php echo count(json_decode(file_get_contents('usuarios.json'), true) ?? []); ?></h3>
                  <p>Usuários Cadastrados</p>
               </div>
               <div class="stat-card">
                  <h3><?php echo date('d/m/Y'); ?></h3>
                  <p>Data Atual</p>
               </div>
               <div class="stat-card">
                  <h3><?php echo date('H:i'); ?></h3>
                  <p>Hora Atual</p>
               </div>
            </div>

            <div class="actions-grid">
               <div class="action-card">
                  <h3>👥 Gerenciar Usuários</h3>
                  <p>Cadastre novos usuários no sistema ou visualize os usuários existentes.</p>
                  <a href="cadastrar.php" class="btn btn-secondary">Cadastrar Usuário</a>
               </div>

               <div class="action-card">
                  <h3>📊 Visualizar Usuários</h3>
                  <p>Veja todos os usuários cadastrados no sistema com suas informações.</p>
                  <a href="visualizar_usuarios.php" class="btn">Ver Usuários</a>
               </div>

               <div class="action-card">
                  <h3>🏠 Voltar ao Site</h3>
                  <p>Retorne à página principal do site.</p>
                  <a href="index.html" class="btn">Ir para o Site</a>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="footer">
      <p>&copy; 2024 Painel Administrativo - Desenvolvido com segurança por Alexandro F. S. Martins</p>
   </div>
</body>

</html>