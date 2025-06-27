<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: admin.php');
    exit;
}

$config_file = 'site_config.json';
$titulo = trim($_POST['titulo'] ?? '');
$menu_nomes = $_POST['menu_nome'] ?? [];
$menu_links = $_POST['menu_link'] ?? [];
$sobre_conteudo = trim($_POST['sobre_conteudo'] ?? '');
$sobre_titulo = trim($_POST['sobre_titulo'] ?? 'Sobre Mim');

if (empty($titulo)) {
    header('Location: editar_site.php?error=O título do site é obrigatório');
    exit;
}

// Monta o menu
$menu = [];
for ($i = 0; $i < count($menu_nomes); $i++) {
    $nome = trim($menu_nomes[$i]);
    $link = trim($menu_links[$i]);
    if ($nome && $link) {
        $menu[] = ["nome" => $nome, "link" => $link];
    }
}
if (count($menu) === 0) {
    header('Location: editar_site.php?error=O menu deve ter pelo menos um item');
    exit;
}

// Carrega config atual
$config = file_exists($config_file) ? json_decode(file_get_contents($config_file), true) : [];
$logomarca = $config['logomarca'] ?? '';

// Upload da logomarca
if (isset($_FILES['logomarca']) && $_FILES['logomarca']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['logomarca']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
    if (!in_array($ext, $permitidas)) {
        header('Location: editar_site.php?error=Formato de imagem não permitido');
        exit;
    }
    $novo_nome = 'imgs/logomarca_' . time() . '.' . $ext;
    if (move_uploaded_file($_FILES['logomarca']['tmp_name'], $novo_nome)) {
        $logomarca = $novo_nome;
    } else {
        header('Location: editar_site.php?error=Erro ao fazer upload da logomarca');
        exit;
    }
}

// Salva config
file_put_contents($config_file, json_encode([
    'titulo' => $titulo,
    'logomarca' => $logomarca,
    'menu' => $menu,
    'sobre_conteudo' => $sobre_conteudo,
    'sobre_titulo' => $sobre_titulo
], JSON_PRETTY_PRINT));

header('Location: editar_site.php?success=Informações do site atualizadas com sucesso!');
exit;
