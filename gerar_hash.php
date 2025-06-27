<?php
// Script para gerar o hash correto da senha admin123
$senha = 'admin123';
$hash = password_hash($senha, PASSWORD_DEFAULT);

echo "Senha: $senha<br>";
echo "Hash gerado: $hash<br><br>";

// Teste de verificação
if (password_verify($senha, $hash)) {
   echo "✅ Hash válido - Senha 'admin123' funcionará!<br>";
} else {
   echo "❌ Hash inválido<br>";
}

echo "<br>Atualizando o arquivo usuarios.json...<br>";

// Carrega o arquivo atual
$usuarios = [
   'admin' => [
      'senha' => $hash,
      'nome' => 'Administrador',
      'email' => 'admin@exemplo.com',
      'data_criacao' => date('Y-m-d H:i:s'),
      'criado_por' => 'sistema'
   ]
];

// Salva o arquivo atualizado
$resultado = file_put_contents('usuarios.json', json_encode($usuarios, JSON_PRETTY_PRINT));

if ($resultado !== false) {
   echo "✅ Arquivo usuarios.json atualizado com sucesso!<br>";
   echo "Agora você pode fazer login com:<br>";
   echo "Usuário: admin<br>";
   echo "Senha: admin123<br>";
} else {
   echo "❌ Erro ao atualizar o arquivo<br>";
}

echo "<br><a href='admin.php'>Ir para o login</a>";
