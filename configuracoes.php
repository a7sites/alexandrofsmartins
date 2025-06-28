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
      <div class="sidebar<?php echo !empty($config['shrink_sidebar']) ? ' shrink' : ''; ?>" style="background: <?php echo htmlspecialchars($sidebar_color); ?>;">
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
         <div class="content-header">
            <h2>Configurações do Painel</h2>
         </div>
         <div class="content-container">
            <div class="container">
               <?php if ($error): ?><div class="error-message"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
               <?php if ($success): ?><div class="success-message"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
               <form action="salvar_configuracoes.php" method="POST" id="configForm">
                  <div class="form-section">
                     <h3>Título do Painel</h3>
                     <input type="text" name="titulo" value="<?php echo htmlspecialchars($config['titulo']); ?>" required>
                  </div>
                  <div class="form-section">
                     <h3>Cor de Fundo da Barra Lateral</h3>
                     <input type="color" name="sidebar_color" value="<?php echo htmlspecialchars($sidebar_color); ?>">
                  </div>
                  <div class="form-section">
                     <h3>Menus do Painel</h3>
                     <div class="form-row-4">
                        <?php foreach ($config['menus'] as $i => $menu): if ($menu['id'] === 'sair') continue; ?>
                           <div class="form-group">
                              <label>Nome do Menu:</label>
                              <input type="text" name="menus[<?php echo $i; ?>][nome]" value="<?php echo htmlspecialchars($menu['nome']); ?>">
                              <label>Ícone:</label>
                              <div class="icon-selector">
                                 <div class="icon-preview" onclick="abrirPopupIconeMenu(this)">
                                    <i class="<?php echo htmlspecialchars($menu['icone']); ?>"></i>
                                    <span><?php echo htmlspecialchars($menu['icone']); ?></span>
                                 </div>
                                 <input type="text" class="icon-input" name="menus[<?php echo $i; ?>][icone]" value="<?php echo htmlspecialchars($menu['icone']); ?>" readonly>
                                 <div class="icon-popup">
                                    <div class="icon-grid"></div>
                                 </div>
                              </div>
                              <input type="hidden" name="menus[<?php echo $i; ?>][id]" value="<?php echo htmlspecialchars($menu['id']); ?>">
                              <small>Ex: fas fa-user, fas fa-cog...</small>
                           </div>
                        <?php endforeach; ?>
                     </div>
                  </div>
                  <div class="form-section">
                     <h3>Modo da Barra Lateral</h3>
                     <label><input type="checkbox" name="shrink_sidebar" value="1" <?php echo !empty($config['shrink_sidebar']) ? 'checked' : ''; ?>> Encolher barra lateral (mostrar só ícones)</label>
                  </div>
                  <div class="form-section">
                     <h3>Timezone do Painel</h3>
                     <select name="timezone">
                        <?php foreach ($timezones as $tz): ?>
                           <option value="<?php echo $tz; ?>" <?php echo $config['timezone'] === $tz ? 'selected' : ''; ?>><?php echo $tz; ?></option>
                        <?php endforeach; ?>
                     </select>
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
   <script src="https://cdn.jsdelivr.net/npm/vanilla-picker@2.11.1/dist/vanilla-picker.min.js"></script>
   <script>
      // Lista de ícones Bootstrap populares
      const iconesBootstrap = [
         'bi-code-slash', 'bi-pencil-square', 'bi-wordpress', 'bi-palette', 'bi-laptop', 'bi-phone',
         'bi-gear', 'bi-star', 'bi-heart', 'bi-lightning', 'bi-fire', 'bi-rocket', 'bi-award',
         'bi-trophy', 'bi-gem', 'bi-diamond', 'bi-cpu', 'bi-motherboard', 'bi-display', 'bi-tablet',
         'bi-window', 'bi-browser-chrome', 'bi-browser-safari', 'bi-browser-edge', 'bi-browser-firefox',
         'bi-arrow-up-circle', 'bi-arrow-down-circle', 'bi-arrow-left-circle', 'bi-arrow-right-circle',
         'bi-check-circle', 'bi-x-circle', 'bi-exclamation-circle', 'bi-question-circle', 'bi-info-circle',
         'bi-plus-circle', 'bi-dash-circle', 'bi-x-lg', 'bi-plus-lg', 'bi-dash-lg', 'bi-asterisk',
         'bi-hash', 'bi-percent', 'bi-currency-dollar', 'bi-currency-euro', 'bi-currency-pound',
         'bi-currency-yen', 'bi-currency-bitcoin', 'bi-bank', 'bi-cash', 'bi-credit-card', 'bi-wallet',
         'bi-bag', 'bi-cart', 'bi-shop', 'bi-tag', 'bi-tags', 'bi-receipt', 'bi-receipt-cutoff',
         'bi-gift', 'bi-box', 'bi-box-seam', 'bi-box-arrow-up', 'bi-box-arrow-down', 'bi-box-arrow-left',
         'bi-box-arrow-right', 'bi-archive', 'bi-archive-fill', 'bi-inbox', 'bi-inbox-fill', 'bi-folder',
         'bi-folder-fill', 'bi-folder-plus', 'bi-folder-minus', 'bi-folder-x', 'bi-folder-check',
         'bi-file-earmark', 'bi-file-earmark-text', 'bi-file-earmark-image', 'bi-file-earmark-pdf',
         'bi-file-earmark-word', 'bi-file-earmark-excel', 'bi-file-earmark-powerpoint', 'bi-file-earmark-zip',
         'bi-file-earmark-music', 'bi-file-earmark-video', 'bi-file-earmark-code', 'bi-file-earmark-binary',
         'bi-file-earmark-break', 'bi-file-earmark-check', 'bi-file-earmark-minus', 'bi-file-earmark-plus',
         'bi-file-earmark-x', 'bi-file-earmark-ruled', 'bi-file-earmark-slides', 'bi-file-earmark-spreadsheet',
         'bi-file-earmark-person', 'bi-file-earmark-lock', 'bi-file-earmark-lock2', 'bi-file-earmark-shield',
         'bi-file-earmark-medical', 'bi-file-earmark-diff', 'bi-file-earmark-arrow-up', 'bi-file-earmark-arrow-down',
         'bi-file-earmark-arrow-left', 'bi-file-earmark-arrow-right', 'bi-file-earmark-bar-graph',
         'bi-file-earmark-easel', 'bi-file-earmark-font', 'bi-file-earmark-image', 'bi-file-earmark-lock2',
         'bi-file-earmark-medical', 'bi-file-earmark-minus', 'bi-file-earmark-music', 'bi-file-earmark-person',
         'bi-file-earmark-play', 'bi-file-earmark-plus', 'bi-file-earmark-post', 'bi-file-earmark-richtext',
         'bi-file-earmark-ruled', 'bi-file-earmark-slides', 'bi-file-earmark-spreadsheet', 'bi-file-earmark-text',
         'bi-file-earmark-word', 'bi-file-earmark-x', 'bi-file-earmark-zip', 'bi-file-earmark', 'bi-file',
         'bi-file-arrow-down', 'bi-file-arrow-up', 'bi-file-bar-graph', 'bi-file-binary', 'bi-file-break',
         'bi-file-check', 'bi-file-code', 'bi-file-diff', 'bi-file-earmark', 'bi-file-easel', 'bi-file-font',
         'bi-file-image', 'bi-file-lock', 'bi-file-lock2', 'bi-file-medical', 'bi-file-minus', 'bi-file-music',
         'bi-file-person', 'bi-file-play', 'bi-file-plus', 'bi-file-post', 'bi-file-richtext', 'bi-file-ruled',
         'bi-file-slides', 'bi-file-spreadsheet', 'bi-file-text', 'bi-file-word', 'bi-file-x', 'bi-file-zip'
      ];

      function abrirPopupIconeMenu(elemento) {
         const popup = elemento.nextElementSibling.nextElementSibling;
         const grid = popup.querySelector('.icon-grid');
         grid.innerHTML = '';
         iconesBootstrap.forEach(icone => {
            const div = document.createElement('div');
            div.className = 'icon-option';
            div.innerHTML = `<i class="${icone}"></i>`;
            div.onclick = () => selecionarIconeMenu(elemento, icone);
            grid.appendChild(div);
         });
         popup.classList.toggle('show');
         document.querySelectorAll('.icon-popup').forEach(p => {
            if (p !== popup) p.classList.remove('show');
         });
      }

      function selecionarIconeMenu(elemento, icone) {
         const preview = elemento.querySelector('i');
         const span = elemento.querySelector('span');
         const input = elemento.parentNode.querySelector('.icon-input');
         preview.className = icone;
         span.textContent = icone;
         input.value = icone;
         elemento.nextElementSibling.nextElementSibling.classList.remove('show');
      }

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