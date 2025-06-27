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
         max-width: 100vw;
         min-width: 1100px;
         overflow-x: auto;
      }

      .users-table table {
         width: 100% !important;
         table-layout: auto !important;
         min-width: 1000px;
      }

      .users-table th,
      .users-table td {
         padding: 10px 12px !important;
         font-size: 0.97rem;
         white-space: nowrap;
         vertical-align: middle !important;
      }

      .users-table th {
         text-align: left;
      }

      .users-table td {
         text-align: left;
      }

      .users-table td .user-avatar {
         display: flex;
         align-items: center;
         justify-content: center;
         margin: 0 auto;
      }

      .users-table td:last-child {
         text-align: center;
         padding-top: 0 !important;
         padding-bottom: 0 !important;
      }

      .users-table .acoes-btns {
         display: flex;
         flex-direction: row;
         gap: 6px;
         justify-content: flex-start;
         align-items: center;
         margin: 0;
         padding: 0;
         height: 36px;
      }

      @media (max-width: 900px) {
         .users-table .acoes-btns {
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
            gap: 4px;
         }
      }

      .users-table .btn,
      .users-table .btn-roxo {
         width: 100px;
         min-width: 100px;
         max-width: 100px;
         height: 36px;
         padding: 0;
         font-size: 0.93rem;
         border-radius: 4px;
         margin-bottom: 0;
         text-align: center;
         display: inline-block;
         line-height: 36px;
         vertical-align: middle;
         white-space: nowrap;
         overflow: hidden;
         text-overflow: ellipsis;
      }

      .users-table .btn-danger {
         background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
         color: #fff;
      }

      .users-table .btn-secondary {
         background: linear-gradient(135deg, #51cf66 0%, #40c057 100%);
         color: #fff;
      }

      .users-table .btn-roxo {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         color: #fff;
         border: none;
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
         <div class="content-header">
            <h2>Visualizar Usuários</h2>
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
                                 <th>Usuário</th>
                                 <th>Nome</th>
                                 <th>E-mail</th>
                                 <th>Data de Criação</th>
                                 <th>Último Login</th>
                                 <th>Ações</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php foreach ($usuarios as $usuario => $dados): ?>
                                 <tr>
                                    <td>
                                       <div class="user-avatar">
                                          <?php echo strtoupper(substr($dados['nome'], 0, 1)); ?>
                                       </div>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($usuario); ?></strong></td>
                                    <td><?php echo htmlspecialchars($dados['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($dados['email']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($dados['data_criacao'])); ?></td>
                                    <td><?php echo isset($dados['ultimo_login']) ? date('d/m/Y H:i', strtotime($dados['ultimo_login'])) : '<span style=\'color:#aaa\'>Nunca</span>'; ?></td>
                                    <td>
                                       <div class="acoes-btns">
                                          <a href="editar_usuario.php?usuario=<?php echo urlencode($usuario); ?>" class="btn btn-secondary">Editar</a>
                                          <?php if ($usuario !== $_SESSION['usuario']): ?>
                                             <a href="excluir_usuario.php?usuario=<?php echo urlencode($usuario); ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                                             <form method="post" action="desconectar_usuario.php" style="display:inline;">
                                                <input type="hidden" name="usuario" value="<?php echo htmlspecialchars($usuario); ?>">
                                                <button type="submit" class="btn btn-roxo">Desconectar</button>
                                             </form>
                                          <?php else: ?>
                                             <span style="color: #666; font-size: 0.9rem;">Usuário Atual</span>
                                          <?php endif; ?>
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
</body>

</html>