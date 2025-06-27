<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
   header('Location: admin.php');
   exit;
}

$config_file = 'especialidades_config.json';
$especialidades = $_POST['especialidades'] ?? [];

if (empty($especialidades)) {
   header('Location: gerenciar_especialidades.php?error=Nenhuma especialidade foi enviada');
   exit;
}

// Valida e processa as especialidades
$especialidades_processadas = [];
foreach ($especialidades as $especialidade) {
   $titulo = trim($especialidade['titulo'] ?? '');
   $descricao = trim($especialidade['descricao'] ?? '');
   $icone = trim($especialidade['icone'] ?? 'bi-star');
   $cor_icone = trim($especialidade['cor_icone'] ?? '#4f46e5');
   $cor_gradiente = trim($especialidade['cor_gradiente'] ?? '#7c3aed');
   $usar_gradiente = isset($especialidade['usar_gradiente']);
   $cor_fundo = trim($especialidade['cor_fundo'] ?? 'transparent');
   $usar_blur = isset($especialidade['usar_blur']);

   if (empty($titulo) || empty($descricao)) {
      header('Location: gerenciar_especialidades.php?error=Título e descrição são obrigatórios para todas as especialidades');
      exit;
   }

   $especialidades_processadas[] = [
      'titulo' => $titulo,
      'descricao' => $descricao,
      'icone' => $icone,
      'cor_icone' => $cor_icone,
      'cor_gradiente' => $cor_gradiente,
      'usar_gradiente' => $usar_gradiente,
      'cor_fundo' => $cor_fundo,
      'usar_blur' => $usar_blur
   ];
}

// Salva no arquivo JSON
if (file_put_contents($config_file, json_encode($especialidades_processadas, JSON_PRETTY_PRINT))) {
   header('Location: gerenciar_especialidades.php?success=Especialidades atualizadas com sucesso!');
} else {
   header('Location: gerenciar_especialidades.php?error=Erro ao salvar as especialidades');
}
exit;
