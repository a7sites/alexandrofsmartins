<?php
// Este include espera que $config['menus'], $config['sidebar_color'], $config['shrink_sidebar'], $config['titulo'] e $perfil['foto_perfil'] estejam definidos no escopo da página.
?>
<div class="sidebar<?php echo !empty($config['shrink_sidebar']) ? ' shrink' : ''; ?>" style="background: <?php echo htmlspecialchars($config['sidebar_color']); ?>;">
   <div class="sidebar-header">
      <div class="profile-section">
         <div class="profile-photo">
            <img src="<?php echo htmlspecialchars(isset($perfil['foto_perfil']) ? $perfil['foto_perfil'] : 'imgs/img_perfil.jpeg'); ?>" alt="Foto de Perfil" id="profile-photo">
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
                     case 'arquivos':
                        echo 'arquivos.php';
                        break;
                     case 'ver_site':
                        echo 'index.php';
                        break;
                     case 'configuracoes':
                        echo 'configuracoes.php';
                        break;
                  } ?>" class="menu-item<?php if (defined('MENU_ATIVO') && MENU_ATIVO === $menu['id']) echo ' active'; ?>">
            <i class="<?php echo htmlspecialchars($menu['icone']); ?>"></i>
            <span><?php echo htmlspecialchars($menu['nome']); ?></span>
         </a>
      <?php endforeach; ?>
      <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
   </div>
</div>