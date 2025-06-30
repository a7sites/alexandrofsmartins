<?php

/**
 * Integração com Elementor Pro
 * Sistema híbrido para usar Elementor em seu painel customizado
 */

session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

// Carregar configurações do painel
$config_file = 'painel_config.json';
$config_painel = json_decode(file_get_contents($config_file), true) ?? [];

// Configurações WordPress
$wp_config = [
   'url' => 'https://seusite.com/wordpress',
   'username' => 'admin',
   'password' => 'senha123',
   'api_base' => '/wp-json/wp/v2/'
];

class ElementorIntegration
{
   private $wp_url;
   private $wp_username;
   private $wp_password;
   private $api_base;

   public function __construct($config)
   {
      $this->wp_url = rtrim($config['url'], '/');
      $this->wp_username = $config['username'];
      $this->wp_password = $config['password'];
      $this->api_base = $config['api_base'];
   }

   /**
    * Autentica com WordPress
    */
   public function authenticate()
   {
      $auth_url = $this->wp_url . '/wp-json/jwt-auth/v1/token';

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $auth_url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
         'username' => $this->wp_username,
         'password' => $this->wp_password
      ]));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $response = curl_exec($ch);
      curl_close($ch);

      $data = json_decode($response, true);
      return $data['token'] ?? false;
   }

   /**
    * Obtém páginas criadas com Elementor
    */
   public function getElementorPages($token)
   {
      $url = $this->wp_url . $this->api_base . 'pages';

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url . '?meta_key=_elementor_edit_mode&meta_value=builder');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
         'Authorization: Bearer ' . $token
      ]);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $response = curl_exec($ch);
      curl_close($ch);

      return json_decode($response, true) ?? [];
   }

   /**
    * Obtém conteúdo de uma página
    */
   public function getPageContent($page_id, $token)
   {
      $url = $this->wp_url . $this->api_base . 'pages/' . $page_id;

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
         'Authorization: Bearer ' . $token
      ]);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $response = curl_exec($ch);
      curl_close($ch);

      return json_decode($response, true);
   }

   /**
    * Renderiza conteúdo Elementor
    */
   public function renderElementorContent($content)
   {
      // Processa o conteúdo HTML do Elementor
      $html = $content['content']['rendered'] ?? '';

      // Adiciona estilos CSS do Elementor
      $html = '<link rel="stylesheet" href="' . $this->wp_url . '/wp-content/plugins/elementor/assets/css/frontend.min.css">' . $html;

      return $html;
   }
}

// Processar ações
$action = $_GET['action'] ?? '';
$message = '';

if ($_POST) {
   switch ($action) {
      case 'connect_wordpress':
         $wp_config['url'] = $_POST['wp_url'];
         $wp_config['username'] = $_POST['wp_username'];
         $wp_config['password'] = $_POST['wp_password'];

         $integration = new ElementorIntegration($wp_config);
         $token = $integration->authenticate();

         if ($token) {
            $message = 'Conexão com WordPress estabelecida com sucesso!';
            $_SESSION['wp_token'] = $token;
            $_SESSION['wp_config'] = $wp_config;
         } else {
            $message = 'Erro ao conectar com WordPress. Verifique as credenciais.';
         }
         break;
   }
}

// Carregar dados se conectado
$pages = [];
if (isset($_SESSION['wp_token'])) {
   $integration = new ElementorIntegration($_SESSION['wp_config']);
   $pages = $integration->getElementorPages($_SESSION['wp_token']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Integração Elementor - <?php echo htmlspecialchars($config_painel['titulo']); ?></title>
   <link rel="stylesheet" href="css/painel.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <style>
      .integration-container {
         max-width: 1200px;
         margin: 0 auto;
         padding: 20px;
      }

      .connection-form {
         background: #fff;
         padding: 30px;
         border-radius: 10px;
         box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
         margin-bottom: 30px;
      }

      .pages-grid {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
         gap: 20px;
         margin-top: 20px;
      }

      .page-card {
         background: #fff;
         border-radius: 10px;
         padding: 20px;
         box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
         transition: transform 0.2s;
      }

      .page-card:hover {
         transform: translateY(-5px);
      }

      .page-title {
         font-size: 18px;
         font-weight: bold;
         margin-bottom: 10px;
         color: #333;
      }

      .page-meta {
         font-size: 14px;
         color: #666;
         margin-bottom: 15px;
      }

      .page-actions {
         display: flex;
         gap: 10px;
      }

      .btn-elementor {
         background: #61ce70;
         color: white;
         border: none;
         padding: 8px 16px;
         border-radius: 5px;
         cursor: pointer;
         text-decoration: none;
         font-size: 14px;
      }

      .btn-elementor:hover {
         background: #4caf50;
      }

      .status-connected {
         background: #4caf50;
         color: white;
         padding: 10px;
         border-radius: 5px;
         margin-bottom: 20px;
      }

      .status-disconnected {
         background: #f44336;
         color: white;
         padding: 10px;
         border-radius: 5px;
         margin-bottom: 20px;
      }
   </style>
</head>

<body>
   <div class="sidebar <?php echo $config_painel['sidebar_shrink'] ? 'shrink' : ''; ?>">
      <div class="sidebar-header">
         <h2><?php echo htmlspecialchars($config_painel['titulo']); ?></h2>
      </div>
      <nav class="sidebar-nav">
         <ul>
            <?php foreach ($config_painel['menus'] as $menu): ?>
               <li>
                  <a href="<?php echo htmlspecialchars($menu['link']); ?>" class="<?php echo $menu['id'] === 'elementor' ? 'active' : ''; ?>">
                     <i class="<?php echo htmlspecialchars($menu['icone']); ?>"></i>
                     <span><?php echo htmlspecialchars($menu['nome']); ?></span>
                  </a>
               </li>
            <?php endforeach; ?>
         </ul>
      </nav>
   </div>

   <div class="main-content <?php echo $config_painel['sidebar_shrink'] ? 'expanded' : ''; ?>">
      <div class="integration-container">
         <h1><i class="bi-palette"></i> Integração Elementor Pro</h1>

         <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
         <?php endif; ?>

         <?php if (isset($_SESSION['wp_token'])): ?>
            <div class="status-connected">
               <i class="bi-check-circle"></i> Conectado ao WordPress
            </div>

            <div class="pages-grid">
               <?php if (empty($pages)): ?>
                  <div class="page-card">
                     <div class="page-title">Nenhuma página Elementor encontrada</div>
                     <div class="page-meta">Crie páginas usando o Elementor no WordPress</div>
                  </div>
               <?php else: ?>
                  <?php foreach ($pages as $page): ?>
                     <div class="page-card">
                        <div class="page-title"><?php echo htmlspecialchars($page['title']['rendered']); ?></div>
                        <div class="page-meta">
                           <strong>ID:</strong> <?php echo $page['id']; ?><br>
                           <strong>Status:</strong> <?php echo $page['status']; ?><br>
                           <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($page['date'])); ?>
                        </div>
                        <div class="page-actions">
                           <a href="<?php echo $wp_config['url']; ?>/wp-admin/post.php?post=<?php echo $page['id']; ?>&action=elementor"
                              target="_blank" class="btn-elementor">
                              <i class="bi-pencil"></i> Editar no Elementor
                           </a>
                           <a href="preview_elementor.php?page_id=<?php echo $page['id']; ?>"
                              target="_blank" class="btn btn-secondary">
                              <i class="bi-eye"></i> Visualizar
                           </a>
                        </div>
                     </div>
                  <?php endforeach; ?>
               <?php endif; ?>
            </div>

         <?php else: ?>
            <div class="status-disconnected">
               <i class="bi-exclamation-triangle"></i> Não conectado ao WordPress
            </div>

            <div class="connection-form">
               <h3><i class="bi-plug"></i> Conectar ao WordPress</h3>
               <form method="POST" action="?action=connect_wordpress">
                  <div class="form-group">
                     <label for="wp_url">URL do WordPress:</label>
                     <input type="url" id="wp_url" name="wp_url" value="<?php echo htmlspecialchars($wp_config['url']); ?>" required>
                  </div>

                  <div class="form-group">
                     <label for="wp_username">Usuário:</label>
                     <input type="text" id="wp_username" name="wp_username" value="<?php echo htmlspecialchars($wp_config['username']); ?>" required>
                  </div>

                  <div class="form-group">
                     <label for="wp_password">Senha:</label>
                     <input type="password" id="wp_username" name="wp_password" value="<?php echo htmlspecialchars($wp_config['password']); ?>" required>
                  </div>

                  <button type="submit" class="btn btn-primary">
                     <i class="bi-plug"></i> Conectar
                  </button>
               </form>
            </div>

            <div class="info-box">
               <h4><i class="bi-info-circle"></i> Como funciona?</h4>
               <p>Esta integração permite:</p>
               <ul>
                  <li>Criar páginas no WordPress usando Elementor Pro</li>
                  <li>Visualizar e gerenciar páginas do seu painel</li>
                  <li>Editar páginas diretamente no Elementor</li>
                  <li>Integrar conteúdo Elementor em seu site</li>
               </ul>
            </div>
         <?php endif; ?>
      </div>
   </div>

   <script src="js/script.js"></script>
</body>

</html>