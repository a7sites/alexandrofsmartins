<?php
// Script para alterar senha do admin2
// Nova senha neutra
$nova_senha = "admin123456";

// Carregar usuários
$usuarios_file = 'usuarios.json';
$usuarios = json_decode(file_get_contents($usuarios_file), true) ?? [];

// Verificar se admin2 existe
if (!isset($usuarios['admin2'])) {
   echo "❌ Usuário 'admin2' não encontrado!";
   exit;
}

// Gerar hash da nova senha
$senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

// Atualizar senha
$usuarios['admin2']['senha'] = $senha_hash;

// Salvar no arquivo
if (file_put_contents($usuarios_file, json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
   echo "✅ Senha do usuário 'admin2' alterada com sucesso!<br>";
   echo "🔑 Nova senha: <strong>$nova_senha</strong><br>";
   echo "📧 Email: " . $usuarios['admin2']['email'] . "<br>";
   echo "👤 Nome: " . $usuarios['admin2']['nome'] . "<br>";
   echo "<br>⚠️ <strong>IMPORTANTE:</strong> Guarde esta senha em local seguro!";
} else {
   echo "❌ Erro ao salvar a nova senha!";
}
