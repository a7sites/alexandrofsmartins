<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

// Carregar configurações do painel
$config_file = 'painel_config.json';
$config = json_decode(file_get_contents($config_file), true) ?? [];

echo "<h2>Debug do Menu</h2>";
echo "<pre>";
print_r($config['menus']);
echo "</pre>";

echo "<h3>Menu 'registros' encontrado:</h3>";
$registros_menu = null;
foreach ($config['menus'] as $menu) {
   if ($menu['id'] === 'registros') {
      $registros_menu = $menu;
      break;
   }
}

if ($registros_menu) {
   echo "✅ Menu 'registros' encontrado:<br>";
   echo "ID: " . $registros_menu['id'] . "<br>";
   echo "Nome: " . $registros_menu['nome'] . "<br>";
   echo "Ícone: " . $registros_menu['icone'] . "<br>";
} else {
   echo "❌ Menu 'registros' NÃO encontrado!<br>";
}

echo "<br><a href='registros.php'>Testar acesso direto ao registros.php</a>";
