# 🔐 Painel Administrativo

Sistema de administração completo com autenticação segura e gerenciamento de usuários.

## 📁 Estrutura de Arquivos

```
/desenvolvedor/
├── admin.php          ← Formulário de login
├── login.php          ← Valida o login e inicia a sessão
├── painel.php         ← Área protegida principal
├── logout.php         ← Encerra a sessão
├── usuarios.json      ← Armazena usuários e senhas criptografadas
├── cadastrar.php      ← Página para cadastrar novos usuários
├── salvar_usuario.php ← Processa o cadastro e salva no JSON
├── visualizar_usuarios.php ← Lista todos os usuários
├── .htaccess          ← Configurações de segurança
└── README.md          ← Este arquivo
```

## 🚀 Instalação

1. **Certifique-se de ter PHP instalado** (versão 7.4 ou superior)
2. **Configure um servidor web** (Apache, Nginx, ou servidor local como XAMPP/Laragon)
3. **Coloque os arquivos** na pasta do seu servidor web
4. **Acesse** `http://seudominio.com/admin.php`

## 🔑 Credenciais Padrão

- **Usuário:** `admin`
- **Senha:** `admin123`

⚠️ **IMPORTANTE:** Altere essas credenciais após o primeiro login!

## 🛡️ Recursos de Segurança

### Implementados:

- ✅ Senhas criptografadas com `password_hash()`
- ✅ Validação de sessão em todas as páginas protegidas
- ✅ Proteção contra listagem de diretórios
- ✅ Headers de segurança HTTP
- ✅ Validação de entrada de dados
- ✅ Proteção do arquivo `usuarios.json`
- ✅ Logout seguro

### Validações de Senha:

- Mínimo 6 caracteres
- Pelo menos uma letra maiúscula
- Pelo menos um número

## 📋 Funcionalidades

### 🔐 Autenticação

- Login seguro com validação
- Sessões PHP protegidas
- Logout automático

### 👥 Gerenciamento de Usuários

- Cadastro de novos usuários
- Visualização de todos os usuários
- Validação de e-mail único
- Histórico de criação

### 📊 Dashboard

- Estatísticas em tempo real
- Interface responsiva
- Navegação intuitiva

## 🔧 Configuração

### Para produção:

1. **Altere as credenciais padrão**
2. **Configure HTTPS** (descomente as linhas no .htaccess)
3. **Ajuste as permissões** dos arquivos:
   ```bash
   chmod 644 *.php *.html
   chmod 600 usuarios.json
   chmod 644 .htaccess
   ```

### Personalização:

- Edite os arquivos CSS inline para personalizar o visual
- Modifique as validações em `salvar_usuario.php`
- Ajuste as configurações de sessão no `.htaccess`

## 🚨 Troubleshooting

### Problema: "Erro ao salvar usuário"

**Solução:** Verifique as permissões da pasta e do arquivo `usuarios.json`

### Problema: "Sessão não inicia"

**Solução:** Verifique se o PHP tem permissão para escrever na pasta de sessões

### Problema: "Página não encontrada"

**Solução:** Verifique se o mod_rewrite está habilitado no Apache

## 📞 Suporte

Para dúvidas ou problemas:

1. Verifique os logs de erro do PHP
2. Confirme as permissões dos arquivos
3. Teste em um servidor local primeiro

## 🔄 Atualizações

Para atualizar o sistema:

1. Faça backup dos arquivos atuais
2. Substitua os arquivos pelos novos
3. Mantenha o arquivo `usuarios.json` existente
4. Teste todas as funcionalidades

---

**Desenvolvido com segurança e boas práticas** 🔒
