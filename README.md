# 🚀 Painel Administrativo - Alexandro F. S. Martins

Sistema completo de painel administrativo em PHP com integração WhatsApp, gerenciamento de usuários, configurações dinâmicas e interface moderna.

## 📋 Funcionalidades

- **Painel Administrativo Moderno**: Interface responsiva com menu lateral dinâmico
- **Sistema de Login Seguro**: Autenticação com hash bcrypt e sessões
- **Gerenciamento de Usuários**: Cadastro, edição, exclusão e visualização
- **Integração WhatsApp**: Formulário de contato com salvamento automático
- **Registros de Contatos**: Visualização e gerenciamento de contatos recebidos
- **Configurações Dinâmicas**: Personalização de cores, menus e timezone
- **Gerenciamento de Especialidades**: Sistema de abas com preview em tempo real
- **Edição de Site**: Configuração de informações do site
- **Alertas Visuais**: Sistema de notificações elegante
- **Menu Lateral Responsivo**: Com opção de encolhimento
- **Timezone Configurável**: Suporte a diferentes fusos horários

## 🛠️ Tecnologias

- **Backend**: PHP 8.3
- **Frontend**: HTML5, CSS3, JavaScript
- **Ícones**: Font Awesome 6.0, Bootstrap Icons
- **Armazenamento**: JSON (usuários, configurações, contatos)
- **Segurança**: Hash bcrypt, validação de sessões

## 📁 Estrutura de Arquivos

```
alexandrofsmartins/
├── .git/                           # Controle de versão Git
├── .htaccess                       # Configurações de segurança Apache
├── README.md                       # Documentação do projeto
├── admin.php                       # Página de login administrativo
├── cadastrar.php                   # Cadastro de novos usuários
├── configuracoes.php               # Configurações do painel
├── contatos_whatsapp.json          # Arquivo de contatos (legado)
├── corrigir_senha.php              # Correção de senhas
├── css/
│   ├── efects.css                  # Efeitos visuais
│   ├── painel.css                  # Estilos do painel administrativo
│   └── style.css                   # Estilos do site principal
├── debug_menu.php                  # Debug do sistema de menus
├── desconectar_usuario.php         # Desconectar usuário específico
├── editar_contato.php              # Edição de contatos WhatsApp
├── editar_site.php                 # Edição de informações do site
├── editar_usuario.php              # Edição de usuários
├── especialidades_config.json      # Configurações de especialidades
├── excluir_contato.php             # Exclusão de contatos
├── excluir_usuario.php             # Exclusão de usuários
├── gerar_hash.php                  # Geração de hash para senhas
├── gerenciar_especialidades.php    # Gerenciamento de especialidades
├── imgs/
│   ├── a7site.svg                  # Logo A7 Sites
│   ├── bg_0.jpg                    # Imagem de fundo alternativa
│   ├── bg.jpg                      # Imagem de fundo principal
│   ├── bg.webp                     # Imagem de fundo otimizada
│   ├── favicon.png                 # Ícone do site
│   └── img_perfil.jpeg             # Foto de perfil padrão
├── index.php                       # Página principal do site
├── js/
│   └── script.js                   # JavaScript do site
├── login.php                       # Processamento de login
├── logout.php                      # Encerramento de sessão
├── meu_perfil.php                  # Edição de perfil do usuário
├── painel.php                      # Dashboard principal
├── painel_config.json              # Configurações do painel
├── processar_contato.php           # Processamento de contatos
├── registros.php                   # Visualização de registros
├── salvar_configuracoes.php        # Salvamento de configurações
├── salvar_edicao_usuario.php       # Salvamento de edição de usuário
├── salvar_especialidades.php       # Salvamento de especialidades
├── salvar_perfil.php               # Salvamento de perfil
├── salvar_site.php                 # Salvamento de informações do site
├── salvar_usuario.php              # Salvamento de usuários
├── site_config.json                # Configurações do site
├── src/                            # Diretório de recursos (vazio)
├── teste_contato.html              # Teste do formulário de contato
├── teste_registros.php             # Teste do sistema de registros
├── teste_simples.php               # Teste simples do sistema
├── usuarios.json                   # Armazenamento de usuários
├── visualizar_usuarios.php         # Listagem de usuários
└── whatsapp.json                   # Contatos do WhatsApp
```

## 🚀 Instalação

1. Clone o repositório:

```bash
git clone https://github.com/a7sites/alexandrofsmartins.git
```

2. Configure o servidor web (Apache/Nginx) para apontar para o diretório do projeto

3. Acesse `admin.php` para fazer o primeiro login

4. Configure as opções do painel em "Configurações"

## 🔧 Configuração

### Primeiro Acesso

- URL: `admin.php`
- Usuário padrão: `admin`
- Senha padrão: `admin123`

### Configurações do Painel

- Título personalizável
- Cor da barra lateral
- Modo encolhido da sidebar
- Timezone configurável
- Menus editáveis com ícones

## 📱 Funcionalidades Principais

### Dashboard

- Estatísticas em tempo real
- Acesso rápido às funcionalidades
- Informações do sistema

### Gerenciamento de Usuários

- Cadastro com validação
- Edição de dados
- Exclusão segura
- Desconexão remota
- Controle de último login

### Integração WhatsApp

- Formulário de contato no site
- Salvamento automático em JSON
- Visualização no painel
- Edição e exclusão de contatos
- Chamada direta via WhatsApp

### Configurações

- Personalização visual
- Gerenciamento de menus
- Configuração de timezone
- Modo responsivo

## 🔒 Segurança

- Senhas criptografadas com bcrypt
- Validação de sessões
- Proteção contra acesso não autorizado
- Sanitização de dados
- Headers de segurança

## 📝 Alterações Recentes

- Corrigido o encolhimento da barra lateral: agora a classe `shrink` é aplicada corretamente na `<div class="sidebar">` em todas as páginas do painel.
- Menu "Configurações" restaurado e garantido que aparece corretamente, mesmo com a sidebar encolhida.
- Ajustada a ordem e os IDs dos menus no `painel_config.json` para evitar sumiço de menus e garantir navegação correta.
- Sidebar agora funciona de forma consistente em todas as páginas do painel.

## 👨‍💻 Desenvolvimento

### Estrutura de Dados

- **usuarios.json**: Armazena usuários e senhas criptografadas
- **whatsapp.json**: Contatos recebidos via formulário
- **painel_config.json**: Configurações do painel administrativo
- **site_config.json**: Configurações do site principal
- **especialidades_config.json**: Configurações de especialidades

### Arquivos Principais

- **painel.php**: Dashboard principal
- **registros.php**: Gerenciamento de contatos
- **configuracoes.php**: Configurações do sistema
- **index.php**: Site principal com formulário de contato

## 📞 Suporte

Para suporte técnico ou dúvidas sobre o sistema, entre em contato através do painel administrativo.

---

**Desenvolvido com ❤️ por Alexandro F. S. Martins - 2025**
