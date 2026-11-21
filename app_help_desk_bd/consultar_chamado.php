<?php
require_once "validador_acesso.php";
require_once "config.php";

$sql = "SELECT perfil FROM usuarios WHERE id_usuario = " . (int)$_SESSION['id'];
$res = $conexao->query($sql);
$row = $res ? $res->fetch_object() : null;
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
<style>
:root{--navy:#0d315d;--navy-dark:#092747;--blue:#0867d5;--text:#122b4a;--muted:#687c94;--bg:#eff5ff;--border:#e2eaf6}
*{box-sizing:border-box}body{margin:0;font-family:Arial,Helvetica,sans-serif;color:var(--text);background:var(--bg)}
.layout{display:flex;min-height:100vh}.sidebar{width:224px;flex:none;background:linear-gradient(180deg,#103c70,var(--navy-dark));color:#fff;padding:22px 12px;display:flex;flex-direction:column}
.brand{display:flex;align-items:center;gap:10px;padding:3px 10px 27px;font-size:19px;font-weight:700}.brand img{width:33px;height:33px;object-fit:contain}.nav{display:flex;flex-direction:column;gap:5px}.nav a,.nav .locked{display:flex;align-items:center;gap:12px;min-height:43px;padding:11px 12px;color:#e6f0ff;text-decoration:none;border-radius:7px;font-size:13px}.nav a:hover,.nav a.active{background:#1769c8;color:#fff}.nav .locked{opacity:.42;cursor:not-allowed}.nav svg{width:18px;height:18px;flex:none}.nav .lock{font-size:11px;margin-left:auto}.logout{margin-top:auto;border-top:1px solid #ffffff2c;padding-top:12px}
.main{flex:1;min-width:0}.topbar{height:66px;background:linear-gradient(90deg,#0c3d78,#0961c5);display:flex;align-items:center;justify-content:space-between;padding:0 31px;color:#fff;box-shadow:0 2px 9px #123e7220}.topbar-title{font-size:16px;font-weight:700}.account{display:flex;align-items:center;gap:10px;font-size:13px;font-weight:700}.avatar{width:32px;height:32px;border-radius:50%;display:grid;place-items:center;background:#2d83e5;font-size:17px}
.content{padding:0;max-width:none;margin:0;min-height:calc(100vh - 66px);background:#fff}
.form-card{max-width:none;margin:0;background:white;border:0;border-radius:0;box-shadow:none;overflow:hidden}
.form-head{display:flex;align-items:center;gap:12px;padding:23px 32px;background:#f7faff;border-bottom:1px solid var(--border)}
.form-icon{width:38px;height:38px;border-radius:10px;display:grid;place-items:center;background:#e8f2ff;color:var(--blue)}
.form-icon svg{width:21px;height:21px}
.form-head h1{font-size:19px;margin:0 0 5px}.form-head p{font-size:13px;color:var(--muted);margin:0}
.form-body{padding:28px 32px 36px}.campo{margin-bottom:14px}label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}
input,select,textarea{display:block;width:100%;font:14px Arial,Helvetica,sans-serif;color:var(--text);border:1px solid #cedaeb;border-radius:7px;padding:9px 12px;background:#fff;outline:none}
input:focus,select:focus,textarea:focus{border-color:var(--blue);box-shadow:0 0 0 3px #0867d51c}
textarea{resize:vertical;min-height:78px}.acoes{display:flex;gap:10px;flex-wrap:wrap;margin-top:17px}
.botao{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:7px;padding:10px 17px;font-weight:700;font-size:13px;text-decoration:none;cursor:pointer}
.primario{background:var(--blue);color:white}.primario:hover{background:#0756b8}.secundario{background:#e9eff7;color:#233954}.secundario:hover{background:#dbe5f2}
.alerta{margin:0 0 20px;padding:13px 16px;border-radius:8px;font-size:14px}.sucesso{background:#e9f9ef;color:#176a38}.erro{background:#fff0f0;color:#a52d2d}
.mobile-toggle{display:none}.mobile-close{display:none}
@media(max-width:850px){.sidebar{width:190px}.content{padding:0}}
@media(max-width:650px){.layout{display:block}.sidebar{position:fixed;z-index:20;top:0;bottom:0;left:0;width:255px;transform:translateX(-100%);transition:transform .2s;box-shadow:10px 0 30px #0002}.sidebar.open{transform:translateX(0)}.mobile-close{display:block;margin-left:auto;background:none;border:0;color:#fff;font-size:25px;cursor:pointer}.mobile-toggle{display:inline-flex;align-items:center;justify-content:center;border:0;background:none;color:#fff;padding:6px;cursor:pointer}.mobile-toggle svg{width:24px;height:24px}.topbar{padding:0 14px;gap:10px}.topbar-title{margin-right:auto}.account{font-size:12px}.content{padding:0}.form-head,.form-body{padding:20px 16px}.form-head h1{font-size:19px}}
.consulta-card{max-width:none;width:100%;min-height:calc(100vh - 66px)}
.filtros{display:flex;gap:14px;align-items:end;flex-wrap:wrap;margin-bottom:18px}
.filtro-pesquisa{flex:2;min-width:240px}.filtro-categoria{flex:1;min-width:170px}
.filtros label{display:block;margin-bottom:7px}.filtros input,.filtros select{width:100%}
.resumo-consulta{font-size:12px;color:var(--muted);margin-bottom:12px}
.tabela-rolagem{overflow-x:auto}.tabela-chamados{width:100%;border-collapse:collapse;text-align:left;font-size:13px}
.tabela-chamados th{background:#edf3fb;color:#213b60;padding:13px 12px;font-size:12px;white-space:nowrap}
.tabela-chamados td{padding:15px 12px;border-bottom:1px solid var(--border);vertical-align:top}
.tabela-chamados tbody tr:hover{background:#f8fbff}.numero{font-weight:700;color:var(--blue);white-space:nowrap}
.descricao-chamado{margin-top:6px;color:var(--muted);line-height:1.45;max-width:340px;overflow-wrap:anywhere}
.status{display:inline-block;padding:6px 9px;border-radius:20px;font-weight:700;font-size:11px;white-space:nowrap}
.status.finalizado{background:#dcf7e5;color:#14733b}.status.andamento{background:#e0efff;color:#145caa}.status.aberto{background:#fff1cf;color:#97610b}.status.neutro{background:#eef1f5;color:#53657b}
.vazio{text-align:center;color:var(--muted);padding:24px}.vazio[hidden]{display:none}
@media(max-width:650px){.filtros{display:block}.filtro-categoria{margin-top:12px}.tabela-chamados{min-width:820px}}
</style>
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
