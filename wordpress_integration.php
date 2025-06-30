<?php

/**
 * Sistema de Integração WordPress + Elementor Pro
 * Permite usar plugins WordPress em seu sistema customizado
 */

class WordPressIntegration
{
   private $wp_url;
   private $wp_username;
   private $wp_password;
   private $api_base = '/wp-json/wp/v2/';

   public function __construct($wp_url, $wp_username, $wp_password)
   {
      $this->wp_url = rtrim($wp_url, '/');
      $this->wp_username = $wp_username;
      $this->wp_password = $wp_password;
   }

   /**
    * Autentica com WordPress e obtém token
    */
   public function authenticate()
   {
      $auth_url = $this->wp_url . '/wp-json/jwt-auth/v1/token';
      $response = wp_remote_post($auth_url, [
         'body' => [
            'username' => $this->wp_username,
            'password' => $this->wp_password
         ]
      ]);

      if (is_wp_error($response)) {
         return false;
      }

      $body = wp_remote_retrieve_body($response);
      $data = json_decode($body, true);

      return $data['token'] ?? false;
   }

   /**
    * Obtém páginas criadas com Elementor
    */
   public function getElementorPages($token)
   {
      $url = $this->wp_url . $this->api_base . 'pages';
      $response = wp_remote_get($url, [
         'headers' => [
            'Authorization' => 'Bearer ' . $token
         ],
         'body' => [
            'meta_key' => '_elementor_edit_mode',
            'meta_value' => 'builder'
         ]
      ]);

      if (is_wp_error($response)) {
         return [];
      }

      $body = wp_remote_retrieve_body($response);
      return json_decode($body, true);
   }

   /**
    * Obtém conteúdo Elementor de uma página
    */
   public function getElementorContent($page_id, $token)
   {
      $url = $this->wp_url . $this->api_base . 'pages/' . $page_id;
      $response = wp_remote_get($url, [
         'headers' => [
            'Authorization' => 'Bearer ' . $token
         ]
      ]);

      if (is_wp_error($response)) {
         return null;
      }

      $body = wp_remote_retrieve_body($response);
      $data = json_decode($body, true);

      // Obtém dados do Elementor
      $elementor_data = get_post_meta($page_id, '_elementor_data', true);

      return [
         'title' => $data['title']['rendered'],
         'content' => $data['content']['rendered'],
         'elementor_data' => $elementor_data,
         'meta' => $data['meta']
      ];
   }

   /**
    * Renderiza conteúdo Elementor
    */
   public function renderElementorContent($elementor_data)
   {
      if (empty($elementor_data)) {
         return '';
      }

      $html = '';
      foreach ($elementor_data as $section) {
         $html .= $this->renderSection($section);
      }

      return $html;
   }

   /**
    * Renderiza uma seção Elementor
    */
   private function renderSection($section)
   {
      $html = '<section class="elementor-section elementor-section-' . $section['elType'] . '">';
      $html .= '<div class="elementor-container">';

      foreach ($section['elements'] as $column) {
         $html .= $this->renderColumn($column);
      }

      $html .= '</div></section>';
      return $html;
   }

   /**
    * Renderiza uma coluna Elementor
    */
   private function renderColumn($column)
   {
      $html = '<div class="elementor-column">';

      foreach ($column['elements'] as $widget) {
         $html .= $this->renderWidget($widget);
      }

      $html .= '</div>';
      return $html;
   }

   /**
    * Renderiza um widget Elementor
    */
   private function renderWidget($widget)
   {
      switch ($widget['widgetType']) {
         case 'heading':
            return '<h' . ($widget['settings']['size'] ?? '2') . '>' .
               htmlspecialchars($widget['settings']['title']) . '</h' . ($widget['settings']['size'] ?? '2') . '>';

         case 'text-editor':
            return '<div class="elementor-text-editor">' .
               $widget['settings']['editor'] . '</div>';

         case 'image':
            return '<img src="' . htmlspecialchars($widget['settings']['image']['url']) . '" 
                             alt="' . htmlspecialchars($widget['settings']['image']['alt']) . '" 
                             class="elementor-image">';

         case 'button':
            return '<a href="' . htmlspecialchars($widget['settings']['link']['url']) . '" 
                           class="elementor-button">' .
               htmlspecialchars($widget['settings']['text']) . '</a>';

         default:
            return '<div class="elementor-widget-' . $widget['widgetType'] . '">' .
               'Widget: ' . $widget['widgetType'] . '</div>';
      }
   }
}

/**
 * Sistema de Plugins Próprio
 */
class PluginSystem
{
   private $plugins_dir = 'plugins/';
   private $active_plugins = [];

   public function __construct()
   {
      $this->loadActivePlugins();
   }

   /**
    * Carrega plugins ativos
    */
   private function loadActivePlugins()
   {
      $config_file = 'plugins_config.json';
      if (file_exists($config_file)) {
         $this->active_plugins = json_decode(file_get_contents($config_file), true) ?? [];
      }
   }

   /**
    * Executa hooks de plugins
    */
   public function doAction($hook_name, $data = null)
   {
      foreach ($this->active_plugins as $plugin) {
         $plugin_file = $this->plugins_dir . $plugin . '/plugin.php';
         if (file_exists($plugin_file)) {
            include_once $plugin_file;

            $function_name = $plugin . '_' . $hook_name;
            if (function_exists($function_name)) {
               $function_name($data);
            }
         }
      }
   }

   /**
    * Aplica filtros de plugins
    */
   public function applyFilter($filter_name, $value)
   {
      foreach ($this->active_plugins as $plugin) {
         $plugin_file = $this->plugins_dir . $plugin . '/plugin.php';
         if (file_exists($plugin_file)) {
            include_once $plugin_file;

            $function_name = $plugin . '_' . $filter_name;
            if (function_exists($function_name)) {
               $value = $function_name($value);
            }
         }
      }
      return $value;
   }
}

/**
 * Editor Visual Próprio (inspirado no Elementor)
 */
class VisualEditor
{
   private $elements = [];
   private $templates = [];

   public function __construct()
   {
      $this->loadElements();
      $this->loadTemplates();
   }

   /**
    * Carrega elementos disponíveis
    */
   private function loadElements()
   {
      $this->elements = [
         'heading' => [
            'name' => 'Título',
            'icon' => 'bi-type-h1',
            'category' => 'basic',
            'settings' => [
               'text' => ['type' => 'text', 'label' => 'Texto'],
               'tag' => ['type' => 'select', 'label' => 'Tag', 'options' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']],
               'align' => ['type' => 'select', 'label' => 'Alinhamento', 'options' => ['left', 'center', 'right']]
            ]
         ],
         'text' => [
            'name' => 'Texto',
            'icon' => 'bi-text-paragraph',
            'category' => 'basic',
            'settings' => [
               'content' => ['type' => 'textarea', 'label' => 'Conteúdo'],
               'align' => ['type' => 'select', 'label' => 'Alinhamento', 'options' => ['left', 'center', 'right', 'justify']]
            ]
         ],
         'image' => [
            'name' => 'Imagem',
            'icon' => 'bi-image',
            'category' => 'media',
            'settings' => [
               'src' => ['type' => 'image', 'label' => 'Imagem'],
               'alt' => ['type' => 'text', 'label' => 'Texto Alternativo'],
               'width' => ['type' => 'number', 'label' => 'Largura'],
               'height' => ['type' => 'number', 'label' => 'Altura']
            ]
         ],
         'button' => [
            'name' => 'Botão',
            'icon' => 'bi-box-arrow-up-right',
            'category' => 'basic',
            'settings' => [
               'text' => ['type' => 'text', 'label' => 'Texto'],
               'link' => ['type' => 'url', 'label' => 'Link'],
               'style' => ['type' => 'select', 'label' => 'Estilo', 'options' => ['primary', 'secondary', 'success', 'danger']]
            ]
         ],
         'container' => [
            'name' => 'Container',
            'icon' => 'bi-box',
            'category' => 'layout',
            'settings' => [
               'width' => ['type' => 'select', 'label' => 'Largura', 'options' => ['100%', '75%', '50%', '25%']],
               'padding' => ['type' => 'number', 'label' => 'Padding'],
               'background' => ['type' => 'color', 'label' => 'Cor de Fundo']
            ]
         ]
      ];
   }

   /**
    * Carrega templates
    */
   private function loadTemplates()
   {
      $templates_file = 'templates.json';
      if (file_exists($templates_file)) {
         $this->templates = json_decode(file_get_contents($templates_file), true) ?? [];
      }
   }

   /**
    * Renderiza o editor
    */
   public function renderEditor($page_data = null)
   {
?>
      <div class="visual-editor">
         <div class="editor-sidebar">
            <div class="elements-panel">
               <h3>Elementos</h3>
               <?php foreach ($this->elements as $element_id => $element): ?>
                  <div class="element-item" draggable="true" data-element="<?php echo $element_id; ?>">
                     <i class="<?php echo $element['icon']; ?>"></i>
                     <span><?php echo $element['name']; ?></span>
                  </div>
               <?php endforeach; ?>
            </div>

            <div class="templates-panel">
               <h3>Templates</h3>
               <?php foreach ($this->templates as $template): ?>
                  <div class="template-item" data-template="<?php echo $template['id']; ?>">
                     <img src="<?php echo $template['preview']; ?>" alt="<?php echo $template['name']; ?>">
                     <span><?php echo $template['name']; ?></span>
                  </div>
               <?php endforeach; ?>
            </div>
         </div>

         <div class="editor-canvas">
            <div class="canvas-toolbar">
               <button class="btn btn-primary" onclick="savePage()">Salvar</button>
               <button class="btn btn-secondary" onclick="previewPage()">Visualizar</button>
               <button class="btn btn-secondary" onclick="undoAction()">Desfazer</button>
               <button class="btn btn-secondary" onclick="redoAction()">Refazer</button>
            </div>

            <div class="canvas-area" id="canvas">
               <?php if ($page_data): ?>
                  <?php echo $this->renderPageContent($page_data); ?>
               <?php else: ?>
                  <div class="empty-canvas">
                     <i class="bi-plus-circle"></i>
                     <p>Arraste elementos aqui para começar</p>
                  </div>
               <?php endif; ?>
            </div>
         </div>

         <div class="editor-panel" id="element-panel">
            <h3>Propriedades</h3>
            <div class="panel-content">
               <p>Selecione um elemento para editar suas propriedades</p>
            </div>
         </div>
      </div>

      <script>
         // JavaScript do editor visual
         class VisualEditor {
            constructor() {
               this.canvas = document.getElementById('canvas');
               this.panel = document.getElementById('element-panel');
               this.selectedElement = null;
               this.history = [];
               this.historyIndex = -1;

               this.initDragAndDrop();
               this.initEventListeners();
            }

            initDragAndDrop() {
               // Drag dos elementos para o canvas
               document.querySelectorAll('.element-item').forEach(item => {
                  item.addEventListener('dragstart', (e) => {
                     e.dataTransfer.setData('text/plain', item.dataset.element);
                  });
               });

               // Drop no canvas
               this.canvas.addEventListener('dragover', (e) => {
                  e.preventDefault();
               });

               this.canvas.addEventListener('drop', (e) => {
                  e.preventDefault();
                  const elementType = e.dataTransfer.getData('text/plain');
                  this.addElement(elementType, e.clientX, e.clientY);
               });
            }

            initEventListeners() {
               // Seleção de elementos
               this.canvas.addEventListener('click', (e) => {
                  if (e.target.closest('.canvas-element')) {
                     this.selectElement(e.target.closest('.canvas-element'));
                  } else {
                     this.deselectElement();
                  }
               });
            }

            addElement(type, x, y) {
               const element = this.createElement(type);
               element.style.position = 'absolute';
               element.style.left = (x - this.canvas.offsetLeft) + 'px';
               element.style.top = (y - this.canvas.offsetTop) + 'px';

               this.canvas.appendChild(element);
               this.saveHistory();
            }

            createElement(type) {
               const element = document.createElement('div');
               element.className = 'canvas-element element-' + type;
               element.dataset.type = type;

               switch (type) {
                  case 'heading':
                     element.innerHTML = '<h2>Novo Título</h2>';
                     break;
                  case 'text':
                     element.innerHTML = '<p>Novo texto aqui...</p>';
                     break;
                  case 'image':
                     element.innerHTML = '<img src="placeholder.jpg" alt="Imagem">';
                     break;
                  case 'button':
                     element.innerHTML = '<button class="btn btn-primary">Botão</button>';
                     break;
                  case 'container':
                     element.innerHTML = '<div class="container">Container</div>';
                     break;
               }

               return element;
            }

            selectElement(element) {
               if (this.selectedElement) {
                  this.selectedElement.classList.remove('selected');
               }

               this.selectedElement = element;
               element.classList.add('selected');
               this.showElementPanel(element);
            }

            deselectElement() {
               if (this.selectedElement) {
                  this.selectedElement.classList.remove('selected');
                  this.selectedElement = null;
               }
               this.hideElementPanel();
            }

            showElementPanel(element) {
               const type = element.dataset.type;
               const elementConfig = <?php echo json_encode($this->elements); ?>[type];

               if (elementConfig) {
                  this.panel.querySelector('.panel-content').innerHTML = this.generateSettingsHTML(elementConfig.settings);
               }
            }

            hideElementPanel() {
               this.panel.querySelector('.panel-content').innerHTML = '<p>Selecione um elemento para editar suas propriedades</p>';
            }

            generateSettingsHTML(settings) {
               let html = '';
               for (const [key, setting] of Object.entries(settings)) {
                  html += `<div class="setting-group">
                            <label>${setting.label}:</label>
                            ${this.generateInputHTML(key, setting)}
                        </div>`;
               }
               return html;
            }

            generateInputHTML(key, setting) {
               switch (setting.type) {
                  case 'text':
                     return `<input type="text" name="${key}" class="form-control">`;
                  case 'textarea':
                     return `<textarea name="${key}" class="form-control"></textarea>`;
                  case 'select':
                     return `<select name="${key}" class="form-control">
                                ${setting.options.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                            </select>`;
                  case 'number':
                     return `<input type="number" name="${key}" class="form-control">`;
                  case 'color':
                     return `<input type="color" name="${key}" class="form-control">`;
                  case 'image':
                     return `<input type="file" name="${key}" accept="image/*" class="form-control">`;
                  case 'url':
                     return `<input type="url" name="${key}" class="form-control">`;
                  default:
                     return `<input type="text" name="${key}" class="form-control">`;
               }
            }

            saveHistory() {
               this.history = this.history.slice(0, this.historyIndex + 1);
               this.history.push(this.canvas.innerHTML);
               this.historyIndex++;
            }

            undoAction() {
               if (this.historyIndex > 0) {
                  this.historyIndex--;
                  this.canvas.innerHTML = this.history[this.historyIndex];
               }
            }

            redoAction() {
               if (this.historyIndex < this.history.length - 1) {
                  this.historyIndex++;
                  this.canvas.innerHTML = this.history[this.historyIndex];
               }
            }

            getPageData() {
               return {
                  elements: Array.from(this.canvas.querySelectorAll('.canvas-element')).map(el => ({
                     type: el.dataset.type,
                     content: el.innerHTML,
                     style: el.style.cssText,
                     position: {
                        x: el.style.left,
                        y: el.style.top
                     }
                  }))
               };
            }
         }

         // Inicializar editor
         const editor = new VisualEditor();

         // Funções globais
         function savePage() {
            const data = editor.getPageData();
            // Enviar para o servidor
            fetch('save_page.php', {
                  method: 'POST',
                  headers: {
                     'Content-Type': 'application/json'
                  },
                  body: JSON.stringify(data)
               }).then(response => response.json())
               .then(result => {
                  if (result.success) {
                     alert('Página salva com sucesso!');
                  } else {
                     alert('Erro ao salvar: ' + result.error);
                  }
               });
         }

         function previewPage() {
            const data = editor.getPageData();
            const previewWindow = window.open('', '_blank');
            previewWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Preview</title>
                        <link rel="stylesheet" href="css/style.css">
                    </head>
                    <body>
                        ${editor.canvas.innerHTML}
                    </body>
                    </html>
                `);
         }

         function undoAction() {
            editor.undoAction();
         }

         function redoAction() {
            editor.redoAction();
         }
      </script>
<?php
   }

   /**
    * Renderiza conteúdo da página
    */
   private function renderPageContent($page_data)
   {
      $html = '';
      foreach ($page_data['elements'] as $element) {
         $html .= '<div class="canvas-element element-' . $element['type'] . '" style="' . $element['style'] . '">';
         $html .= $element['content'];
         $html .= '</div>';
      }
      return $html;
   }
}

// Exemplo de uso
if (isset($_GET['action'])) {
   switch ($_GET['action']) {
      case 'wordpress_integration':
         // Configurar integração WordPress
         $wp_integration = new WordPressIntegration(
            'https://seusite.com/wordpress',
            'admin',
            'senha123'
         );

         $token = $wp_integration->authenticate();
         if ($token) {
            $pages = $wp_integration->getElementorPages($token);
            echo json_encode($pages);
         }
         break;

      case 'visual_editor':
         // Mostrar editor visual
         $editor = new VisualEditor();
         $editor->renderEditor();
         break;

      case 'plugin_system':
         // Sistema de plugins
         $plugin_system = new PluginSystem();
         $plugin_system->doAction('init');
         break;
   }
}
?>