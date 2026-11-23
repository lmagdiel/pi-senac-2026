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
if (!$tecnico) { http_response_code(403); exit('Acesso restrito a técnicos e administradores.'); }
$mensagem = '';
if (($_GET['delete'] ?? '') === 'sucesso') $mensagem = 'Chamado excluído com sucesso!';
if (($_GET['edicao'] ?? '') === 'sucesso') $mensagem = 'Chamado editado com sucesso!';
$chamados = $conexao->query("SELECT c.*, u.nome AS nome_usuario FROM chamados c LEFT JOIN usuarios u ON u.id_usuario = c.id_usuario ORDER BY c.id_chamado DESC");
$temData = (bool)$conexao->query("SHOW COLUMNS FROM chamados LIKE 'data_abertura'")->num_rows;
function seguro($valor) { return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Editar Chamados | App Help Desk</title>
<link rel="icon" href="imagens/logo.png" type="image/x-icon">
<style>
:root{--navy:#0d315d;--navy-dark:#092747;--blue:#0867d5;--text:#122b4a;--muted:#687c94;--bg:#eff5ff;--border:#e2eaf6}
*{box-sizing:border-box}body{margin:0;font-family:Arial,Helvetica,sans-serif;color:var(--text);background:var(--bg)}
.layout{display:flex;min-height:100vh}.sidebar{width:224px;flex:none;background:linear-gradient(180deg,#103c70,var(--navy-dark));color:#fff;padding:22px 12px;display:flex;flex-direction:column}
.brand{display:flex;align-items:center;gap:10px;padding:3px 10px 27px;font-size:19px;font-weight:700}.brand img{width:33px;height:33px;object-fit:contain}.nav{display:flex;flex-direction:column;gap:5px}.nav a,.nav .locked{display:flex;align-items:center;gap:12px;min-height:43px;padding:11px 12px;color:#e6f0ff;text-decoration:none;border-radius:7px;font-size:13px}.nav a:hover,.nav a.active{background:#1769c8;color:#fff}.nav .locked{opacity:.42;cursor:not-allowed}.nav svg{width:18px;height:18px;flex:none}.nav .lock{font-size:11px;margin-left:auto}.logout{margin-top:auto;border-top:1px solid #ffffff2c;padding-top:12px}
.main{flex:1;min-width:0}.topbar{height:66px;background:linear-gradient(90deg,#0c3d78,#0961c5);display:flex;align-items:center;justify-content:space-between;padding:0 31px;color:#fff;box-shadow:0 2px 9px #123e7220}.topbar-title{font-size:16px;font-weight:700}.account{display:flex;align-items:center;gap:10px;font-size:13px;font-weight:700}.avatar{width:32px;height:32px;border-radius:50%;display:grid;place-items:center;background:#2d83e5;font-size:17px}
.content{padding:0;max-width:none;margin:0;min-height:calc(100vh - 66px);background:#fff}
.form-card{width:100%;max-width:none;min-height:calc(100vh - 66px);margin:0;background:white;border:0;border-radius:0;box-shadow:none;overflow:hidden}
.form-head{display:flex;align-items:center;gap:12px;padding:22px 28px;background:#f7faff;border-bottom:1px solid var(--border)}
.form-icon{width:38px;height:38px;border-radius:10px;display:grid;place-items:center;background:#e8f2ff;color:var(--blue)}
.form-icon svg{width:21px;height:21px}
.form-head h1{font-size:19px;margin:0 0 5px}.form-head p{font-size:13px;color:var(--muted);margin:0}
.form-body{padding:26px 28px 30px}.campo{margin-bottom:14px}label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}
input,select,textarea{display:block;width:100%;font:14px Arial,Helvetica,sans-serif;color:var(--text);border:1px solid #cedaeb;border-radius:7px;padding:9px 12px;background:#fff;outline:none}
input:focus,select:focus,textarea:focus{border-color:var(--blue);box-shadow:0 0 0 3px #0867d51c}
textarea{resize:vertical;min-height:78px}.acoes{display:flex;gap:10px;flex-wrap:wrap;margin-top:17px}
.botao{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:7px;padding:10px 17px;font-weight:700;font-size:13px;text-decoration:none;cursor:pointer}
.primario{background:var(--blue);color:white}.primario:hover{background:#0756b8}.secundario{background:#e9eff7;color:#233954}.secundario:hover{background:#dbe5f2}
.alerta{margin:0 0 20px;padding:13px 16px;border-radius:8px;font-size:14px}.sucesso{background:#e9f9ef;color:#176a38}.erro{background:#fff0f0;color:#a52d2d}
.mobile-toggle{display:none}.mobile-close{display:none}
@media(max-width:850px){.sidebar{width:190px}.content{padding:0}}
@media(max-width:650px){.layout{display:block}.sidebar{position:fixed;z-index:20;top:0;bottom:0;left:0;width:255px;transform:translateX(-100%);transition:transform .2s;box-shadow:10px 0 30px #0002}.sidebar.open{transform:translateX(0)}.mobile-close{display:block;margin-left:auto;background:none;border:0;color:#fff;font-size:25px;cursor:pointer}.mobile-toggle{display:inline-flex;align-items:center;justify-content:center;border:0;background:none;color:#fff;padding:6px;cursor:pointer}.mobile-toggle svg{width:24px;height:24px}.topbar{padding:0 14px;gap:10px}.topbar-title{margin-right:auto}.account{font-size:12px}.content{padding:0}.form-head,.form-body{padding:19px}.form-head h1{font-size:19px}}

.table-wrap{width:100%;overflow-x:auto}.tickets{border-collapse:collapse;width:100%;font-size:13px;min-width:1040px}.tickets th{text-align:left;background:#f4f8ff;color:#385475;font-size:12px;padding:14px 13px;border-bottom:1px solid #dfe8f5;white-space:nowrap}.tickets td{padding:15px 13px;border-bottom:1px solid #e8eef7;vertical-align:middle;color:#263e5d}.tickets tr:hover td{background:#f9fbff}.tickets td.descricao{max-width:240px;white-space:normal;line-height:1.45}.tickets .id{font-weight:700;color:#0c61c4}.status{display:inline-block;padding:6px 10px;border-radius:20px;background:#eaf2ff;color:#235ba0;font-size:12px;font-weight:700;white-space:nowrap}.status.aberto{background:#fff3d8;color:#8a5a0b}.status.andamento{background:#e6f1ff;color:#145fb5}.status.fechado{background:#e5f7ec;color:#187441}.acoes-tabela{white-space:nowrap}.acao{display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:8px 11px;border-radius:7px;font-size:12px;font-weight:700;text-decoration:none;border:0;cursor:pointer}.acao svg{width:14px;height:14px}.acao.editar{background:#e5f0ff;color:#075ec1}.acao.editar:hover{background:#d4e7ff}.acao.excluir{background:#fff0f0;color:#ba3434;margin-left:5px}.acao.excluir:hover{background:#ffe0e0}.vazio{padding:32px;color:var(--muted);text-align:center}.table-intro{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px}.table-intro strong{font-size:14px}.table-intro span{font-size:12px;color:var(--muted)}
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
        <a class="<?= $item['link'] === 'editar_chamado.php' ? 'active' : '' ?>" href="<?= htmlspecialchars($item['link'], ENT_QUOTES, 'UTF-8') ?>"><?= icone($item['icone']) ?> <?= htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8') ?></a>
      <?php else: ?>
        <span class="locked" title="Disponível para outro perfil"><?= icone($item['icone']) ?> <?= htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8') ?> <span class="lock">🔒</span></span>
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
    <section class="form-card">
      <div class="form-head">
        <span class="form-icon" aria-hidden="true"><?= icone('pencil') ?></span>
        <div><h1>Editar Chamados</h1><p>Gerencie, edite ou exclua os chamados registrados.</p></div>
      </div>
      <div class="form-body">
        <?php if ($mensagem): ?><div class="alerta sucesso" role="status"><?= seguro($mensagem) ?></div><?php endif; ?>
        <div class="table-intro"><strong>Lista de chamados</strong><span><?= $chamados ? $chamados->num_rows : 0 ?> chamado(s) encontrado(s)</span></div>
        <?php if ($chamados && $chamados->num_rows): ?>
          <div class="table-wrap"><table class="tickets"><thead><tr>
            <th>Chamado</th><th>Título</th><th>Categoria</th><th>Descrição do usuário</th><th>Descrição do técnico</th><th>Status</th><th>Valor</th><th>Usuário</th>
            <?php if ($temData): ?><th>Data de abertura</th><?php endif; ?>
            <th>Ações</th>
          </tr></thead><tbody>
          <?php while ($chamado = $chamados->fetch_object()):
            $status = mb_strtolower(trim((string)($chamado->statuschamado ?? '')), 'UTF-8');
            $classeStatus = (str_contains($status,'abert') || str_contains($status,'pend')) ? 'aberto' : ((str_contains($status,'andamento') || str_contains($status,'progres')) ? 'andamento' : ((str_contains($status,'finaliz') || str_contains($status,'fech') || str_contains($status,'conclu') || str_contains($status,'resolv')) ? 'fechado' : ''));
          ?>
            <tr>
              <td class="id">#<?= (int)$chamado->id_chamado ?></td>
              <td><?= seguro($chamado->titulo) ?></td>
              <td><?= seguro($chamado->categoria) ?></td>
              <td class="descricao"><?= nl2br(seguro($chamado->descricao)) ?></td>
              <td class="descricao"><?= nl2br(seguro($chamado->descricaotecnico)) ?></td>
              <td><span class="status <?= $classeStatus ?>"><?= seguro($chamado->statuschamado) ?></span></td>
              <td><?= seguro($chamado->valor) ?></td>
              <td><?= seguro($chamado->nome_usuario ?: 'Não identificado') ?></td>
              <?php if ($temData): ?><td><?= !empty($chamado->data_abertura) ? date('d/m/Y H:i', strtotime($chamado->data_abertura)) : 'Não registrada' ?></td><?php endif; ?>
              <td class="acoes-tabela">
                <a class="acao editar" href="edit_chamado.php?id=<?= (int)$chamado->id_chamado ?>"><?= icone('pencil') ?> Editar</a>
                <button class="acao excluir" type="button" onclick="confirmDeletion(<?= (int)$chamado->id_chamado ?>)">Excluir</button>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody></table></div>
        <?php else: ?><div class="vazio">Nenhum chamado encontrado.</div><?php endif; ?>
      </div>
    </section>
  </div>
</main>
</div>
<script>function toggleMenu(open){document.getElementById("sidebar").classList.toggle("open",open)} function confirmDeletion(id){if(confirm("Você realmente deseja excluir este chamado?")){window.location.href="deletar_chamado.php?id="+encodeURIComponent(id)}}</script>
</body>
</html>
