<?php

/**
 * Visualizador de Páginas Elementor
 */

session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

$page_id = $_GET['page_id'] ?? 0;

if (!$page_id || !isset($_SESSION['wp_token'])) {
   die('Página não encontrada ou não conectado ao WordPress');
}

class ElementorPreview
{
   private $wp_url;
   private $token;

   public function __construct($wp_url, $token)
   {
      $this->wp_url = rtrim($wp_url, '/');
      $this->token = $token;
   }

   public function getPageContent($page_id)
   {
      $url = $this->wp_url . '/wp-json/wp/v2/pages/' . $page_id;

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
         'Authorization: Bearer ' . $this->token
      ]);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $response = curl_exec($ch);
      curl_close($ch);

      return json_decode($response, true);
   }

   public function renderPage($page_data)
   {
      $html = '<!DOCTYPE html>';
      $html .= '<html lang="pt-BR">';
      $html .= '<head>';
      $html .= '<meta charset="UTF-8">';
      $html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
      $html .= '<title>' . htmlspecialchars($page_data['title']['rendered']) . '</title>';

      // CSS do Elementor
      $html .= '<link rel="stylesheet" href="' . $this->wp_url . '/wp-content/plugins/elementor/assets/css/frontend.min.css">';
      $html .= '<link rel="stylesheet" href="' . $this->wp_url . '/wp-content/plugins/elementor/assets/css/frontend-legacy.min.css">';

      // CSS customizado
      $html .= '<style>';
      $html .= 'body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }';
      $html .= '.preview-header { background: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }';
      $html .= '.preview-header h1 { margin: 0; color: #333; }';
      $html .= '.preview-header .meta { color: #666; font-size: 14px; margin-top: 5px; }';
      $html .= '.elementor-section { margin-bottom: 20px; }';
      $html .= '.elementor-container { max-width: 1200px; margin: 0 auto; }';
      $html .= '</style>';
      $html .= '</head>';
      $html .= '<body>';

      // Header da preview
      $html .= '<div class="preview-header">';
      $html .= '<h1>' . htmlspecialchars($page_data['title']['rendered']) . '</h1>';
      $html .= '<div class="meta">';
      $html .= 'ID: ' . $page_id . ' | ';
      $html .= 'Status: ' . $page_data['status'] . ' | ';
      $html .= 'Data: ' . date('d/m/Y H:i', strtotime($page_data['date']));
      $html .= '</div>';
      $html .= '</div>';

      // Conteúdo da página
      $html .= $page_data['content']['rendered'];

      $html .= '</body>';
      $html .= '</html>';

      return $html;
   }
}

$preview = new ElementorPreview($_SESSION['wp_config']['url'], $_SESSION['wp_token']);
$page_data = $preview->getPageContent($page_id);

if (!$page_data) {
   die('Erro ao carregar página');
}

echo $preview->renderPage($page_data);
