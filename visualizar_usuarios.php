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

// Carrega os usuários
$arquivo_usuarios = 'usuarios.json';
$usuarios = [];

if (file_exists($arquivo_usuarios)) {
   $usuarios = json_decode(file_get_contents($arquivo_usuarios), true) ?? [];
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
   <style>
      .users-table {
         background: white;
         border-radius: 10px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
         overflow: hidden;
         margin-bottom: 2rem;
      }

      .table-container {
         overflow-x: auto;
         max-width: 100%;
      }

      .users-table table {
         width: 100%;
         border-collapse: collapse;
         min-width: 800px;
      }

      .users-table th,
      .users-table td {
         padding: 12px 15px;
         text-align: left;
         border-bottom: 1px solid #e1e5e9;
         vertical-align: middle;
      }

      .users-table th {
         background: #f8f9fa;
         font-weight: 600;
         color: #333;
         font-size: 0.95rem;
      }

      .users-table tr:hover {
         background: #f8f9fa;
      }

      .users-table tr:last-child td {
         border-bottom: none;
      }

      .user-avatar {
         display: flex;
         align-items: center;
         justify-content: center;
      }

      .user-avatar img {
         width: 40px;
         height: 40px;
         border-radius: 50%;
         object-fit: cover;
      }

      .acoes-btns {
         display: flex;
         gap: 8px;
         align-items: center;
         justify-content: flex-start;
         flex-wrap: wrap;
      }

      .users-table .btn {
         padding: 8px 12px;
         font-size: 0.9rem;
         border-radius: 6px;
         text-decoration: none;
         display: inline-flex;
         align-items: center;
         gap: 5px;
         transition: all 0.2s;
         white-space: nowrap;
         min-width: 80px;
         justify-content: center;
      }

      .users-table .btn:hover {
         transform: translateY(-1px);
         box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
      }

      .users-table .btn-danger {
         background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
         color: white;
         border: none;
      }

      .users-table .btn-secondary {
         background: linear-gradient(135deg, #51cf66 0%, #40c057 100%);
         color: white;
         border: none;
      }

      .users-table .btn-roxo {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         color: white;
         border: none;
      }

      /* Responsividade para mobile */
      @media (max-width: 1024px) {
         .users-table table {
            min-width: 700px;
         }

         .users-table th,
         .users-table td {
            padding: 10px 12px;
            font-size: 0.9rem;
         }

         .users-table .btn {
            padding: 6px 10px;
            font-size: 0.85rem;
            min-width: 70px;
         }
      }

      @media (max-width: 768px) {
         .users-table {
            margin: 0 -15px;
            border-radius: 0;
         }

         .table-container {
            margin: 0 -15px;
         }

         .users-table table {
            min-width: 600px;
         }

         .users-table th,
         .users-table td {
            padding: 8px 10px;
            font-size: 0.85rem;
         }

         .acoes-btns {
            flex-direction: column;
            gap: 4px;
            align-items: stretch;
         }

         .users-table .btn {
            width: 100%;
            padding: 8px;
            font-size: 0.8rem;
            min-width: auto;
            justify-content: center;
         }

         .user-avatar img {
            width: 35px;
            height: 35px;
         }

         .content-container {
            padding: 15px;
         }

         .stats-card {
            margin-bottom: 1rem;
         }
      }

      @media (max-width: 480px) {
         .users-table table {
            min-width: 500px;
         }

         .users-table th,
         .users-table td {
            padding: 6px 8px;
            font-size: 0.8rem;
         }

         .users-table .btn {
            padding: 6px 8px;
            font-size: 0.75rem;
         }

         .user-avatar img {
            width: 30px;
            height: 30px;
         }

         .content-container {
            padding: 10px;
         }
      }

      /* Melhorias para telas muito pequenas */
      @media (max-width: 360px) {
         .users-table table {
            min-width: 450px;
         }

         .users-table th,
         .users-table td {
            padding: 5px 6px;
            font-size: 0.75rem;
         }

         .users-table .btn {
            padding: 5px 6px;
            font-size: 0.7rem;
         }

         .user-avatar img {
            width: 25px;
            height: 25px;
         }
      }
   </style>
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
                           case 'registros':
                              echo 'registros.php';
                              break;
                           case 'ver_site':
                              echo 'index.php';
                              break;
                           case 'configuracoes':
                              echo 'configuracoes.php';
                              break;
                        } ?>" class="menu-item<?php echo $menu['id'] === 'usuarios' ? ' active' : ''; ?>">
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
            <h2>👥 Gerenciar Usuários</h2>
         </div>

         <div class="content-container">
            <div class="container">
               <div class="stats-card">
                  <h2><?php echo count($usuarios); ?></h2>
                  <p>Usuários Cadastrados no Sistema</p>
               </div>

               <div class="users-table">
                  <div class="table-header">
                     <h2>👥 Lista de Usuários</h2>
                  </div>
                  <div class="table-container">
                     <?php if (empty($usuarios)): ?>
                        <div class="empty-state">
                           <h3>Nenhum usuário cadastrado</h3>
                           <p>Comece cadastrando o primeiro usuário no sistema.</p>
                           <a href="cadastrar.php" class="btn btn-secondary">Cadastrar Usuário</a>
                        </div>
                     <?php else: ?>
                        <table>
                           <thead>
                              <tr>
                                 <th>Avatar</th>
                                 <th>Nome</th>
                                 <th>Usuário</th>
                                 <th>Email</th>
                                 <th>Último Login</th>
                                 <th>Ações</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php foreach ($usuarios as $index => $usuario): ?>
                                 <tr>
                                    <td>
                                       <div class="user-avatar">
                                          <img src="imgs/img_perfil.jpeg" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%;">
                                       </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['ultimo_login'] ?? 'Nunca'); ?></td>
                                    <td>
                                       <div class="acoes-btns">
                                          <a href="editar_usuario.php?id=<?php echo $index; ?>" class="btn btn-secondary">
                                             <i class="fas fa-edit"></i> Editar
                                          </a>
                                          <a href="excluir_usuario.php?id=<?php echo $index; ?>" class="btn btn-danger"
                                             onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                             <i class="fas fa-trash"></i> Excluir
                                          </a>
                                          <a href="desconectar_usuario.php?id=<?php echo $index; ?>" class="btn btn-roxo">
                                             <i class="fas fa-sign-out-alt"></i> Desconectar
                                          </a>
                                       </div>
                                    </td>
                                 </tr>
                              <?php endforeach; ?>
                           </tbody>
                        </table>
                     <?php endif; ?>
                  </div>
               </div>

               <div style="text-align: center; margin-top: 2rem;">
                  <a href="cadastrar.php" class="btn btn-secondary">Cadastrar Novo Usuário</a>
                  <a href="painel.php" class="btn">Voltar ao Painel</a>
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