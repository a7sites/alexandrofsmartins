<?php
$config = [
   'titulo' => 'Alexandro Martins',
   'logomarca' => 'imgs/a7site.svg',
   'menu' => [
      ["nome" => "Principal", "link" => "#inicio"],
      ["nome" => "Especialidades", "link" => "#especialidades"],
      ["nome" => "Sobre Mim", "link" => "#sobremim"],
      ["nome" => "Meu GitHub", "link" => "#"],
      ["nome" => "Projetos", "link" => "#"]
   ]
];
if (file_exists(__DIR__ . '/site_config.json')) {
   $json = json_decode(file_get_contents(__DIR__ . '/site_config.json'), true);
   if ($json) $config = array_merge($config, $json);
}

// Carrega especialidades
$especialidades = [
   [
      'titulo' => 'Criação de Sites',
      'descricao' => 'Especialista em criação de sites modernos, responsivos e otimizados para resultados. Transformo ideias em soluções digitais com design atrativo, performance, segurança e foco na experiência do usuário.',
      'icone' => 'bi-code-slash',
      'cor_icone' => '#4f46e5',
      'usar_gradiente' => false,
      'cor_fundo' => 'transparent'
   ],
   [
      'titulo' => 'Designer',
      'descricao' => 'Designer criativo e detalhista, com foco em identidade visual, estética funcional e comunicação impactante. Transformo conceitos em visuais únicos que conectam marcas ao seu público.',
      'icone' => 'bi-pencil-square',
      'cor_icone' => '#7c3aed',
      'usar_gradiente' => false,
      'cor_fundo' => 'transparent'
   ],
   [
      'titulo' => 'WordPress',
      'descricao' => 'Designer especializado em WordPress, unindo criatividade e funcionalidade para criar sites visualmente marcantes, responsivos e fáceis de gerenciar. Transformo ideias em experiências digitais intuitivas e profissionais.',
      'icone' => 'bi-wordpress',
      'cor_icone' => '#059669',
      'usar_gradiente' => false,
      'cor_fundo' => 'transparent'
   ]
];

if (file_exists(__DIR__ . '/especialidades_config.json')) {
   $especialidades = json_decode(file_get_contents(__DIR__ . '/especialidades_config.json'), true) ?? $especialidades;
}

function isTransparent($color)
{
   $color = strtolower(trim($color));
   if ($color === 'transparent') return true;
   if (preg_match('/^#([0-9a-f]{8})$/i', $color, $m)) {
      // Hexa com alpha 0
      return substr($m[1], 6, 2) === '00';
   }
   if (preg_match('/^rgba?\(([^)]+)\)$/', $color, $m)) {
      $parts = explode(',', $m[1]);
      if (count($parts) === 4 && floatval($parts[3]) == 0) return true;
   }
   return false;
}

function hexToRgba($hex)
{
   $hex = ltrim($hex, '#');
   if (strlen($hex) === 8) {
      $r = hexdec(substr($hex, 0, 2));
      $g = hexdec(substr($hex, 2, 2));
      $b = hexdec(substr($hex, 4, 2));
      $a = round(hexdec(substr($hex, 6, 2)) / 255, 2);
      return "rgba($r, $g, $b, $a)";
   }
   if (strlen($hex) === 6) {
      $r = hexdec(substr($hex, 0, 2));
      $g = hexdec(substr($hex, 2, 2));
      $b = hexdec(substr($hex, 4, 2));
      return "rgb($r, $g, $b)";
   }
   return "#$hex";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($config['titulo']); ?></title>

   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/efects.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
   <script src="js/script.js"></script>

</head>

<body>

   <div class="particulas"></div>
   <header>
      <div class="interface">
         <div class="container_logo">
            <a href="/">
               <img class="logomarca" src="<?php echo htmlspecialchars($config['logomarca']); ?>" alt="Logomarca">
            </a>
         </div>
         <div class="menu_header">
            <ul>
               <?php foreach ($config['menu'] as $item): ?>
                  <li><a href="<?php echo htmlspecialchars($item['link']); ?>"><?php echo htmlspecialchars($item['nome']); ?></a></li>
               <?php endforeach; ?>
            </ul>
         </div>
         <div class="btn_falecomigo">
            <a href="#falecomigo">
               <button>Fale Comigo!</button>
            </a>
         </div>
      </div>
   </header>

   <main id="inicio" class="personal">
      <img src="imgs/img_perfil.jpeg" alt="Programador Alexandro Martins" class="img_perfil">
      <h1>Alexandro F. S. Martins</h1>
      <p class="p-personal">Desenvolvedor Front End</p>
   </main>

   <section id="sobremim" class="sobre">
      <div class="interface">
         <h2 class="title_page"><?php echo strip_tags($config['sobre_titulo'] ?? 'Sobre <span>Mim</span>', '<span>'); ?></h2>
         <div class="caixa_desc">
            <div class="desc_p"><?php echo $config['sobre_conteudo'] ?? ''; ?></div>
         </div>
      </div>
   </section>

   <section id="especialidades" class="especialidades">
      <div class="interface">
         <h2 class="title_page">Minha <span>Especialidades.</span></h2>
         <div class="flex">
            <?php foreach ($especialidades as $especialidade): ?>
               <?php
               // Fundo
               if (isTransparent($especialidade['cor_fundo'])) {
                  $corFundo = 'transparent';
               } elseif (preg_match('/^#([0-9a-f]{8})$/i', $especialidade['cor_fundo'])) {
                  $corFundo = hexToRgba($especialidade['cor_fundo']);
               } else {
                  $corFundo = htmlspecialchars($especialidade['cor_fundo']);
               }
               // Ícone
               if (isTransparent($especialidade['cor_icone'])) {
                  $corIcone = 'transparent';
               } elseif (preg_match('/^#([0-9a-f]{8})$/i', $especialidade['cor_icone'])) {
                  $corIcone = hexToRgba($especialidade['cor_icone']);
               } else {
                  $corIcone = htmlspecialchars($especialidade['cor_icone']);
               }
               // Gradiente
               if (isTransparent($especialidade['cor_gradiente'])) {
                  $corGradiente = 'transparent';
               } elseif (preg_match('/^#([0-9a-f]{8})$/i', $especialidade['cor_gradiente'])) {
                  $corGradiente = hexToRgba($especialidade['cor_gradiente']);
               } else {
                  $corGradiente = htmlspecialchars($especialidade['cor_gradiente']);
               }
               $usarGradiente = !empty($especialidade['usar_gradiente']);
               $usarBlur = !empty($especialidade['usar_blur']);
               $iconeStyle = '';
               if ($corIcone === 'transparent') {
                  $iconeStyle = 'color: transparent !important; background: none !important;';
               } elseif ($usarGradiente && $corIcone !== 'transparent' && $corGradiente !== 'transparent') {
                  $iconeStyle = 'background: linear-gradient(45deg, ' . $corIcone . ', ' . $corGradiente . '); -webkit-background-clip: text; -webkit-text-fill-color: transparent;';
               } else {
                  $iconeStyle = 'color: ' . $corIcone . '; background: none;';
               }
               ?>
               <div class="card_box" style="background-color: <?php echo $corFundo; ?>;<?php echo $usarBlur ? 'backdrop-filter: blur(10px);' : ''; ?>;">
                  <i class="<?php echo htmlspecialchars($especialidade['icone']); ?>" style="<?php echo $iconeStyle; ?>"></i>
                  <h3><?php echo htmlspecialchars($especialidade['titulo']); ?></h3>
                  <p><?php echo htmlspecialchars($especialidade['descricao']); ?></p>
               </div>
            <?php endforeach; ?>
         </div>
      </div>
   </section>

   <section id="falecomigo" class="falecomigo">
      <div class="interface">
         <h2 class="title_page">Fale <span>Comigo</span></h2>

         <form class="form_whatsapp" id="formulario" onSubmit="enviarWhatsapp(event)">

            <div id="erroMensagem" class="erro-popup">Erro ao enviar sua mensagem. Todos os campos precisam ser preenchidos.</div>

            <div class="grupo_form">
               <input placeholder="Seu Nome" id="nome" class="campo_form">
            </div>
            <div class="grupo_form">
               <textarea class="campo_form" rows="5" id="msg" placeholder="Sua Mensagem"></textarea>
            </div>
            <div class="grupo_form">
               <button type="submit" class="btn_form">Enviar WhatsApp</button>
            </div>

         </form>

      </div>

   </section>
</body>

</html>