<?php
require_once "validador_acesso.php";
require_once "config.php";

$idUsuario = (int)($_SESSION['id'] ?? 0);
$consultaPerfil = $conexao->query("SELECT perfil FROM usuarios WHERE id_usuario = $idUsuario");
$usuarioAtual = $consultaPerfil ? $consultaPerfil->fetch_object() : null;
$perfil = $usuarioAtual->perfil ?? '';
$tecnico = in_array($perfil, ['Tec', 'Adm'], true);
$administrador = ($perfil === 'Adm');
$nome = htmlspecialchars($_SESSION['nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8');

if (!$tecnico) {
    http_response_code(403);
    exit('Acesso não autorizado.');
}

$idChamado = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idChamado || $idChamado < 1) {
    http_response_code(400);
    exit('Identificador de chamado inválido.');
}

$stmt = $conexao->prepare('SELECT * FROM chamados WHERE id_chamado = ?');
$stmt->bind_param('i', $idChamado);
$stmt->execute();
$res = $stmt->get_result();
$chamado = $res->fetch_object();
$stmt->close();
if (!$chamado) {
    http_response_code(404);
    exit('Chamado não encontrado.');
}

function h($valor) {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}
function icone($nome) {
    $paths = [
        'home' => '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/>',
        'file-plus' => '<path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M13 2v7h7M12 13v6m-3-3h6"/>',
        'search' => '<circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/>',
        'pencil' => '<path d="m4 20 4.5-1 11-11a2.1 2.1 0 0 0-3-3l-11 11L4 20zM14.5 7.5l3 3"/>',
        'shield' => '<path d="M12 2 4 5v6c0 5 3.4 8.5 8 11 4.6-2.5 8-6 8-11V5z"/><path d="m8 12 3 3 5-6"/>',
        'users' => '<circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M16 5a3 3 0 0 1 0 6m2 3a5 5 0 0 1 3 5v1"/>',
        'chart' => '<path d="M4 21V11h4v10M10 21V5h4v16M16 21v-8h4v8M2 21h20"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>'
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$nome] ?? '').'</svg>';
}
$itens = [
    ['titulo'=>'Abrir chamado','icone'=>'file-plus','link'=>'abrir_chamado.php','permitido'=>true],
    ['titulo'=>'Consultar chamados','icone'=>'search','link'=>'consultar_chamado.php','permitido'=>true],
    ['titulo'=>'Editar chamado','icone'=>'pencil','link'=>'editar_chamado.php','permitido'=>$tecnico],
    ['titulo'=>'Autorizar','icone'=>'shield','link'=>'autorizar_usuarios.php','permitido'=>$administrador],
    ['titulo'=>'Usuários','icone'=>'users','link'=>'usuarios.php','permitido'=>$administrador],
    ['titulo'=>'Relatórios','icone'=>'chart','link'=>'relatorios.php','permitido'=>$administrador]
];
$categorias = ['Impressora','Hardware','Software','Rede','Formatação','Orçamento','Outros'];
$statusDisponiveis = ['Aberto','Em andamento','Finalizado'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Editar Ordem de Serviço | App Help Desk</title>
<link rel="icon" href="imagens/logo.png" type="image/x-icon">
<link rel="stylesheet" href="css/edit_chamado.css">
</head>
<body>
<div class="layout">
<aside class="sidebar" id="sidebar">
  <div class="brand"><img src="imagens/logo.png" alt="Logo Help Desk">Help Desk <button class="mobile-close" type="button" aria-label="Fechar menu" onclick="toggleMenu(false)">×</button></div>
  <nav class="nav" aria-label="Menu principal">
    <a href="home.php"><?= icone('home') ?> Início</a>
    <?php foreach ($itens as $item): ?>
      <?php if ($item['permitido']): ?>
        <a class="<?= $item['link'] === 'editar_chamado.php' ? 'active' : '' ?>" href="<?= h($item['link']) ?>"><?= icone($item['icone']) ?> <?= h($item['titulo']) ?></a>
      <?php else: ?>
        <span class="locked" title="Disponível para outro perfil"><?= icone($item['icone']) ?> <?= h($item['titulo']) ?> <span class="lock">🔒</span></span>
      <?php endif; ?>
    <?php endforeach; ?>
  </nav>
  <nav class="nav logout"><a href="logoff.php"><?= icone('logout') ?> Sair</a></nav>
</aside>
<main class="main">
  <header class="topbar">
    <button class="mobile-toggle" type="button" aria-label="Abrir menu" onclick="toggleMenu(true)"><?= icone('menu') ?></button>
    <span class="topbar-title">Help Desk</span>
    <div class="account"><span class="avatar" aria-hidden="true">●</span><span><?= $nome ?></span></div>
  </header>
  <div class="content">
    <div class="form-head">
      <span class="form-icon"><?= icone('pencil') ?></span>
      <div><h1>Editar Chamado #<?= (int)$chamado->id_chamado ?></h1><p>Atualize as informações da ordem de serviço.</p></div>
    </div>
    <div class="form-body">
      <form action="conf_edit_chamado.php" method="POST">
        <input type="hidden" name="id_chamado" value="<?= (int)$chamado->id_chamado ?>">
        <div class="form-grid">
          <div class="campo full"><label for="titulo">Título *</label><input id="titulo" name="titulo" type="text" value="<?= h($chamado->titulo) ?>" required autofocus></div>
          <div class="campo"><label for="categoria">Categoria *</label><select id="categoria" name="categoria" required>
            <?php if (!in_array($chamado->categoria, $categorias, true)): ?><option value="<?= h($chamado->categoria) ?>" selected><?= h($chamado->categoria) ?></option><?php endif; ?>
            <?php foreach ($categorias as $categoria): ?><option value="<?= h($categoria) ?>" <?= $chamado->categoria === $categoria ? 'selected' : '' ?>><?= h($categoria) ?></option><?php endforeach; ?>
          </select></div>
          <div class="campo"><label for="status">Status *</label><select id="status" name="status" required onchange="atualizarCorStatus()">
            <?php if (!in_array($chamado->statuschamado, $statusDisponiveis, true)): ?><option value="<?= h($chamado->statuschamado) ?>" selected><?= h($chamado->statuschamado ?: 'Selecione o status') ?></option><?php endif; ?>
            <?php foreach ($statusDisponiveis as $status): ?><option value="<?= h($status) ?>" <?= $chamado->statuschamado === $status ? 'selected' : '' ?>><?= h($status) ?></option><?php endforeach; ?>
          </select></div>
          <div class="campo full"><label for="descricao">Descrição do Usuário</label><textarea id="descricao" rows="3" readonly><?= h(trim((string)$chamado->descricao)) ?></textarea><div class="helper">Informação original do solicitante, disponível apenas para leitura.</div></div>
          <div class="campo full"><label for="descricaotecnico">Descrição do Técnico *</label><textarea id="descricaotecnico" name="descricaotecnico" rows="3" required><?= h(trim((string)$chamado->descricaotecnico)) ?></textarea></div>
          <div class="campo"><label for="valor">Valor *</label><input id="valor" name="valor" type="text" value="<?= h($chamado->valor) ?>" required></div>
        </div>
        <div class="acoes"><button type="submit" class="botao primario">Salvar alterações</button><a href="editar_chamado.php" class="botao secundario">Cancelar</a></div>
      </form>
    </div>
  </div>
</main>
</div>
<script>function toggleMenu(open){document.getElementById('sidebar').classList.toggle('open',open)}</script>
</body>
</html>
