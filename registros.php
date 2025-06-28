<?php
session_start();

// Debug temporário - remover depois
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica se está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

// Debug - verificar se chegou até aqui
echo "<!-- Debug: Arquivo registros.php carregado com sucesso -->";

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
      ['id' => 'registros', 'nome' => 'Registros', 'icone' => 'fas fa-phone'],
      ['id' => 'ver_site', 'nome' => 'Ver Site', 'icone' => 'fas fa-home'],
      ['id' => 'configuracoes', 'nome' => 'Configurações', 'icone' => 'fas fa-cog'],
      ['id' => 'sair', 'nome' => 'Sair', 'icone' => 'fas fa-sign-out-alt'],
   ]
];
$config = $default_config;
if (file_exists($config_file)) {
   $config = json_decode(file_get_contents($config_file), true) ?? $default_config;
}

// Debug - verificar se config foi carregada
echo "<!-- Debug: Config carregada: " . ($config ? 'SIM' : 'NÃO') . " -->";

// Definir timezone do painel
if (!empty($config['timezone'])) {
   date_default_timezone_set($config['timezone']);
}

// Carregar registros de contatos
$contatos_file = 'whatsapp.json';
$contatos = [];
if (file_exists($contatos_file)) {
   $contatos = json_decode(file_get_contents($contatos_file), true) ?? [];
}

// Ordenar por data mais recente
usort($contatos, function ($a, $b) {
   return strtotime($b['data_hora']) - strtotime($a['data_hora']);
});

$nome_usuario = $_SESSION['nome'] ?? 'Usuário';
$usuario = $_SESSION['usuario'] ?? '';
$data_login = $_SESSION['data_login'] ?? '';

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
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($config['titulo']); ?> - Registros</title>
   <link rel="stylesheet" href="css/painel.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <style>
      .contatos-table {
         background: white;
         border-radius: 10px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
         overflow: hidden;
         margin-bottom: 2rem;
      }

      .contatos-table table {
         width: 100%;
         border-collapse: collapse;
      }

      .contatos-table th,
      .contatos-table td {
         padding: 15px;
         text-align: left;
         border-bottom: 1px solid #e1e5e9;
      }

      .contatos-table th {
         background: #f8f9fa;
         font-weight: 600;
         color: #333;
         font-size: 0.95rem;
      }

      .contatos-table tr:hover {
         background: #f8f9fa;
      }

      .contatos-table tr:last-child td {
         border-bottom: none;
      }

      .btn-whatsapp {
         background: #25d366;
         color: white;
         border: none;
         border-radius: 6px;
         padding: 8px 12px;
         cursor: pointer;
         text-decoration: none;
         display: inline-flex;
         align-items: center;
         gap: 5px;
         font-size: 0.9rem;
         transition: background 0.2s;
      }

      .btn-whatsapp:hover {
         background: #128c7e;
         color: white;
      }

      .btn-edit {
         background: #667eea;
         color: white;
         border: none;
         border-radius: 6px;
         padding: 8px 12px;
         cursor: pointer;
         text-decoration: none;
         display: inline-flex;
         align-items: center;
         gap: 5px;
         font-size: 0.9rem;
         transition: background 0.2s;
      }

      .btn-edit:hover {
         background: #5a6fd8;
         color: white;
      }

      .btn-delete {
         background: #ff6b6b;
         color: white;
         border: none;
         border-radius: 6px;
         padding: 8px 12px;
         cursor: pointer;
         text-decoration: none;
         display: inline-flex;
         align-items: center;
         gap: 5px;
         font-size: 0.9rem;
         transition: background 0.2s;
      }

      .btn-delete:hover {
         background: #ff5252;
         color: white;
      }

      .contato-info {
         position: relative;
         cursor: help;
      }

      .contato-info:hover::after {
         content: attr(data-tooltip);
         position: absolute;
         bottom: 100%;
         left: 50%;
         transform: translateX(-50%);
         background: #333;
         color: white;
         padding: 8px 12px;
         border-radius: 6px;
         font-size: 0.85rem;
         white-space: nowrap;
         z-index: 1000;
         margin-bottom: 5px;
      }

      .contato-info:hover::before {
         content: '';
         position: absolute;
         bottom: 100%;
         left: 50%;
         transform: translateX(-50%);
         border: 5px solid transparent;
         border-top-color: #333;
         margin-bottom: -5px;
      }

      .empty-state {
         text-align: center;
         padding: 3rem;
         color: #666;
      }

      .empty-state i {
         font-size: 3rem;
         color: #ddd;
         margin-bottom: 1rem;
      }

      .stats-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 2rem;
      }

      .total-contatos {
         background: #667eea;
         color: white;
         padding: 1rem 1.5rem;
         border-radius: 10px;
         font-weight: 600;
      }
   </style>
</head>

<body>
   <div class="admin-layout">
      <!-- Menu Lateral -->
      <div class="sidebar<?php echo !empty($config['shrink_sidebar']) ? ' shrink' : ''; ?>" style="background: <?php echo htmlspecialchars($config['sidebar_color']); ?>;">
         <div class="sidebar-header">
            <div class="profile-section">
               <div class="profile-photo">
                  <img src="imgs/img_perfil.jpeg" alt="Foto de Perfil" id="profile-photo">
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
                        } ?>" class="menu-item<?php echo $menu['id'] === 'registros' ? ' active' : ''; ?>">
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
            <h2>📞 Registros de Contatos WhatsApp</h2>
         </div>

         <div class="content-container">
            <div class="stats-header">
               <div class="total-contatos">
                  <i class="fas fa-phone"></i> Total de Contatos: <?php echo count($contatos); ?>
               </div>
               <div id="alerta-painel" style="display:none; margin-left: 1rem;"></div>
            </div>

            <?php if (empty($contatos)): ?>
               <div class="empty-state">
                  <i class="fas fa-phone-slash"></i>
                  <h3>Nenhum contato registrado</h3>
                  <p>Ainda não há registros de contatos do WhatsApp.</p>
               </div>
            <?php else: ?>
               <div class="contatos-table">
                  <table>
                     <thead>
                        <tr>
                           <th>Nome</th>
                           <th>WhatsApp</th>
                           <th>Data e Hora</th>
                           <th>Último Contato</th>
                           <th>Ações</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($contatos as $index => $contato): ?>
                           <tr>
                              <td>
                                 <strong><?php echo htmlspecialchars($contato['nome']); ?></strong>
                              </td>
                              <td>
                                 <span class="contato-info" data-tooltip="Clique para copiar">
                                    <?php
                                    // Formatar WhatsApp para (99) 9 9999-9999
                                    $w = preg_replace('/\D/', '', $contato['whatsapp']);
                                    if (strlen($w) === 11) {
                                       $w_formatado = sprintf('(%s) %s %s-%s', substr($w, 0, 2), substr($w, 2, 1), substr($w, 3, 4), substr($w, 7, 4));
                                    } elseif (strlen($w) === 10) {
                                       $w_formatado = sprintf('(%s) %s-%s', substr($w, 0, 2), substr($w, 2, 4), substr($w, 6, 4));
                                    } else {
                                       $w_formatado = $contato['whatsapp'];
                                    }
                                    echo htmlspecialchars($w_formatado);
                                    ?>
                                 </span>
                              </td>
                              <td>
                                 <?php
                                 $data = new DateTime($contato['data_hora']);
                                 echo $data->format('d/m/Y H:i');
                                 ?>
                              </td>
                              <td>
                                 <span class="contato-info" data-tooltip="Último contato: <?php echo $data->format('d/m/Y H:i'); ?>">
                                    <i class="fas fa-clock"></i>
                                    <?php
                                    $agora = new DateTime();
                                    $diferenca = $agora->diff($data);
                                    if ($diferenca->days > 0) {
                                       echo $diferenca->days . ' dia' . ($diferenca->days > 1 ? 's' : '') . ' atrás';
                                    } elseif ($diferenca->h > 0) {
                                       echo $diferenca->h . ' hora' . ($diferenca->h > 1 ? 's' : '') . ' atrás';
                                    } elseif ($diferenca->i > 0) {
                                       echo $diferenca->i . ' minuto' . ($diferenca->i > 1 ? 's' : '') . ' atrás';
                                    } else {
                                       echo 'Agora mesmo';
                                    }
                                    ?>
                                 </span>
                              </td>
                              <td>
                                 <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $contato['whatsapp']); ?>"
                                       target="_blank"
                                       class="btn-whatsapp"
                                       title="Chamar no WhatsApp">
                                       <i class="fab fa-whatsapp"></i> Chamar
                                    </a>
                                    <button onclick="editarContato('<?php echo htmlspecialchars($contato['whatsapp']); ?>')"
                                       class="btn-edit"
                                       title="Editar contato">
                                       <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button onclick="excluirContato('<?php echo htmlspecialchars($contato['whatsapp']); ?>')"
                                       class="btn-delete"
                                       title="Excluir contato">
                                       <i class="fas fa-trash"></i> Excluir
                                    </button>
                                 </div>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>
            <?php endif; ?>
         </div>
      </div>
   </div>

   <script>
      function mostrarAlertaPainel(msg, tipo = 'success') {
         const alerta = document.getElementById('alerta-painel');
         alerta.innerHTML = `<div style="padding: 12px 24px; border-radius: 8px; font-weight: 500; background: ${tipo === 'success' ? '#25d366' : '#ff6b6b'}; color: white; display: flex; align-items: center; gap: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); min-width: 220px;">${tipo === 'success' ? '<i class=\'fas fa-check-circle\'></i>' : '<i class=\'fas fa-exclamation-circle\'></i>'} <span>${msg}</span></div>`;
         alerta.style.display = 'block';
         clearTimeout(alerta._timeout);
         alerta._timeout = setTimeout(() => {
            alerta.style.display = 'none';
         }, 4000);
      }

      function editarContato(whatsapp) {
         const contatos = <?php echo json_encode($contatos); ?>;
         const contato = contatos.find(c => c.whatsapp === whatsapp);
         if (!contato) {
            mostrarAlertaPainel('Contato não encontrado!', 'error');
            return;
         }
         // Formatar WhatsApp para exibir no input
         let w = contato.whatsapp.replace(/\D/g, '');
         let w_formatado = w;
         if (w.length === 11) {
            w_formatado = `(${w.substr(0,2)}) ${w.substr(2,1)} ${w.substr(3,4)}-${w.substr(7,4)}`;
         } else if (w.length === 10) {
            w_formatado = `(${w.substr(0,2)}) ${w.substr(2,4)}-${w.substr(6,4)}`;
         }
         // Criar modal de edição
         const modal = document.createElement('div');
         modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
         `;
         modal.innerHTML = `
            <div style="background: white; padding: 2rem; border-radius: 10px; width: 90%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
               <h3 style="margin-bottom: 1.5rem; color: #333;">Editar Contato</h3>
               <div style="margin-bottom: 1rem;">
                  <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Nome:</label>
                  <input type="text" id="edit-nome" value="${contato.nome}" style="width: 100%; padding: 10px; border: 2px solid #e1e5e9; border-radius: 6px; font-size: 1rem;">
               </div>
               <div style="margin-bottom: 1.5rem;">
                  <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">WhatsApp:</label>
                  <input type="text" id="edit-whatsapp" value="${w_formatado}" style="width: 100%; padding: 10px; border: 2px solid #e1e5e9; border-radius: 6px; font-size: 1rem;">
               </div>
               <div style="display: flex; gap: 10px; justify-content: flex-end;">
                  <button onclick="fecharModal()" style="padding: 10px 20px; border: 2px solid #e1e5e9; background: white; border-radius: 6px; cursor: pointer;">Cancelar</button>
                  <button onclick="salvarEdicao('${whatsapp}')" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer;">Salvar</button>
               </div>
            </div>
         `;
         document.body.appendChild(modal);
         // Máscara no campo de edição
         setTimeout(() => {
            const input = document.getElementById('edit-whatsapp');
            if (input) {
               input.addEventListener('input', function(e) {
                  let v = this.value.replace(/\D/g, '');
                  v = v.replace(/^0/, '');
                  if (v.length > 11) v = v.slice(0, 11);
                  if (v.length > 10) {
                     v = v.replace(/^(\d{2})(\d{1})(\d{4})(\d{4}).*/, '($1) $2 $3-$4');
                  } else if (v.length > 6) {
                     v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
                  } else if (v.length > 2) {
                     v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
                  } else {
                     v = v.replace(/^(\d*)/, '($1');
                  }
                  this.value = v;
               });
            }
         }, 100);
      }

      function fecharModal() {
         const modal = document.querySelector('div[style*="position: fixed"]');
         if (modal) {
            modal.remove();
         }
      }

      function salvarEdicao(whatsappAntigo) {
         const nome = document.getElementById('edit-nome').value.trim();
         const whatsapp = document.getElementById('edit-whatsapp').value.replace(/\D/g, '');
         if (!nome || !whatsapp) {
            mostrarAlertaPainel('Nome e WhatsApp são obrigatórios!', 'error');
            return;
         }
         const dados = new FormData();
         dados.append('whatsapp_antigo', whatsappAntigo);
         dados.append('nome', nome);
         dados.append('whatsapp', whatsapp);
         fetch('editar_contato.php', {
               method: 'POST',
               body: dados
            })
            .then(response => response.json())
            .then(data => {
               if (data.success) {
                  mostrarAlertaPainel('Contato atualizado com sucesso!', 'success');
                  setTimeout(() => location.reload(), 1200);
               } else {
                  mostrarAlertaPainel('Erro ao atualizar contato: ' + data.message, 'error');
               }
            })
            .catch(error => {
               mostrarAlertaPainel('Erro ao atualizar contato: ' + error.message, 'error');
            });
      }

      function excluirContato(whatsapp) {
         // Criar modal de confirmação customizado
         if (document.getElementById('modal-confirmar-excluir')) return;
         const modal = document.createElement('div');
         modal.id = 'modal-confirmar-excluir';
         modal.style.cssText = `
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.25); z-index: 20000;
            display: flex; align-items: center; justify-content: center;`;
         modal.innerHTML = `
            <div style="background: white; padding: 2rem 2.5rem; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.18); min-width: 320px; display: flex; flex-direction: column; align-items: center;">
               <div style='font-size: 2.2rem; color: #ff6b6b; margin-bottom: 1rem;'><i class="fas fa-exclamation-triangle"></i></div>
               <div style="font-size: 1.1rem; font-weight: 500; margin-bottom: 2rem; text-align: center;">Tem certeza que deseja excluir este contato?</div>
               <div style="display: flex; gap: 18px;">
                  <button id="btn-confirmar-excluir" style="background: #ff6b6b; color: white; border: none; border-radius: 6px; padding: 10px 28px; font-size: 1rem; font-weight: 600; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">Excluir</button>
                  <button id="btn-cancelar-excluir" style="background: #e1e5e9; color: #333; border: none; border-radius: 6px; padding: 10px 28px; font-size: 1rem; font-weight: 600; cursor: pointer;">Cancelar</button>
               </div>
            </div>
         `;
         document.body.appendChild(modal);
         document.getElementById('btn-cancelar-excluir').onclick = () => modal.remove();
         document.getElementById('btn-confirmar-excluir').onclick = () => {
            modal.remove();
            // Executar exclusão
            const dados = new FormData();
            dados.append('whatsapp', whatsapp);
            fetch('excluir_contato.php', {
                  method: 'POST',
                  body: dados
               })
               .then(response => response.json())
               .then(data => {
                  if (data.success) {
                     mostrarAlertaPainel('Contato excluído com sucesso!', 'success');
                     setTimeout(() => location.reload(), 1200);
                  } else {
                     mostrarAlertaPainel('Erro ao excluir contato: ' + data.message, 'error');
                  }
               })
               .catch(error => {
                  mostrarAlertaPainel('Erro ao excluir contato: ' + error.message, 'error');
               });
         };
      }

      // Copiar telefone ao clicar
      document.querySelectorAll('.contato-info').forEach(function(element) {
         element.addEventListener('click', function() {
            const telefone = this.textContent.trim();
            navigator.clipboard.writeText(telefone).then(function() {
               mostrarAlertaPainel('Telefone copiado: ' + telefone, 'success');
            });
         });
      });

      // Fechar modal ao clicar fora
      document.addEventListener('click', function(event) {
         const modal = document.querySelector('div[style*="position: fixed"]');
         if (modal && event.target === modal) {
            fecharModal();
         }
      });
   </script>
</body>

</html>