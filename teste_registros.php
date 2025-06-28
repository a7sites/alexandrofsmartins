<?php
session_start();

// Verifica se está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

echo "Teste de acesso ao registros.php - OK!";
echo "<br>Usuário logado: " . ($_SESSION['nome'] ?? 'N/A');
echo "<br><a href='registros.php'>Ir para Registros</a>";
