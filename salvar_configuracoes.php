<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

$config_file = 'painel_config.json';

// Carregar configuração padrão para garantir todos os campos
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

// Receber dados do formulário
$titulo = $_POST['titulo'] ?? $default_config['titulo'];
$sidebar_color = $_POST['sidebar_color'] ?? $default_config['sidebar_color'];
$shrink_sidebar = !empty($_POST['shrink_sidebar']);
$timezone = $_POST['timezone'] ?? $default_config['timezone'];
$menus = $default_config['menus'];
if (isset($_POST['menus']) && is_array($_POST['menus'])) {
   foreach ($_POST['menus'] as $i => $menu) {
      if (isset($menus[$i])) {
         $menus[$i]['nome'] = trim($menu['nome'] ?? $menus[$i]['nome']);
         $menus[$i]['icone'] = trim($menu['icone'] ?? $menus[$i]['icone']);
      }
   }
}

// Montar config final
$config = [
   'titulo' => $titulo,
   'sidebar_color' => $sidebar_color,
   'shrink_sidebar' => $shrink_sidebar,
   'timezone' => $timezone,
   'menus' => $menus
];

if (file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT))) {
   header('Location: configuracoes.php?success=Configurações salvas com sucesso!');
} else {
   header('Location: configuracoes.php?error=Erro ao salvar configurações!');
}
exit;
