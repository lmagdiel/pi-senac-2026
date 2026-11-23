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

if (!$administrador) {
    http_response_code(403);
    exit('Acesso restrito a administradores.');
}

function seguro($valor) {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}
function icone($nome) {
    $paths = [
        'home' => '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/>',
        'file-plus'=>'<path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M13 2v7h7M12 13v6m-3-3h6"/>',
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
$mensagem = '';
if (($_GET['delete'] ?? '') === 'sucesso') $mensagem = 'Usuário excluído com sucesso!';
if (($_GET['edicao'] ?? '') === 'sucesso') $mensagem = 'Usuário editado com sucesso!';
$usuarios = $conexao->query('SELECT id_usuario, nome, email, perfil FROM usuarios ORDER BY id_usuario DESC');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Usuários | App Help Desk</title>
<link rel="icon" href="imagens/logo.png" type="image/x-icon">
<link rel="stylesheet" href="css/usuarios.css">
</head>
<body>
<div class="layout">
<aside class="sidebar" id="sidebar">
  <div class="brand"><img src="imagens/logo.png" alt="Logo Help Desk">Help Desk <button class="mobile-close" type="button" aria-label="Fechar menu" onclick="toggleMenu(false)">×</button></div>
  <nav class="nav" aria-label="Menu principal">
    <a href="home.php"><?= icone('home') ?> Início</a>
    <?php foreach ($itens as $item): ?>
      <?php if ($item['permitido']): ?>
        <a class="<?= $item['link'] === 'usuarios.php' ? 'active' : '' ?>" href="<?= seguro($item['link']) ?>"><?= icone($item['icone']) ?> <?= seguro($item['titulo']) ?></a>
      <?php else: ?>
        <span class="locked" title="Disponível para outro perfil"><?= icone($item['icone']) ?> <?= seguro($item['titulo']) ?> <span class="lock">🔒</span></span>
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
      <span class="form-icon" aria-hidden="true"><?= icone('users') ?></span>
      <div><h1>Usuários</h1><p>Consulte e gerencie os usuários cadastrados no sistema.</p></div>
    </div>
    <div class="form-body">
      <?php if ($mensagem): ?><div class="alerta" role="status"><?= seguro($mensagem) ?></div><?php endif; ?>
      <div class="table-intro"><strong>Lista de usuários</strong><span><?= $usuarios ? $usuarios->num_rows : 0 ?> usuário(s) encontrado(s)</span></div>
      <?php if ($usuarios && $usuarios->num_rows): ?>
        <div class="table-wrap"><table class="tickets"><thead><tr><th>ID</th><th>Usuário</th><th>E-mail</th><th>Perfil</th><th>Ações</th></tr></thead><tbody>
        <?php while ($usuario = $usuarios->fetch_object()):
          $perfilUsuario = trim((string)$usuario->perfil);
          $classePerfil = match ($perfilUsuario) {
            'Adm' => 'adm', 'Tec' => 'tec', 'Usu', 'Usuario' => 'usu', 'Administrador', 'Tecnico' => 'pendente', default => ''
          };
        ?>
          <tr>
            <td class="id">#<?= (int)$usuario->id_usuario ?></td>
            <td><?= seguro($usuario->nome) ?></td>
            <td><?= seguro($usuario->email) ?></td>
            <td><span class="perfil <?= $classePerfil ?>"><?= seguro($usuario->perfil) ?></span></td>
            <td class="acoes-tabela">
              <a class="acao editar" href="edit_usuario.php?id=<?= (int)$usuario->id_usuario ?>"><?= icone('pencil') ?> Editar</a>
              <button class="acao excluir" type="button" onclick="confirmDeletion(<?= (int)$usuario->id_usuario ?>)">Excluir</button>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody></table></div>
      <?php else: ?><div class="vazio">Nenhum usuário encontrado.</div><?php endif; ?>
    </div>
  </div>
</main>
</div>
<script>
function toggleMenu(open){document.getElementById('sidebar').classList.toggle('open',open)}
function confirmDeletion(id){if(confirm('Você realmente deseja excluir este usuário?')){window.location.href='deletar_usuario.php?id='+encodeURIComponent(id)}}
</script>
</body>
</html>
