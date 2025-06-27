<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

$config_file = 'especialidades_config.json';
$especialidades = [
   [
      'titulo' => 'Criação de Sites',
      'descricao' => 'Especialista em criação de sites modernos, responsivos e otimizados para resultados. Transformo ideias em soluções digitais com design atrativo, performance, segurança e foco na experiência do usuário.',
      'icone' => 'bi-code-slash',
      'cor_icone' => '#4f46e5',
      'cor_gradiente' => '#7c3aed',
      'usar_gradiente' => false,
      'cor_fundo' => 'transparent',
      'usar_blur' => false
   ],
   [
      'titulo' => 'Designer',
      'descricao' => 'Designer criativo e detalhista, com foco em identidade visual, estética funcional e comunicação impactante. Transformo conceitos em visuais únicos que conectam marcas ao seu público.',
      'icone' => 'bi-pencil-square',
      'cor_icone' => '#7c3aed',
      'cor_gradiente' => '#4f46e5',
      'usar_gradiente' => false,
      'cor_fundo' => 'transparent',
      'usar_blur' => false
   ],
   [
      'titulo' => 'WordPress',
      'descricao' => 'Designer especializado em WordPress, unindo criatividade e funcionalidade para criar sites visualmente marcantes, responsivos e fáceis de gerenciar. Transformo ideias em experiências digitais intuitivas e profissionais.',
      'icone' => 'bi-wordpress',
      'cor_icone' => '#059669',
      'cor_gradiente' => '#10b981',
      'usar_gradiente' => false,
      'cor_fundo' => 'transparent',
      'usar_blur' => false
   ]
];

if (file_exists($config_file)) {
   $especialidades = json_decode(file_get_contents($config_file), true) ?? $especialidades;
}

// Carregar configurações do painel
$config_file_painel = 'painel_config.json';
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
$config_painel = $default_config;
if (file_exists($config_file_painel)) {
   $config_painel = json_decode(file_get_contents($config_file_painel), true) ?? $default_config;
}

// Reordena o array de menus para colocar 'registros' logo após 'perfil'
$menus = $config_painel['menus'];
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
$config_painel['menus'] = $novo_menus;

// Mensagens de feedback
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($config_painel['titulo']); ?></title>
   <link rel="stylesheet" href="css/painel.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanilla-picker@2.11.1/dist/vanilla-picker.min.css" />
</head>

<body>
   <div class="admin-layout">
      <!-- Menu Lateral -->
      <div class="sidebar" style="background: <?php echo htmlspecialchars($config_painel['sidebar_color']); ?>;">
         <div class="sidebar-header">
            <div class="profile-section">
               <div class="profile-photo">
                  <img src="imgs/img_perfil.jpeg" alt="Foto de Perfil" id="profile-photo">
               </div>
               <h1><?php echo htmlspecialchars($config_painel['titulo']); ?></h1>
            </div>
         </div>
         <div class="sidebar-menu">
            <?php foreach ($config_painel['menus'] as $menu): if ($menu['id'] === 'sair') continue; ?>
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
                        } ?>" class="menu-item<?php echo $menu['id'] === 'editar_site' ? ' active' : ''; ?>">
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
            <h2>Gerenciar Especialidades</h2>
         </div>

         <div class="content-container">
            <div class="container">
               <?php if ($error): ?><div class="error-message"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
               <?php if ($success): ?><div class="success-message"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

               <form action="salvar_especialidades.php" method="POST" id="especialidadesForm">
                  <div id="especialidades-container">
                     <?php foreach ($especialidades as $index => $especialidade): ?>
                        <div class="especialidade-card" data-index="<?php echo $index; ?>">
                           <h3>
                              Especialidade <?php echo $index + 1; ?>
                              <button type="button" class="remove-especialidade" onclick="removerEspecialidade(this)">Remover</button>
                           </h3>

                           <div class="form-row">
                              <div class="form-group">
                                 <label>Título:</label>
                                 <input type="text" name="especialidades[<?php echo $index; ?>][titulo]" value="<?php echo htmlspecialchars($especialidade['titulo']); ?>" required>
                              </div>
                              <div class="form-group">
                                 <label>Ícone:</label>
                                 <div class="icon-selector">
                                    <div class="icon-preview" onclick="abrirPopupIcone(this)">
                                       <i class="<?php echo htmlspecialchars($especialidade['icone']); ?>" style="color: <?php echo htmlspecialchars($especialidade['cor_icone']); ?>;"></i>
                                       <span><?php echo htmlspecialchars($especialidade['icone']); ?></span>
                                    </div>
                                    <input type="hidden" name="especialidades[<?php echo $index; ?>][icone]" value="<?php echo htmlspecialchars($especialidade['icone']); ?>">
                                    <div class="icon-popup">
                                       <div class="icon-grid">
                                          <!-- Ícones Bootstrap serão inseridos via JavaScript -->
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>

                           <div class="form-row-4">
                              <div class="form-group">
                                 <label>Cor do Ícone:</label>
                                 <div class="color-picker">
                                    <input type="text" class="color-input" name="especialidades[<?php echo $index; ?>][cor_icone]" value="<?php echo htmlspecialchars($especialidade['cor_icone']); ?>" onchange="atualizarPreview(this)">
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label>Segunda Cor (Gradiente):</label>
                                 <div class="color-picker">
                                    <input type="text" class="color-input" name="especialidades[<?php echo $index; ?>][cor_gradiente]" value="<?php echo htmlspecialchars($especialidade['cor_gradiente'] ?? '#7c3aed'); ?>" onchange="atualizarPreview(this)">
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label>Cor de Fundo:</label>
                                 <div class="bg-color-picker">
                                    <input type="text" class="color-input" name="especialidades[<?php echo $index; ?>][cor_fundo]" value="<?php echo htmlspecialchars($especialidade['cor_fundo']); ?>" onchange="atualizarPreview(this)">
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label>Opções:</label>
                                 <div class="gradient-toggle">
                                    <span>Gradiente</span>
                                    <input type="checkbox" name="especialidades[<?php echo $index; ?>][usar_gradiente]" <?php echo $especialidade['usar_gradiente'] ? 'checked' : ''; ?> onchange="toggleGradiente(this)">
                                 </div>
                                 <div class="blur-toggle">
                                    <span>Blur</span>
                                    <input type="checkbox" name="especialidades[<?php echo $index; ?>][usar_blur]" <?php echo $especialidade['usar_blur'] ? 'checked' : ''; ?> onchange="atualizarPreview(this)">
                                 </div>
                              </div>
                           </div>

                           <div class="descricao-container">
                              <div class="descricao-field">
                                 <div class="form-group">
                                    <label>Descrição:</label>
                                    <textarea name="especialidades[<?php echo $index; ?>][descricao]" rows="4" required><?php echo htmlspecialchars($especialidade['descricao']); ?></textarea>
                                 </div>
                              </div>
                              <div class="preview-container">
                                 <div class="form-group">
                                    <label>Preview:</label>
                                    <div class="preview-card" style="background-color: <?php echo $especialidade['cor_fundo'] === 'transparent' ? 'transparent' : htmlspecialchars($especialidade['cor_fundo']); ?>; <?php echo $especialidade['usar_blur'] ? 'backdrop-filter: blur(10px);' : ''; ?>;">
                                       <i class="<?php echo htmlspecialchars($especialidade['icone']); ?>" style="<?php echo $especialidade['usar_gradiente'] ? 'background: linear-gradient(45deg, ' . htmlspecialchars($especialidade['cor_icone']) . ', ' . htmlspecialchars($especialidade['cor_gradiente']) . '); -webkit-background-clip: text; -webkit-text-fill-color: transparent;' : 'color: ' . htmlspecialchars($especialidade['cor_icone']) . ';'; ?>"></i>
                                       <h4><?php echo htmlspecialchars($especialidade['titulo']); ?></h4>
                                       <p><?php echo htmlspecialchars(substr($especialidade['descricao'], 0, 80)) . '...'; ?></p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php endforeach; ?>
                  </div>

                  <div class="btn-group">
                     <button type="button" class="btn btn-secondary" onclick="adicionarEspecialidade()">Adicionar Especialidade</button>
                     <button type="submit" class="btn">Salvar Alterações</button>
                     <a href="editar_site.php" class="btn btn-secondary">Voltar ao Editar Site</a>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/vanilla-picker@2.11.1/dist/vanilla-picker.min.js"></script>
   <script>
      // Lista de ícones Bootstrap populares
      const iconesBootstrap = [
         'bi-code-slash', 'bi-pencil-square', 'bi-wordpress', 'bi-palette', 'bi-laptop', 'bi-phone',
         'bi-gear', 'bi-star', 'bi-heart', 'bi-lightning', 'bi-fire', 'bi-rocket', 'bi-award',
         'bi-trophy', 'bi-gem', 'bi-diamond', 'bi-cpu', 'bi-motherboard', 'bi-display', 'bi-tablet',
         'bi-window', 'bi-browser-chrome', 'bi-browser-safari', 'bi-browser-edge', 'bi-browser-firefox',
         'bi-arrow-up-circle', 'bi-arrow-down-circle', 'bi-arrow-left-circle', 'bi-arrow-right-circle',
         'bi-check-circle', 'bi-x-circle', 'bi-exclamation-circle', 'bi-question-circle', 'bi-info-circle',
         'bi-plus-circle', 'bi-dash-circle', 'bi-x-lg', 'bi-plus-lg', 'bi-dash-lg', 'bi-asterisk',
         'bi-hash', 'bi-percent', 'bi-currency-dollar', 'bi-currency-euro', 'bi-currency-pound',
         'bi-currency-yen', 'bi-currency-bitcoin', 'bi-bank', 'bi-cash', 'bi-credit-card', 'bi-wallet',
         'bi-bag', 'bi-cart', 'bi-shop', 'bi-tag', 'bi-tags', 'bi-receipt', 'bi-receipt-cutoff',
         'bi-gift', 'bi-box', 'bi-box-seam', 'bi-box-arrow-up', 'bi-box-arrow-down', 'bi-box-arrow-left',
         'bi-box-arrow-right', 'bi-archive', 'bi-archive-fill', 'bi-inbox', 'bi-inbox-fill', 'bi-folder',
         'bi-folder-fill', 'bi-folder-plus', 'bi-folder-minus', 'bi-folder-x', 'bi-folder-check',
         'bi-file-earmark', 'bi-file-earmark-text', 'bi-file-earmark-image', 'bi-file-earmark-pdf',
         'bi-file-earmark-word', 'bi-file-earmark-excel', 'bi-file-earmark-powerpoint', 'bi-file-earmark-zip',
         'bi-file-earmark-music', 'bi-file-earmark-video', 'bi-file-earmark-code', 'bi-file-earmark-binary',
         'bi-file-earmark-break', 'bi-file-earmark-check', 'bi-file-earmark-minus', 'bi-file-earmark-plus',
         'bi-file-earmark-x', 'bi-file-earmark-ruled', 'bi-file-earmark-slides', 'bi-file-earmark-spreadsheet',
         'bi-file-earmark-person', 'bi-file-earmark-lock', 'bi-file-earmark-lock2', 'bi-file-earmark-shield',
         'bi-file-earmark-medical', 'bi-file-earmark-diff', 'bi-file-earmark-arrow-up', 'bi-file-earmark-arrow-down',
         'bi-file-earmark-arrow-left', 'bi-file-earmark-arrow-right', 'bi-file-earmark-bar-graph',
         'bi-file-earmark-easel', 'bi-file-earmark-font', 'bi-file-earmark-image', 'bi-file-earmark-lock2',
         'bi-file-earmark-medical', 'bi-file-earmark-minus', 'bi-file-earmark-music', 'bi-file-earmark-person',
         'bi-file-earmark-play', 'bi-file-earmark-plus', 'bi-file-earmark-post', 'bi-file-earmark-richtext',
         'bi-file-earmark-ruled', 'bi-file-earmark-slides', 'bi-file-earmark-spreadsheet', 'bi-file-earmark-text',
         'bi-file-earmark-word', 'bi-file-earmark-x', 'bi-file-earmark-zip', 'bi-file-earmark', 'bi-file',
         'bi-file-arrow-down', 'bi-file-arrow-up', 'bi-file-bar-graph', 'bi-file-binary', 'bi-file-break',
         'bi-file-check', 'bi-file-code', 'bi-file-diff', 'bi-file-earmark', 'bi-file-easel', 'bi-file-font',
         'bi-file-image', 'bi-file-lock', 'bi-file-lock2', 'bi-file-medical', 'bi-file-minus', 'bi-file-music',
         'bi-file-person', 'bi-file-play', 'bi-file-plus', 'bi-file-post', 'bi-file-richtext', 'bi-file-ruled',
         'bi-file-slides', 'bi-file-spreadsheet', 'bi-file-text', 'bi-file-word', 'bi-file-x', 'bi-file-zip'
      ];

      function abrirPopupIcone(elemento) {
         const popup = elemento.nextElementSibling.nextElementSibling;
         const grid = popup.querySelector('.icon-grid');

         // Limpa o grid
         grid.innerHTML = '';

         // Adiciona os ícones
         iconesBootstrap.forEach(icone => {
            const div = document.createElement('div');
            div.className = 'icon-option';
            div.innerHTML = `<i class="${icone}"></i>`;
            div.onclick = () => selecionarIcone(elemento, icone);
            grid.appendChild(div);
         });

         // Toggle do popup
         popup.classList.toggle('show');

         // Fecha outros popups
         document.querySelectorAll('.icon-popup').forEach(p => {
            if (p !== popup) p.classList.remove('show');
         });
      }

      function selecionarIcone(elemento, icone) {
         const preview = elemento.querySelector('i');
         const span = elemento.querySelector('span');
         const input = elemento.nextElementSibling;

         preview.className = icone;
         span.textContent = icone;
         input.value = icone;

         // Fecha o popup
         elemento.nextElementSibling.nextElementSibling.classList.remove('show');

         // Atualiza preview
         atualizarPreview(elemento.closest('.especialidade-card'));
      }

      function atualizarPreview(elemento) {
         const card = elemento.closest('.especialidade-card');
         const preview = card.querySelector('.preview-card i');
         const titulo = card.querySelector('input[name*="[titulo]"]').value;
         const descricao = card.querySelector('textarea').value;
         const corIcone = card.querySelector('input[name*="[cor_icone]"]').value;
         const corGradiente = card.querySelector('input[name*="[cor_gradiente]"]').value;
         const corFundo = card.querySelector('input[name*="[cor_fundo]"]').value;
         const usarGradiente = card.querySelector('input[name*="[usar_gradiente]"]').checked;
         const usarBlur = card.querySelector('input[name*="[usar_blur]"]').checked;

         // Aplica gradiente ou cor sólida
         if (usarGradiente) {
            preview.style.background = `linear-gradient(45deg, ${corIcone}, ${corGradiente})`;
            preview.style.webkitBackgroundClip = 'text';
            preview.style.webkitTextFillColor = 'transparent';
         } else {
            preview.style.background = '';
            preview.style.webkitBackgroundClip = '';
            preview.style.webkitTextFillColor = '';
            preview.style.color = corIcone;
         }

         // Aplica blur no fundo
         card.querySelector('.preview-card').style.backdropFilter = usarBlur ? 'blur(10px)' : '';
         card.querySelector('.preview-card').style.backgroundColor = corFundo === 'transparent' ? 'transparent' : corFundo;

         card.querySelector('.preview-card h4').textContent = titulo;
         card.querySelector('.preview-card p').textContent = descricao.substring(0, 80) + '...';
      }

      function toggleGradiente(checkbox) {
         const card = checkbox.closest('.especialidade-card');
         const preview = card.querySelector('.icon-preview i');
         const corIcone = card.querySelector('input[name*="[cor_icone]"]').value;
         const corGradiente = card.querySelector('input[name*="[cor_gradiente]"]').value;

         if (checkbox.checked) {
            preview.style.background = `linear-gradient(45deg, ${corIcone}, ${corGradiente})`;
            preview.style.webkitBackgroundClip = 'text';
            preview.style.webkitTextFillColor = 'transparent';
         } else {
            preview.style.background = '';
            preview.style.webkitBackgroundClip = '';
            preview.style.webkitTextFillColor = '';
            preview.style.color = corIcone;
         }

         atualizarPreview(card);
      }

      function adicionarEspecialidade() {
         const container = document.getElementById('especialidades-container');
         const index = container.children.length;

         const template = `
            <div class="especialidade-card" data-index="${index}">
               <h3>
                  Especialidade ${index + 1}
                  <button type="button" class="remove-especialidade" onclick="removerEspecialidade(this)">Remover</button>
               </h3>
               
               <div class="form-row">
                  <div class="form-group">
                     <label>Título:</label>
                     <input type="text" name="especialidades[${index}][titulo]" value="Nova Especialidade" required>
                  </div>
                  <div class="form-group">
                     <label>Ícone:</label>
                     <div class="icon-selector">
                        <div class="icon-preview" onclick="abrirPopupIcone(this)">
                           <i class="bi-star" style="color: #4f46e5;"></i>
                           <span>bi-star</span>
                        </div>
                        <input type="hidden" name="especialidades[${index}][icone]" value="bi-star">
                        <div class="icon-popup">
                           <div class="icon-grid"></div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="form-row-4">
                  <div class="form-group">
                     <label>Cor do Ícone:</label>
                     <div class="color-picker">
                        <input type="text" class="color-input" name="especialidades[${index}][cor_icone]" value="#4f46e5" onchange="atualizarPreview(this)">
                     </div>
                  </div>
                  <div class="form-group">
                     <label>Segunda Cor (Gradiente):</label>
                     <div class="color-picker">
                        <input type="text" class="color-input" name="especialidades[${index}][cor_gradiente]" value="#7c3aed" onchange="atualizarPreview(this)">
                     </div>
                  </div>
                  <div class="form-group">
                     <label>Cor de Fundo:</label>
                     <div class="bg-color-picker">
                        <input type="text" class="color-input" name="especialidades[${index}][cor_fundo]" value="#ffffff" onchange="atualizarPreview(this)">
                     </div>
                  </div>
                  <div class="form-group">
                     <label>Opções:</label>
                     <div class="gradient-toggle">
                        <span>Gradiente</span>
                        <input type="checkbox" name="especialidades[${index}][usar_gradiente]" onchange="toggleGradiente(this)">
                     </div>
                     <div class="blur-toggle">
                        <span>Blur</span>
                        <input type="checkbox" name="especialidades[${index}][usar_blur]" onchange="atualizarPreview(this)">
                     </div>
                  </div>
               </div>

               <div class="descricao-container">
                  <div class="descricao-field">
                     <div class="form-group">
                        <label>Descrição:</label>
                        <textarea name="especialidades[${index}][descricao]" rows="4" required>Descrição da nova especialidade...</textarea>
                     </div>
                  </div>
                  <div class="preview-container">
                     <div class="form-group">
                        <label>Preview:</label>
                        <div class="preview-card">
                           <i class="bi-star" style="color: #4f46e5;"></i>
                           <h4>Nova Especialidade</h4>
                           <p>Descrição da nova especialidade...</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         `;

         container.insertAdjacentHTML('beforeend', template);
         renumerarEspecialidades();
         initVanillaPicker();
      }

      function removerEspecialidade(botao) {
         if (document.querySelectorAll('.especialidade-card').length > 1) {
            botao.closest('.especialidade-card').remove();
            renumerarEspecialidades();
         } else {
            alert('Deve haver pelo menos uma especialidade!');
         }
      }

      function renumerarEspecialidades() {
         document.querySelectorAll('.especialidade-card').forEach((card, index) => {
            card.setAttribute('data-index', index);
            card.querySelector('h3').textContent = `Especialidade ${index + 1}`;

            // Atualiza os nomes dos campos
            card.querySelectorAll('input, textarea').forEach(input => {
               const name = input.getAttribute('name');
               if (name) {
                  input.setAttribute('name', name.replace(/especialidades\[\d+\]/, `especialidades[${index}]`));
               }
            });
         });
      }

      // Fecha popups quando clicar fora
      document.addEventListener('click', function(e) {
         if (!e.target.closest('.icon-selector')) {
            document.querySelectorAll('.icon-popup').forEach(popup => {
               popup.classList.remove('show');
            });
         }
      });

      function initVanillaPicker() {
         document.querySelectorAll('.color-input').forEach(function(input) {
            // Remove picker antigo se existir
            if (input._vanillaPicker) {
               input._vanillaPicker.destroy();
               input._vanillaPicker = null;
            }
            // Cria novo picker
            const picker = new Picker({
               parent: input.parentElement,
               popup: 'right',
               color: input.value,
               alpha: true,
               editor: true,
               onChange: function(color) {
                  if (color.rgba[3] === 0) {
                     input.value = 'transparent';
                  } else {
                     input.value = color.hex || color.rgbaString;
                  }
                  input.dispatchEvent(new Event('input', {
                     bubbles: true
                  }));
                  input.dispatchEvent(new Event('change', {
                     bubbles: true
                  }));
                  atualizarPreview(input);
               },
               onDone: function(color) {
                  picker.hide();
               }
            });
            // Abre picker ao clicar no input
            input.addEventListener('focus', function() {
               picker.show();
            });
            input._vanillaPicker = picker;
         });
      }
      window.addEventListener('DOMContentLoaded', initVanillaPicker);
   </script>
</body>

</html>