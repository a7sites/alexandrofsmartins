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

define('MENU_ATIVO', 'cadastrar');
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
      <?php include 'src/sidebar.php'; ?>

      <!-- Conteúdo Principal -->
      <div class="main-content">
         <div class="content-header">
            <h2>Cadastrar Usuário</h2>
         </div>

         <div class="content-container">
            <div class="container">
               <div class="form-card">
                  <h2>📝 Cadastrar Novo Usuário</h2>

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

                  <form action="salvar_usuario.php" method="POST">
                     <div class="form-row">
                        <div class="form-group">
                           <label for="usuario">Usuário:</label>
                           <input type="text" id="usuario" name="usuario" required>
                        </div>

                        <div class="form-group">
                           <label for="nome">Nome Completo:</label>
                           <input type="text" id="nome" name="nome" required>
                        </div>
                     </div>

                     <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                     </div>

                     <div class="form-row">
                        <div class="form-group">
                           <label for="senha">Senha:</label>
                           <input type="password" id="senha" name="senha" required>
                        </div>

                        <div class="form-group">
                           <label for="confirmar_senha">Confirmar Senha:</label>
                           <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                        </div>
                     </div>

                     <div class="password-requirements">
                        <strong>Requisitos da senha:</strong>
                        <ul>
                           <li>Mínimo de 6 caracteres</li>
                           <li>Pelo menos uma letra maiúscula</li>
                           <li>Pelo menos um número</li>
                        </ul>
                     </div>

                     <div class="btn-group">
                        <button type="submit" class="btn">Cadastrar Usuário</button>
                        <a href="visualizar_usuarios.php" class="btn btn-secondary">Ver Usuários</a>
                        <a href="painel.php" class="btn btn-secondary">Voltar ao Painel</a>
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