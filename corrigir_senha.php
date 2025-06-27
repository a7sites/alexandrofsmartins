<?php
// Script para corrigir a senha do admin
$senha = 'admin123';
$hash = password_hash($senha, PASSWORD_DEFAULT);

// Verifica se o hash está correto
if (password_verify($senha, $hash)) {
   echo "✅ Hash gerado corretamente para a senha: $senha<br>";
   echo "Hash: $hash<br><br>";

   // Atualiza o arquivo usuarios.json
   $usuarios = [
      'admin' => [
         'senha' => $hash,
         'nome' => 'Administrador',
         'email' => 'admin@exemplo.com',
         'data_criacao' => date('Y-m-d H:i:s'),
         'criado_por' => 'sistema'
      ]
   ];

   $resultado = file_put_contents('usuarios.json', json_encode($usuarios, JSON_PRETTY_PRINT));

   if ($resultado !== false) {
      echo "✅ Arquivo usuarios.json atualizado com sucesso!<br>";
      echo "<br><strong>Credenciais de acesso:</strong><br>";
      echo "Usuário: <strong>admin</strong><br>";
      echo "Senha: <strong>admin123</strong><br>";
      echo "<br><a href='admin.php' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Fazer Login</a>";
   } else {
      echo "❌ Erro ao atualizar o arquivo usuarios.json";
   }
} else {
   echo "❌ Erro ao gerar o hash da senha";
}
