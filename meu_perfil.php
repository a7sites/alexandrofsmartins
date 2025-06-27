<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

$perfil_file = 'perfil_config.json';
$perfil = [
   'nome' => 'Alexandro Martins',
   'email' => 'alexandro@exemplo.com',
   'biografia' => 'Desenvolvedor web apaixonado por criar soluções digitais inovadoras e experiências únicas para meus clientes.',
   'telefone' => '(11) 99999-9999',
   'github' => 'https://github.com/alexandrofmartins',
   'linkedin' => 'https://linkedin.com/in/alexandrofmartins',
   'instagram' => 'https://instagram.com/alexandrofmartins',
   'facebook' => 'https://facebook.com/alexandrofmartins',
   'twitter' => 'https://twitter.com/alexandrofmartins',
   'youtube' => 'https://youtube.com/@alexandrofmartins',
   'foto_perfil' => 'imgs/img_perfil.jpeg'
];

if (file_exists($perfil_file)) {
   $perfil = json_decode(file_get_contents($perfil_file), true) ?? $perfil;
}

// Carregar configurações do painel
$config_file = 'painel_config.json';
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
$config = $default_config;
if (file_exists($config_file)) {
   $config = json_decode(file_get_contents($config_file), true) ?? $default_config;
}

// Reordena o array de menus para colocar 'registros' logo após 'perfil'
$menus = $config['menus'];
$novo_menus = [];
foreach ($menus as $menu) {
   if ($menu['id'] === 'dashboard') $novo_menus[] = $menu;
   if ($menu['id'] === 'perfil') {
      $novo_menus[] = $menu;
      // Após perfil, inserir registros
      foreach ($menus as $m2) {
         if ($m2['id'] === 'registros') $novo_menus[] = $m2;
      }
   }
}
// Adiciona os demais menus (exceto registros, já inserido)
foreach ($menus as $menu) {
   if ($menu['id'] !== 'dashboard' && $menu['id'] !== 'perfil' && $menu['id'] !== 'registros' && $menu['id'] !== 'sair') {
      $novo_menus[] = $menu;
   }
}
$config['menus'] = $novo_menus;

// Mensagens de feedback
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($config['titulo']); ?></title>
   <link rel="stylesheet" href="css/painel.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
   <style>
      /* Forçar visual padronizado nos campos de redes sociais */
      .campo_form {
         width: 100%;
         padding: 1rem;
         background: #fff;
         border-radius: 8px;
         color: #333;
         border: 2px solid #e1e5e9;
         outline: none;
         font-family: inherit;
         font-size: 1rem;
         font-weight: 400;
         margin-bottom: 0.5rem;
         box-sizing: border-box;
         transition: border 0.2s;
      }

      .campo_form:focus {
         border-color: #667eea;
         box-shadow: 0 0 5px rgba(124, 58, 237, 0.2);
      }

      .campo_form::placeholder {
         color: #888;
         opacity: 0.7;
         font-size: 0.97rem;
      }
   </style>
</head>

<body>
   <div class="admin-layout">
      <!-- Menu Lateral -->
      <div class="sidebar" style="background: <?php echo htmlspecialchars($config['sidebar_color']); ?>;">
         <div class="sidebar-header">
            <div class="profile-section">
               <div class="profile-photo">
                  <img src="<?php echo htmlspecialchars($perfil['foto_perfil']); ?>" alt="Foto de Perfil" id="profile-photo">
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
                           case 'ver_site':
                              echo 'index.php';
                              break;
                           case 'configuracoes':
                              echo 'configuracoes.php';
                              break;
                        } ?>" class="menu-item<?php echo $menu['id'] === 'perfil' ? ' active' : ''; ?>">
                  <i class="<?php echo htmlspecialchars($menu['icone']); ?>"></i>
                  <span><?php echo htmlspecialchars($menu['nome']); ?></span>
               </a>
            <?php endforeach; ?>
            <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
         </div>
      </div>

      <!-- Conteúdo Principal -->
      <div class="main-content">
         <div class="content-header">
            <h2>Meu Perfil</h2>
         </div>

         <div class="content-container">
            <div class="container">
               <?php if ($error): ?><div class="error-message"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
               <?php if ($success): ?><div class="success-message"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

               <form action="salvar_perfil.php" method="POST" enctype="multipart/form-data" id="perfilForm">
                  <div class="profile-edit-container">
                     <!-- Seção de Foto -->
                     <div class="profile-photo-section">
                        <div class="current-photo">
                           <img src="<?php echo htmlspecialchars($perfil['foto_perfil']); ?>" alt="Foto Atual" id="current-photo">
                        </div>
                        <div class="photo-upload">
                           <label for="foto_perfil">Alterar Foto de Perfil:</label>
                           <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" onchange="previewImage(this)">
                           <small>Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</small>
                        </div>
                     </div>

                     <!-- Informações Básicas -->
                     <div class="form-section">
                        <h3>Informações Básicas</h3>
                        <div class="form-row">
                           <div class="form-group">
                              <label for="nome">Nome de Usuário:</label>
                              <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($perfil['nome']); ?>" required class="form-control">
                           </div>
                           <div class="form-group">
                              <label for="email">E-mail de Cadastro:</label>
                              <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($perfil['email']); ?>" required class="form-control">
                           </div>
                        </div>
                        <div class="form-row">
                           <div class="form-group">
                              <label for="senha_atual">Senha Atual:</label>
                              <input type="password" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual" class="form-control">
                           </div>
                           <div class="form-group">
                              <label for="nova_senha">Nova Senha:</label>
                              <input type="password" id="nova_senha" name="nova_senha" placeholder="Deixe em branco para não alterar" class="form-control">
                           </div>
                        </div>
                        <div class="form-row">
                           <div class="form-group">
                              <label for="telefone">Telefone de Contato:</label>
                              <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($perfil['telefone']); ?>" placeholder="(11) 99999-9999" maxlength="15">
                           </div>
                           <div class="form-group">
                              <label for="biografia">Biografia:</label>
                              <textarea id="biografia" name="biografia" rows="4" maxlength="320" placeholder="Conte um pouco sobre você..."><?php echo htmlspecialchars($perfil['biografia']); ?></textarea>
                              <small><span id="char-count">0</span>/320 caracteres</small>
                           </div>
                        </div>
                     </div>

                     <!-- Redes Sociais -->
                     <div class="form-section">
                        <h3>Redes Sociais</h3>
                        <div class="form-row">
                           <div class="form-group">
                              <label for="github">GitHub:</label>
                              <input type="url" id="github" name="github" value="<?php echo htmlspecialchars($perfil['github']); ?>" placeholder="https://github.com/seuusuario" class="campo_form">
                           </div>
                           <div class="form-group">
                              <label for="linkedin">LinkedIn:</label>
                              <input type="url" id="linkedin" name="linkedin" value="<?php echo htmlspecialchars($perfil['linkedin']); ?>" placeholder="https://linkedin.com/in/seuusuario" class="campo_form">
                           </div>
                        </div>
                        <div class="form-row">
                           <div class="form-group">
                              <label for="instagram">Instagram:</label>
                              <input type="url" id="instagram" name="instagram" value="<?php echo htmlspecialchars($perfil['instagram']); ?>" placeholder="https://instagram.com/seuusuario" class="campo_form">
                           </div>
                           <div class="form-group">
                              <label for="facebook">Facebook:</label>
                              <input type="url" id="facebook" name="facebook" value="<?php echo htmlspecialchars($perfil['facebook']); ?>" placeholder="https://facebook.com/seuusuario" class="campo_form">
                           </div>
                        </div>
                        <div class="form-row">
                           <div class="form-group">
                              <label for="twitter">Twitter:</label>
                              <input type="url" id="twitter" name="twitter" value="<?php echo htmlspecialchars($perfil['twitter']); ?>" placeholder="https://twitter.com/seuusuario" class="campo_form">
                           </div>
                           <div class="form-group">
                              <label for="youtube">YouTube:</label>
                              <input type="url" id="youtube" name="youtube" value="<?php echo htmlspecialchars($perfil['youtube']); ?>" placeholder="https://youtube.com/@seucanal" class="campo_form">
                           </div>
                        </div>
                     </div>

                     <div class="btn-group">
                        <button type="submit" class="btn">Salvar Alterações</button>
                        <a href="painel.php" class="btn btn-secondary">Voltar ao Dashboard</a>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>

   <script>
      // Contador de caracteres para biografia
      document.getElementById('biografia').addEventListener('input', function() {
         const maxLength = 320;
         const currentLength = this.value.length;
         const charCount = document.getElementById('char-count');

         charCount.textContent = currentLength;

         if (currentLength > maxLength) {
            charCount.style.color = '#ff6b6b';
         } else {
            charCount.style.color = '#666';
         }
      });

      // Preview da imagem
      function previewImage(input) {
         if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
               document.getElementById('current-photo').src = e.target.result;
               document.getElementById('profile-photo').src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
         }
      }

      // Inicializar contador de caracteres
      document.addEventListener('DOMContentLoaded', function() {
         const biografia = document.getElementById('biografia');
         const charCount = document.getElementById('char-count');
         charCount.textContent = biografia.value.length;
      });

      // Máscara de telefone
      document.getElementById('telefone').addEventListener('input', function(e) {
         let v = this.value.replace(/\D/g, '');
         if (v.length > 11) v = v.slice(0, 11);
         if (v.length > 0) v = '(' + v;
         if (v.length > 3) v = v.slice(0, 3) + ') ' + v.slice(3);
         if (v.length > 10) v = v.slice(0, 10) + '-' + v.slice(10);
         else if (v.length > 6) v = v.slice(0, 9) + '-' + v.slice(9);
         this.value = v;
      });
   </script>
</body>

</html>