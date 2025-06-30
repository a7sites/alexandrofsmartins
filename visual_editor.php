<?php

/**
 * Editor Visual Próprio (inspirado no Elementor)
 */

session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

// Carregar configurações do painel
$config_file = 'painel_config.json';
$config_painel = json_decode(file_get_contents($config_file), true) ?? [];

// Carregar página se existir
$page_id = $_GET['page_id'] ?? null;
$page_data = null;

if ($page_id) {
   $pages_file = 'pages/' . $page_id . '.json';
   if (file_exists($pages_file)) {
      $page_data = json_decode(file_get_contents($pages_file), true);
   }
}

// Elementos disponíveis
$elements = [
   'heading' => [
      'name' => 'Título',
      'icon' => 'bi-type-h1',
      'category' => 'basic',
      'settings' => [
         'text' => ['type' => 'text', 'label' => 'Texto', 'default' => 'Novo Título'],
         'tag' => ['type' => 'select', 'label' => 'Tag', 'options' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], 'default' => 'h2'],
         'align' => ['type' => 'select', 'label' => 'Alinhamento', 'options' => ['left', 'center', 'right'], 'default' => 'left'],
         'color' => ['type' => 'color', 'label' => 'Cor', 'default' => '#333333'],
         'size' => ['type' => 'number', 'label' => 'Tamanho (px)', 'default' => '24']
      ]
   ],
   'text' => [
      'name' => 'Texto',
      'icon' => 'bi-text-paragraph',
      'category' => 'basic',
      'settings' => [
         'content' => ['type' => 'textarea', 'label' => 'Conteúdo', 'default' => 'Digite seu texto aqui...'],
         'align' => ['type' => 'select', 'label' => 'Alinhamento', 'options' => ['left', 'center', 'right', 'justify'], 'default' => 'left'],
         'color' => ['type' => 'color', 'label' => 'Cor', 'default' => '#666666'],
         'size' => ['type' => 'number', 'label' => 'Tamanho (px)', 'default' => '16']
      ]
   ],
   'image' => [
      'name' => 'Imagem',
      'icon' => 'bi-image',
      'category' => 'media',
      'settings' => [
         'src' => ['type' => 'image', 'label' => 'Imagem', 'default' => ''],
         'alt' => ['type' => 'text', 'label' => 'Texto Alternativo', 'default' => ''],
         'width' => ['type' => 'number', 'label' => 'Largura (%)', 'default' => '100'],
         'height' => ['type' => 'number', 'label' => 'Altura (px)', 'default' => 'auto'],
         'border_radius' => ['type' => 'number', 'label' => 'Borda Arredondada (px)', 'default' => '0']
      ]
   ],
   'button' => [
      'name' => 'Botão',
      'icon' => 'bi-box-arrow-up-right',
      'category' => 'basic',
      'settings' => [
         'text' => ['type' => 'text', 'label' => 'Texto', 'default' => 'Clique Aqui'],
         'link' => ['type' => 'url', 'label' => 'Link', 'default' => '#'],
         'style' => ['type' => 'select', 'label' => 'Estilo', 'options' => ['primary', 'secondary', 'success', 'danger', 'warning', 'info'], 'default' => 'primary'],
         'size' => ['type' => 'select', 'label' => 'Tamanho', 'options' => ['small', 'medium', 'large'], 'default' => 'medium'],
         'target' => ['type' => 'select', 'label' => 'Abrir em', 'options' => ['_self', '_blank'], 'default' => '_self']
      ]
   ],
   'container' => [
      'name' => 'Container',
      'icon' => 'bi-box',
      'category' => 'layout',
      'settings' => [
         'width' => ['type' => 'select', 'label' => 'Largura', 'options' => ['100%', '75%', '50%', '25%'], 'default' => '100%'],
         'padding' => ['type' => 'number', 'label' => 'Padding (px)', 'default' => '20'],
         'margin' => ['type' => 'number', 'label' => 'Margin (px)', 'default' => '0'],
         'background' => ['type' => 'color', 'label' => 'Cor de Fundo', 'default' => 'transparent'],
         'border' => ['type' => 'text', 'label' => 'Borda (CSS)', 'default' => 'none']
      ]
   ],
   'divider' => [
      'name' => 'Divisor',
      'icon' => 'bi-dash',
      'category' => 'basic',
      'settings' => [
         'style' => ['type' => 'select', 'label' => 'Estilo', 'options' => ['solid', 'dashed', 'dotted'], 'default' => 'solid'],
         'width' => ['type' => 'number', 'label' => 'Largura (%)', 'default' => '100'],
         'height' => ['type' => 'number', 'label' => 'Altura (px)', 'default' => '1'],
         'color' => ['type' => 'color', 'label' => 'Cor', 'default' => '#cccccc']
      ]
   ],
   'spacer' => [
      'name' => 'Espaçador',
      'icon' => 'bi-arrows-vertical',
      'category' => 'layout',
      'settings' => [
         'height' => ['type' => 'number', 'label' => 'Altura (px)', 'default' => '50']
      ]
   ]
];

// Templates disponíveis
$templates = [
   [
      'id' => 'hero_section',
      'name' => 'Seção Hero',
      'preview' => 'imgs/template_hero.jpg',
      'elements' => [
         [
            'type' => 'container',
            'settings' => ['width' => '100%', 'padding' => '100', 'background' => '#f8f9fa'],
            'children' => [
               [
                  'type' => 'heading',
                  'settings' => ['text' => 'Título Principal', 'tag' => 'h1', 'align' => 'center', 'size' => '48']
               ],
               [
                  'type' => 'text',
                  'settings' => ['content' => 'Descrição da sua empresa ou projeto', 'align' => 'center', 'size' => '18']
               ],
               [
                  'type' => 'button',
                  'settings' => ['text' => 'Saiba Mais', 'link' => '#', 'style' => 'primary', 'size' => 'large']
               ]
            ]
         ]
      ]
   ],
   [
      'id' => 'about_section',
      'name' => 'Seção Sobre',
      'preview' => 'imgs/template_about.jpg',
      'elements' => [
         [
            'type' => 'container',
            'settings' => ['width' => '100%', 'padding' => '80'],
            'children' => [
               [
                  'type' => 'heading',
                  'settings' => ['text' => 'Sobre Nós', 'tag' => 'h2', 'align' => 'center']
               ],
               [
                  'type' => 'text',
                  'settings' => ['content' => 'Texto sobre sua empresa ou projeto...', 'align' => 'justify']
               ],
               [
                  'type' => 'image',
                  'settings' => ['src' => 'imgs/about.jpg', 'alt' => 'Sobre nós', 'width' => '50']
               ]
            ]
         ]
      ]
   ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Editor Visual - <?php echo htmlspecialchars($config_painel['titulo']); ?></title>
   <link rel="stylesheet" href="css/painel.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <style>
      .visual-editor {
         display: flex;
         height: 100vh;
         background: #f5f5f5;
      }

      .editor-sidebar {
         width: 300px;
         background: #fff;
         border-right: 1px solid #ddd;
         overflow-y: auto;
      }

      .editor-canvas {
         flex: 1;
         display: flex;
         flex-direction: column;
      }

      .editor-panel {
         width: 350px;
         background: #fff;
         border-left: 1px solid #ddd;
         overflow-y: auto;
      }

      .canvas-toolbar {
         background: #fff;
         padding: 15px;
         border-bottom: 1px solid #ddd;
         display: flex;
         gap: 10px;
         align-items: center;
      }

      .canvas-area {
         flex: 1;
         padding: 20px;
         overflow-y: auto;
         background: #f8f9fa;
      }

      .elements-panel,
      .templates-panel {
         padding: 20px;
      }

      .elements-panel h3,
      .templates-panel h3 {
         margin: 0 0 15px 0;
         color: #333;
         font-size: 16px;
      }

      .element-item,
      .template-item {
         display: flex;
         align-items: center;
         gap: 10px;
         padding: 10px;
         margin-bottom: 5px;
         border: 1px solid #ddd;
         border-radius: 5px;
         cursor: pointer;
         transition: all 0.2s;
      }

      .element-item:hover,
      .template-item:hover {
         background: #f8f9fa;
         border-color: #007bff;
      }

      .element-item[draggable="true"] {
         cursor: grab;
      }

      .element-item[draggable="true"]:active {
         cursor: grabbing;
      }

      .template-item img {
         width: 50px;
         height: 30px;
         object-fit: cover;
         border-radius: 3px;
      }

      .canvas-element {
         position: relative;
         margin-bottom: 10px;
         border: 2px solid transparent;
         transition: all 0.2s;
      }

      .canvas-element:hover {
         border-color: #007bff;
      }

      .canvas-element.selected {
         border-color: #007bff;
         box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
      }

      .canvas-element .element-controls {
         position: absolute;
         top: -30px;
         right: 0;
         background: #007bff;
         color: white;
         padding: 5px;
         border-radius: 3px;
         display: none;
         gap: 5px;
      }

      .canvas-element:hover .element-controls {
         display: flex;
      }

      .element-controls button {
         background: none;
         border: none;
         color: white;
         cursor: pointer;
         padding: 2px;
      }

      .empty-canvas {
         text-align: center;
         padding: 100px 20px;
         color: #666;
      }

      .empty-canvas i {
         font-size: 48px;
         margin-bottom: 20px;
         color: #ddd;
      }

      .panel-content {
         padding: 20px;
      }

      .setting-group {
         margin-bottom: 15px;
      }

      .setting-group label {
         display: block;
         margin-bottom: 5px;
         font-weight: bold;
         color: #333;
      }

      .setting-group input,
      .setting-group select,
      .setting-group textarea {
         width: 100%;
         padding: 8px;
         border: 1px solid #ddd;
         border-radius: 4px;
         font-size: 14px;
      }

      .setting-group textarea {
         min-height: 80px;
         resize: vertical;
      }

      .color-picker {
         position: relative;
      }

      .color-picker input[type="color"] {
         width: 50px;
         height: 35px;
         padding: 0;
         border: none;
         border-radius: 4px;
         cursor: pointer;
      }

      .btn-group {
         display: flex;
         gap: 10px;
         flex-wrap: wrap;
      }

      .btn {
         padding: 8px 16px;
         border: none;
         border-radius: 4px;
         cursor: pointer;
         font-size: 14px;
         text-decoration: none;
         display: inline-flex;
         align-items: center;
         gap: 5px;
      }

      .btn-primary {
         background: #007bff;
         color: white;
      }

      .btn-secondary {
         background: #6c757d;
         color: white;
      }

      .btn-success {
         background: #28a745;
         color: white;
      }

      .btn-danger {
         background: #dc3545;
         color: white;
      }

      .btn-warning {
         background: #ffc107;
         color: #212529;
      }

      .btn-info {
         background: #17a2b8;
         color: white;
      }

      .btn-small {
         padding: 4px 8px;
         font-size: 12px;
      }

      .btn-large {
         padding: 12px 24px;
         font-size: 16px;
      }
   </style>
</head>

<body>
   <div class="visual-editor">
      <div class="editor-sidebar">
         <div class="elements-panel">
            <h3><i class="bi-puzzle"></i> Elementos</h3>
            <?php foreach ($elements as $element_id => $element): ?>
               <div class="element-item" draggable="true" data-element="<?php echo $element_id; ?>">
                  <i class="<?php echo $element['icon']; ?>"></i>
                  <span><?php echo $element['name']; ?></span>
               </div>
            <?php endforeach; ?>
         </div>

         <div class="templates-panel">
            <h3><i class="bi-collection"></i> Templates</h3>
            <?php foreach ($templates as $template): ?>
               <div class="template-item" data-template="<?php echo $template['id']; ?>">
                  <img src="<?php echo $template['preview']; ?>" alt="<?php echo $template['name']; ?>">
                  <span><?php echo $template['name']; ?></span>
               </div>
            <?php endforeach; ?>
         </div>
      </div>

      <div class="editor-canvas">
         <div class="canvas-toolbar">
            <button class="btn btn-primary" onclick="savePage()">
               <i class="bi-save"></i> Salvar
            </button>
            <button class="btn btn-secondary" onclick="previewPage()">
               <i class="bi-eye"></i> Visualizar
            </button>
            <button class="btn btn-secondary" onclick="undoAction()">
               <i class="bi-arrow-counterclockwise"></i> Desfazer
            </button>
            <button class="btn btn-secondary" onclick="redoAction()">
               <i class="bi-arrow-clockwise"></i> Refazer
            </button>
            <div style="margin-left: auto;">
               <button class="btn btn-secondary" onclick="window.close()">
                  <i class="bi-x"></i> Fechar
               </button>
            </div>
         </div>

         <div class="canvas-area" id="canvas">
            <?php if ($page_data && !empty($page_data['elements'])): ?>
               <?php foreach ($page_data['elements'] as $element): ?>
                  <div class="canvas-element element-<?php echo $element['type']; ?>" data-type="<?php echo $element['type']; ?>" data-settings='<?php echo json_encode($element['settings']); ?>'>
                     <div class="element-controls">
                        <button onclick="editElement(this.parentNode.parentNode)" title="Editar">
                           <i class="bi-pencil"></i>
                        </button>
                        <button onclick="duplicateElement(this.parentNode.parentNode)" title="Duplicar">
                           <i class="bi-files"></i>
                        </button>
                        <button onclick="deleteElement(this.parentNode.parentNode)" title="Excluir">
                           <i class="bi-trash"></i>
                        </button>
                     </div>
                     <?php echo renderElement($element); ?>
                  </div>
               <?php endforeach; ?>
            <?php else: ?>
               <div class="empty-canvas">
                  <i class="bi-plus-circle"></i>
                  <p>Arraste elementos aqui para começar</p>
                  <p>ou use um template</p>
               </div>
            <?php endif; ?>
         </div>
      </div>

      <div class="editor-panel" id="element-panel">
         <h3><i class="bi-gear"></i> Propriedades</h3>
         <div class="panel-content">
            <p>Selecione um elemento para editar suas propriedades</p>
         </div>
      </div>
   </div>

   <script>
      // Configurações dos elementos
      const elementsConfig = <?php echo json_encode($elements); ?>;
      const templatesConfig = <?php echo json_encode($templates); ?>;

      // Editor principal
      class VisualEditor {
         constructor() {
            this.canvas = document.getElementById('canvas');
            this.panel = document.getElementById('element-panel');
            this.selectedElement = null;
            this.history = [];
            this.historyIndex = -1;
            this.elementCounter = 0;

            this.initDragAndDrop();
            this.initEventListeners();
            this.saveHistory();
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

            // Templates
            document.querySelectorAll('.template-item').forEach(item => {
               item.addEventListener('click', () => {
                  this.loadTemplate(item.dataset.template);
               });
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
            element.dataset.id = 'element_' + (++this.elementCounter);

            const config = elementsConfig[type];
            const settings = {};

            // Aplicar configurações padrão
            for (const [key, setting] of Object.entries(config.settings)) {
               settings[key] = setting.default;
            }

            element.dataset.settings = JSON.stringify(settings);

            // Renderizar elemento
            element.innerHTML = this.renderElementHTML(type, settings);

            return element;
         }

         renderElementHTML(type, settings) {
            let html = '<div class="element-controls">';
            html += '<button onclick="editElement(this.parentNode)" title="Editar"><i class="bi-pencil"></i></button>';
            html += '<button onclick="duplicateElement(this.parentNode)" title="Duplicar"><i class="bi-files"></i></button>';
            html += '<button onclick="deleteElement(this.parentNode)" title="Excluir"><i class="bi-trash"></i></button>';
            html += '</div>';

            switch (type) {
               case 'heading':
                  const tag = settings.tag || 'h2';
                  html += `<${tag} style="color: ${settings.color}; font-size: ${settings.size}px; text-align: ${settings.align};">${settings.text}</${tag}>`;
                  break;

               case 'text':
                  html += `<p style="color: ${settings.color}; font-size: ${settings.size}px; text-align: ${settings.align};">${settings.content}</p>`;
                  break;

               case 'image':
                  html += `<img src="${settings.src || 'imgs/placeholder.jpg'}" alt="${settings.alt}" style="width: ${settings.width}%; height: ${settings.height}px; border-radius: ${settings.border_radius}px;">`;
                  break;

               case 'button':
                  const btnClass = `btn btn-${settings.style} btn-${settings.size}`;
                  html += `<a href="${settings.link}" target="${settings.target}" class="${btnClass}">${settings.text}</a>`;
                  break;

               case 'container':
                  html += `<div style="width: ${settings.width}; padding: ${settings.padding}px; margin: ${settings.margin}px; background: ${settings.background}; border: ${settings.border};">Container</div>`;
                  break;

               case 'divider':
                  html += `<hr style="border: ${settings.height}px ${settings.style} ${settings.color}; width: ${settings.width}%;">`;
                  break;

               case 'spacer':
                  html += `<div style="height: ${settings.height}px;"></div>`;
                  break;

               default:
                  html += `<div>Elemento: ${type}</div>`;
            }

            return html;
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
            const elementConfig = elementsConfig[type];

            if (elementConfig) {
               const settings = JSON.parse(element.dataset.settings);
               this.panel.querySelector('.panel-content').innerHTML = this.generateSettingsHTML(elementConfig.settings, settings, element);
            }
         }

         hideElementPanel() {
            this.panel.querySelector('.panel-content').innerHTML = '<p>Selecione um elemento para editar suas propriedades</p>';
         }

         generateSettingsHTML(configSettings, currentSettings, element) {
            let html = '';
            for (const [key, setting] of Object.entries(configSettings)) {
               html += `<div class="setting-group">
                        <label>${setting.label}:</label>
                        ${this.generateInputHTML(key, setting, currentSettings[key], element)}
                    </div>`;
            }
            return html;
         }

         generateInputHTML(key, setting, value, element) {
            switch (setting.type) {
               case 'text':
                  return `<input type="text" name="${key}" value="${value}" class="form-control" onchange="updateElementSetting('${key}', this.value)">`;

               case 'textarea':
                  return `<textarea name="${key}" class="form-control" onchange="updateElementSetting('${key}', this.value)">${value}</textarea>`;

               case 'select':
                  return `<select name="${key}" class="form-control" onchange="updateElementSetting('${key}', this.value)">
                            ${setting.options.map(opt => `<option value="${opt}" ${opt === value ? 'selected' : ''}>${opt}</option>`).join('')}
                        </select>`;

               case 'number':
                  return `<input type="number" name="${key}" value="${value}" class="form-control" onchange="updateElementSetting('${key}', this.value)">`;

               case 'color':
                  return `<input type="color" name="${key}" value="${value}" class="form-control" onchange="updateElementSetting('${key}', this.value)">`;

               case 'image':
                  return `<input type="file" name="${key}" accept="image/*" class="form-control" onchange="updateElementImage('${key}', this)">`;

               case 'url':
                  return `<input type="url" name="${key}" value="${value}" class="form-control" onchange="updateElementSetting('${key}', this.value)">`;

               default:
                  return `<input type="text" name="${key}" value="${value}" class="form-control" onchange="updateElementSetting('${key}', this.value)">`;
            }
         }

         loadTemplate(templateId) {
            const template = templatesConfig.find(t => t.id === templateId);
            if (template) {
               this.canvas.innerHTML = '';

               template.elements.forEach(elementData => {
                  const element = this.createElementFromTemplate(elementData);
                  this.canvas.appendChild(element);
               });

               this.saveHistory();
            }
         }

         createElementFromTemplate(templateData) {
            const element = document.createElement('div');
            element.className = 'canvas-element element-' + templateData.type;
            element.dataset.type = templateData.type;
            element.dataset.id = 'element_' + (++this.elementCounter);
            element.dataset.settings = JSON.stringify(templateData.settings);

            element.innerHTML = this.renderElementHTML(templateData.type, templateData.settings);

            if (templateData.children) {
               templateData.children.forEach(childData => {
                  const child = this.createElementFromTemplate(childData);
                  element.appendChild(child);
               });
            }

            return element;
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
                  settings: JSON.parse(el.dataset.settings),
                  content: el.innerHTML
               }))
            };
         }
      }

      // Inicializar editor
      const editor = new VisualEditor();

      // Funções globais
      function updateElementSetting(key, value) {
         if (editor.selectedElement) {
            const settings = JSON.parse(editor.selectedElement.dataset.settings);
            settings[key] = value;
            editor.selectedElement.dataset.settings = JSON.stringify(settings);

            // Re-renderizar elemento
            const type = editor.selectedElement.dataset.type;
            editor.selectedElement.innerHTML = editor.renderElementHTML(type, settings);

            editor.saveHistory();
         }
      }

      function updateElementImage(key, input) {
         const file = input.files[0];
         if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
               updateElementSetting(key, e.target.result);
            };
            reader.readAsDataURL(file);
         }
      }

      function editElement(element) {
         editor.selectElement(element);
      }

      function duplicateElement(element) {
         const clone = element.cloneNode(true);
         clone.dataset.id = 'element_' + (++editor.elementCounter);
         element.parentNode.insertBefore(clone, element.nextSibling);
         editor.saveHistory();
      }

      function deleteElement(element) {
         if (confirm('Tem certeza que deseja excluir este elemento?')) {
            element.remove();
            editor.saveHistory();
         }
      }

      function savePage() {
         const data = editor.getPageData();
         const pageId = '<?php echo $page_id ?: "new_page"; ?>';

         fetch('save_visual_page.php', {
               method: 'POST',
               headers: {
                  'Content-Type': 'application/json'
               },
               body: JSON.stringify({
                  page_id: pageId,
                  data: data
               })
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
                    <title>Preview - Editor Visual</title>
                    <link rel="stylesheet" href="css/style.css">
                    <style>
                        body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                        .canvas-element { position: relative; margin-bottom: 10px; }
                        .element-controls { display: none; }
                    </style>
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
</body>

</html>

<?php
function renderElement($element)
{
   $type = $element['type'];
   $settings = $element['settings'];

   switch ($type) {
      case 'heading':
         $tag = $settings['tag'] ?? 'h2';
         $style = "color: {$settings['color']}; font-size: {$settings['size']}px; text-align: {$settings['align']};";
         return "<{$tag} style=\"{$style}\">{$settings['text']}</{$tag}>";

      case 'text':
         $style = "color: {$settings['color']}; font-size: {$settings['size']}px; text-align: {$settings['align']};";
         return "<p style=\"{$style}\">{$settings['content']}</p>";

      case 'image':
         $style = "width: {$settings['width']}%; height: {$settings['height']}px; border-radius: {$settings['border_radius']}px;";
         return "<img src=\"{$settings['src']}\" alt=\"{$settings['alt']}\" style=\"{$style}\">";

      case 'button':
         $btnClass = "btn btn-{$settings['style']} btn-{$settings['size']}";
         return "<a href=\"{$settings['link']}\" target=\"{$settings['target']}\" class=\"{$btnClass}\">{$settings['text']}</a>";

      case 'container':
         $style = "width: {$settings['width']}; padding: {$settings['padding']}px; margin: {$settings['margin']}px; background: {$settings['background']}; border: {$settings['border']};";
         return "<div style=\"{$style}\">Container</div>";

      case 'divider':
         $style = "border: {$settings['height']}px {$settings['style']} {$settings['color']}; width: {$settings['width']}%;";
         return "<hr style=\"{$style}\">";

      case 'spacer':
         return "<div style=\"height: {$settings['height']}px;\"></div>";

      default:
         return "<div>Elemento: {$type}</div>";
   }
}
?>