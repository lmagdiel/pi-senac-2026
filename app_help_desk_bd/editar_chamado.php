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
<link rel="stylesheet" href="css/editar_chamado.css">
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
