<?php
require_once "validador_acesso.php";
require_once "config.php";

$idUsuario = (int) $_SESSION['id'];

$stmt = $conexao->prepare("SELECT perfil FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$res = $stmt->get_result();
$row = $res->fetch_object();
$stmt->close();
$perfil = $row->perfil ?? '';
$tecnico = ($perfil === 'Tec' || $perfil === 'Adm');
$administrador = ($perfil === 'Adm');
$nome = htmlspecialchars($_SESSION['nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8');

$itens = [
    ['titulo' => 'Abrir chamado', 'icone' => 'file-plus', 'link' => 'abrir_chamado.php', 'permitido' => true],
    ['titulo' => 'Consultar chamados', 'icone' => 'search', 'link' => 'consultar_chamado.php', 'permitido' => true],
    ['titulo' => 'Editar chamado', 'icone' => 'pencil', 'link' => 'editar_chamado.php', 'permitido' => $tecnico],
    ['titulo' => 'Autorizar', 'icone' => 'shield', 'link' => 'autorizar_usuarios.php', 'permitido' => $administrador],
    ['titulo' => 'Usuários', 'icone' => 'users', 'link' => 'usuarios.php', 'permitido' => $administrador],
    ['titulo' => 'Relatórios', 'icone' => 'chart', 'link' => 'relatorios.php', 'permitido' => $administrador]
];
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
// Mantém a regra original de consulta por perfil.
if ($administrador) {
    $sqlChamados = "SELECT * FROM chamados ORDER BY id_chamado DESC";
} else {
    $idUsuario = (int) $_SESSION['id'];
    $sqlChamados = "SELECT * FROM chamados WHERE id_usuario = $idUsuario ORDER BY id_chamado DESC";
}
$resChamados = $conexao->query($sqlChamados);
$chamados = $resChamados ? $resChamados->fetch_all(MYSQLI_ASSOC) : [];
$resUsuarios = $conexao->query("SELECT id_usuario, nome FROM usuarios");
$usuarios = [];
if ($resUsuarios) {
    while ($usuario = $resUsuarios->fetch_assoc()) {
        $usuarios[(int)$usuario['id_usuario']] = $usuario['nome'];
    }
}
// A coluna de data pode ainda não existir na versão atual do banco.
$temDataAbertura = false;
$colunasData = $conexao->query("SHOW COLUMNS FROM chamados LIKE 'data_abertura'");
if ($colunasData) { $temDataAbertura = $colunasData->num_rows > 0; }
function e($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Consultar Chamados | App Help Desk</title>
<link rel="icon" href="imagens/logo.png" type="image/x-icon">
<link rel="stylesheet" href="css/consultar_chamado.css">
</head>
<body>
<div class="layout">
<aside class="sidebar" id="sidebar">
  <div class="brand"><img src="imagens/logo.png" alt="Logo Help Desk">Help Desk <button class="mobile-close" type="button" aria-label="Fechar menu" onclick="toggleMenu(false)">×</button></div>
  <nav class="nav" aria-label="Menu principal">
    <a href="home.php"><?= icone('home') ?> Início</a>
    <?php foreach ($itens as $item): ?>
      <?php if ($item['permitido']): ?>
        <a class="<?= $item['link'] === 'consultar_chamado.php' ? 'active' : '' ?>" href="<?= htmlspecialchars($item['link'], ENT_QUOTES, 'UTF-8') ?>"><?= icone($item['icone']) ?> <?= htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8') ?></a>
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
    <section class="form-card consulta-card">
      <div class="form-head">
        <span class="form-icon" aria-hidden="true"><?= icone('search') ?></span>
        <div><h1>Consultar Chamados</h1><p>Visualize e acompanhe suas solicitações de suporte.</p></div>
      </div>
      <div class="form-body">
        <div class="filtros">
          <div class="filtro-pesquisa">
            <label for="pesquisa">Pesquisar chamado</label>
            <input id="pesquisa" type="search" placeholder="Busque pelo título, categoria ou número..." oninput="filtrarChamados()">
          </div>
          <div class="filtro-categoria">
            <label for="categoriaFiltro">Categoria</label>
            <select id="categoriaFiltro" onchange="filtrarChamados()">
              <option value="">Todas</option>
              <option>Impressora</option><option>Hardware</option><option>Software</option>
              <option>Rede</option><option>Formatação</option><option>Orçamento</option><option>Outros</option>
            </select>
          </div>
        </div>
        <div class="resumo-consulta"><span><strong id="quantidade"><?= count($chamados) ?></strong> chamado(s) encontrado(s)</span></div>
        <div class="tabela-rolagem">
          <table class="tabela-chamados">
            <thead><tr><th>Nº</th><th>Título e descrição</th><th>Categoria</th><th>Solicitante</th><th>Data de abertura</th><th>Status</th></tr></thead>
            <tbody id="listaChamados">
              <?php foreach ($chamados as $chamado):
                $status = trim((string)($chamado['statuschamado'] ?? ''));
                $statusTexto = $status !== '' ? $status : 'Não informado';
                $normalizado = strtolower($statusTexto);
                $classeStatus = str_contains($normalizado, 'finaliz') ? 'finalizado' : (str_contains($normalizado, 'andamento') ? 'andamento' : (str_contains($normalizado, 'abert') ? 'aberto' : 'neutro'));
              ?>
              <tr data-categoria="<?= e($chamado['categoria'] ?? '') ?>">
                <td class="numero">#<?= e($chamado['id_chamado']) ?></td>
                <td><strong><?= e($chamado['titulo'] ?? '') ?></strong><div class="descricao-chamado"><?= nl2br(e($chamado['descricao'] ?? '')) ?></div></td>
                <td><?= e($chamado['categoria'] ?? '') ?></td>
                <td><?= e($usuarios[(int)($chamado['id_usuario'] ?? 0)] ?? 'Não identificado') ?></td>
                <td><?php
                  $dataOriginal = $temDataAbertura ? ($chamado['data_abertura'] ?? null) : null;
                  $dataFormatada = 'Não registrada';
                  if ($dataOriginal && $dataOriginal !== '0000-00-00 00:00:00') {
                      try { $dataFormatada = (new DateTime($dataOriginal))->format('d/m/Y H:i'); }
                      catch (Exception $erro) { $dataFormatada = 'Não registrada'; }
                  }
                  echo e($dataFormatada);
                ?></td>
                <td><span class="status <?= $classeStatus ?>"><?= e($statusTexto) ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <p id="semResultados" class="vazio" <?= count($chamados) ? 'hidden' : '' ?>>Nenhum chamado encontrado.</p>
        <div class="acoes"><a href="home.php" class="botao secundario">Voltar</a><a href="abrir_chamado.php" class="botao primario">+ Abrir chamado</a></div>
      </div>
    </section>
  </div>
</main>
</div>
<script>function toggleMenu(open){document.getElementById("sidebar").classList.toggle("open",open)}function filtrarChamados(){
  const busca=document.getElementById('pesquisa').value.toLocaleLowerCase('pt-BR').trim();
  const categoria=document.getElementById('categoriaFiltro').value.toLocaleLowerCase('pt-BR');
  let visiveis=0;
  document.querySelectorAll('#listaChamados tr').forEach(linha=>{
    const texto=linha.textContent.toLocaleLowerCase('pt-BR');
    const cat=(linha.dataset.categoria||'').toLocaleLowerCase('pt-BR');
    const mostrar=texto.includes(busca)&&(!categoria||cat===categoria);
    linha.hidden=!mostrar;
    if(mostrar)visiveis++;
  });
  document.getElementById('quantidade').textContent=visiveis;
  document.getElementById('semResultados').hidden=visiveis!==0;
}</script>
</body>
</html>
