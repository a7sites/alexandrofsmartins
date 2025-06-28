<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

echo "<h1>Teste Simples</h1>";
echo "<p>Usuário logado: " . ($_SESSION['nome'] ?? 'N/A') . "</p>";

// Testar carregamento do JSON
$config = json_decode(file_get_contents('painel_config.json'), true);
echo "<p>Config carregada: " . ($config ? 'SIM' : 'NÃO') . "</p>";

// Testar carregamento do WhatsApp
$whatsapp = json_decode(file_get_contents('whatsapp.json'), true);
echo "<p>WhatsApp carregado: " . ($whatsapp ? 'SIM' : 'NÃO') . "</p>";
echo "<p>Total de contatos: " . count($whatsapp) . "</p>";

echo "<br><a href='registros.php'>Ir para Registros</a>";
echo "<br><a href='painel.php'>Voltar ao Painel</a>";
