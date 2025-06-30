<?php
session_start();
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
      ['id' => 'registros', 'nome' => 'Registros', 'icone' => 'fas fa-phone'],
      ['id' => 'arquivos', 'nome' => 'Arquivos', 'icone' => 'fas fa-photo-film'],
      ['id' => 'ver_site', 'nome' => 'Ver Site', 'icone' => 'fas fa-home'],
      ['id' => 'configuracoes', 'nome' => 'Configurações', 'icone' => 'fas fa-cog'],
      ['id' => 'sair', 'nome' => 'Sair', 'icone' => 'fas fa-sign-out-alt'],
   ]
];
$config = $default_config;
if (file_exists($config_file)) {
   $config = json_decode(file_get_contents($config_file), true) ?? $default_config;
}
if (!empty($config['timezone'])) {
   date_default_timezone_set($config['timezone']);
}
$menus = $config['menus'];
$novo_menus = [];
foreach ($menus as $menu) {
   if ($menu['id'] === 'dashboard') $novo_menus[] = $menu;
   if ($menu['id'] === 'perfil') {
      $novo_menus[] = $menu;
      foreach ($menus as $m2) {
         if ($m2['id'] === 'registros') $novo_menus[] = $m2;
      }
   }
}
foreach ($menus as $menu) {
   if ($menu['id'] !== 'dashboard' && $menu['id'] !== 'perfil' && $menu['id'] !== 'registros' && $menu['id'] !== 'sair') {
      $novo_menus[] = $menu;
   }
}
$config['menus'] = $novo_menus;
define('MENU_ATIVO', 'arquivos');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Biblioteca de Arquivos</title>
   <link rel="stylesheet" href="css/painel.css">
   <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@200;400;500;700&display=swap" rel="stylesheet">
   <style>
      .sidebar {
         min-width: 220px;
      }

      .file-thumb img {
         object-fit: cover;
         width: 120px;
         height: 120px;
         display: block;
         margin: 0 auto;
      }

      .file-thumb.selected {
         outline: 2px solid #6c2c8f;
      }

      .btn {
         display: inline-block;
         font-weight: 500;
         border-radius: 8px;
         transition: background 0.2s, color 0.2s;
      }

      .btn-secondary {
         background: #fff;
         color: #6c2c8f;
         border: 2px solid #6c2c8f;
      }

      .btn-secondary:hover {
         background: #6c2c8f;
         color: #fff;
      }

      #uploadForm .btn,
      #newFolderBtn {
         margin-right: 12px;
      }

      .file-grid {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
         gap: 16px;
         margin-top: 32px;
      }

      .file-thumb {
         width: 120px;
         height: 120px;
         display: flex;
         align-items: center;
         justify-content: center;
         background: #fff;
         border-radius: 12px;
         box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
         cursor: pointer;
         transition: box-shadow 0.2s;
      }

      .file-thumb img {
         width: 100%;
         height: 100%;
         object-fit: cover;
         border-radius: 10px;
      }

      #fileModal {
         display: none;
         align-items: center;
         justify-content: center;
         position: fixed;
         inset: 0;
         background: rgba(0, 0, 0, 0.4);
         z-index: 9999;
      }

      #fileModal .modal-box {
         width: 500px;
         height: 400px;
         background: #fff;
         border-radius: 16px;
         box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.18);
         padding: 32px 24px;
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: center;
         position: relative;
      }

      #fileModal img {
         max-width: 100%;
         max-height: 180px;
         margin-bottom: 18px;
         border-radius: 10px;
      }

      #fileModal .close-btn {
         position: absolute;
         top: 12px;
         right: 16px;
         background: none;
         border: none;
         font-size: 1.5rem;
         color: #888;
         cursor: pointer;
      }

      #fileModal .modal-box .popup-actions {
         display: flex;
         gap: 16px;
         margin-top: 24px;
         justify-content: center;
      }

      #newFolderBtn,
      #uploadForm {
         display: inline-flex !important;
         width: auto !important;
         min-width: unset !important;
         max-width: unset !important;
         margin: 0 !important;
         vertical-align: middle;
      }

      .flex.items-center.mb-6 {
         flex-wrap: nowrap !important;
      }

      .biblioteca-filtros {
         display: flex;
         flex-direction: column;
         gap: 12px;
         margin-bottom: 24px;
      }
   </style>
</head>

<body class="bg-gray-100 min-h-screen">
   <div class="admin-layout">
      <!-- Menu Lateral -->
      <?php include 'src/sidebar.php'; ?>
      <!-- Conteúdo Principal -->
      <div class="main-content">
         <div class="content-header">
            <h2>Biblioteca de Arquivos</h2>
         </div>
         <div class="content-container">
            <div class="biblioteca-filtros">
               <input type="text" id="search" placeholder="Buscar arquivo..." class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400" />
               <select id="dateFilter" class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400">
                  <option value="">Todos os meses</option>
               </select>
               <select id="sortBy" class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400">
                  <option value="date">Mais recentes</option>
                  <option value="name">Nome</option>
                  <option value="size">Tamanho</option>
               </select>
            </div>
            <div class="flex gap-4 items-center mb-6">
               <button id="newFolderBtn" class="btn btn-secondary flex items-center gap-2 shrink-0">
                  <i class="fas fa-folder-plus"></i> Nova Pasta
               </button>
               <form id="uploadForm" class="flex items-center gap-2 m-0 shrink-0 w-auto" enctype="multipart/form-data" style="margin:0;">
                  <label class="btn flex items-center gap-2 bg-purple-700 text-white hover:bg-purple-800 border-0 px-4 py-2 rounded cursor-pointer">
                     <i class="fas fa-upload"></i>
                     <span>Enviar arquivos</span>
                     <input type="file" name="files[]" id="fileInput" class="hidden" multiple accept=".svg,.webp,.png,.jpg,.jpeg,.gif" />
                  </label>
                  <div id="uploadProgress" class="w-64 h-2 bg-gray-200 rounded overflow-hidden hidden">
                     <div class="h-full bg-purple-600 transition-all" style="width:0%"></div>
                  </div>
                  <span id="uploadStatus" class="text-sm text-gray-600"></span>
               </form>
            </div>
            <div id="fileGrid" class="file-grid grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            </div>
            <div id="fileModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
               <div class="modal-box">
                  <button id="closeModal" class="close-btn"><i class="fas fa-times"></i></button>
                  <div id="modalContent"></div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <script src="js/arquivos.js"></script>
</body>

</html>