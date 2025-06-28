<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

$config_file = 'site_config.json';
$config_site = [
   'titulo' => '',
   'logomarca' => '',
   'menu' => []
];
if (file_exists($config_file)) {
   $config_site = json_decode(file_get_contents($config_file), true);
}

// Carregar configurações do painel
$config_file_painel = 'painel_config.json';
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
$config_painel = $default_config;
if (file_exists($config_file_painel)) {
   $config_painel = json_decode(file_get_contents($config_file_painel), true) ?? $default_config;
}

// Reordena o array de menus para colocar 'registros' logo após 'perfil'
$menus = $config_painel['menus'];
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
$config_painel['menus'] = $novo_menus;

// Mensagens de feedback
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($config_painel['titulo']); ?></title>
   <link rel="stylesheet" href="css/painel.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
   <script>
      function addMenuItem(nome = '', link = '') {
         const list = document.getElementById('menu-list');
         const div = document.createElement('div');
         div.className = 'menu-item';
         div.innerHTML = `<input type="text" name="menu_nome[]" placeholder="Nome" value="${nome}" required> <input type="text" name="menu_link[]" placeholder="Link" value="${link}" required> <button type="button" class="remove-btn" onclick="this.parentNode.remove()">Remover</button>`;
         list.appendChild(div);
      }

      function initMenu() {
         const menu = <?php echo json_encode($config_site['menu']); ?>;
         if (menu.length === 0) addMenuItem();
         else menu.forEach(item => addMenuItem(item.nome, item.link));
      }
      window.onload = initMenu;
   </script>
</head>

<body>
   <div class="admin-layout">
      <!-- Menu Lateral -->
      <div class="sidebar<?php echo !empty($config_painel['shrink_sidebar']) ? ' shrink' : ''; ?>" style="background: <?php echo htmlspecialchars($config_painel['sidebar_color']); ?>;">
         <div class="sidebar-header">
            <div class="profile-section">
               <div class="profile-photo">
                  <img src="imgs/img_perfil.jpeg" alt="Foto de Perfil" id="profile-photo">
               </div>
               <h1><?php echo htmlspecialchars($config_painel['titulo']); ?></h1>
            </div>
         </div>
         <div class="sidebar-menu">
            <?php foreach ($config_painel['menus'] as $menu): if ($menu['id'] === 'sair') continue; ?>
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
                        } ?>" class="menu-item<?php echo $menu['id'] === 'editar_site' ? ' active' : ''; ?>">
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
            <h2>Editar Informações do Site</h2>
         </div>

         <div class="content-container">
            <?php if ($error): ?><div class="error-message"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <?php if ($success): ?><div class="success-message"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

            <div class="container">
               <div class="submenu-tabs">
                  <a href="#site-info" class="tab-link active" onclick="showTab('site-info')">Informações do Site</a>
                  <a href="#especialidades" class="tab-link" onclick="showTab('especialidades')">Especialidades</a>
               </div>

               <div id="site-info" class="tab-content active">
                  <form action="salvar_site.php" method="POST" enctype="multipart/form-data">
                     <div class="form-group">
                        <label for="titulo">Título do Site:</label>
                        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($config_site['titulo'] ?? ''); ?>" required>
                     </div>

                     <div class="form-group">
                        <label for="logomarca">Logomarca (atual: <?php echo $config_site['logomarca'] ? htmlspecialchars($config_site['logomarca']) : 'Nenhuma'; ?>):</label>
                        <?php if (!empty($config_site['logomarca'])): ?>
                           <img src="<?php echo htmlspecialchars($config_site['logomarca']); ?>" alt="Logomarca atual" class="img-preview">
                        <?php endif; ?>
                        <input type="file" id="logomarca" name="logomarca" accept="image/*">
                        <div class="info">Deixe em branco para manter a logomarca atual</div>
                     </div>

                     <div class="form-group">
                        <label for="sobre_titulo">Título da Seção "Sobre Mim":</label>
                        <input type="text" id="sobre_titulo" name="sobre_titulo" value="<?php echo htmlspecialchars($config_site['sobre_titulo'] ?? 'Sobre Mim'); ?>" required>
                     </div>

                     <div class="form-group">
                        <label for="sobre_conteudo">Conteúdo da Seção "Sobre Mim":</label>
                        <textarea id="sobre_conteudo" name="sobre_conteudo" required><?php echo htmlspecialchars($config_site['sobre_conteudo'] ?? ''); ?></textarea>
                     </div>

                     <div class="form-group">
                        <label>Menu do Site:</label>
                        <div id="menu-list" class="menu-list">
                           <!-- Itens do menu serão adicionados via JavaScript -->
                        </div>
                        <button type="button" class="add-btn" onclick="addMenuItem()">Adicionar Item do Menu</button>
                     </div>

                     <div class="btn-group">
                        <button type="submit" class="btn">Salvar Alterações</button>
                        <a href="painel.php" class="btn btn-secondary">Cancelar</a>
                     </div>
                  </form>
               </div>

               <div id="especialidades" class="tab-content">
                  <div class="especialidades-info">
                     <h3>Gerenciar Especialidades</h3>
                     <p>Personalize os ícones, cores e conteúdo das suas especialidades profissionais.</p>
                     <a href="gerenciar_especialidades.php" class="btn btn-secondary">Abrir Gerenciador de Especialidades</a>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <script>
      ClassicEditor
         .create(document.querySelector('#sobre_conteudo'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo']
         })
         .catch(error => {
            console.error(error);
         });

      function addMenuItem(nome = '', link = '') {
         const list = document.getElementById('menu-list');
         const div = document.createElement('div');
         div.className = 'menu-item';
         div.innerHTML = `<input type="text" name="menu_nome[]" placeholder="Nome" value="${nome}" required> <input type="text" name="menu_link[]" placeholder="Link" value="${link}" required> <button type="button" class="remove-btn" onclick="this.parentNode.remove()">Remover</button>`;
         list.appendChild(div);
      }

      function initMenu() {
         const menu = <?php echo json_encode($config_site['menu']); ?>;
         if (menu.length === 0) addMenuItem();
         else menu.forEach(item => addMenuItem(item.nome, item.link));
      }

      function showTab(tabName) {
         // Esconde todas as abas
         document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
         });
         document.querySelectorAll('.tab-link').forEach(link => {
            link.classList.remove('active');
         });

         // Mostra a aba selecionada
         document.getElementById(tabName).classList.add('active');
         event.target.classList.add('active');
      }

      window.onload = initMenu;
   </script>

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