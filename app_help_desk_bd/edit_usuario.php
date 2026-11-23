
<?php
require_once "validador_acesso.php";
require_once "config.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    exit("Usuário inválido.");
}

$stmt = $conexao->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_object();

if (!$row) {
    http_response_code(404);
    exit("Usuário não encontrado.");
}

$idUsuario = (int)($_SESSION['id'] ?? 0);
$consultaPerfil = $conexao->query("SELECT perfil FROM usuarios WHERE id_usuario = $idUsuario");
$usuarioAtual = $consultaPerfil ? $consultaPerfil->fetch_object() : null;
$perfilAtual = $usuarioAtual->perfil ?? '';

$tecnico = in_array($perfilAtual, ['Tec', 'Adm'], true);
$administrador = $perfilAtual === 'Adm';

if (!$administrador) {
    http_response_code(403);
    exit("Acesso restrito a administradores.");
}

$nomeLogado = htmlspecialchars($_SESSION['nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8');

function seguro($valor) {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function icone($nome) {
    $paths = [
        'home'=>'<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/>',
        'file-plus'=>'<path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M13 2v7h7M12 13v6m-3-3h6"/>',
        'search'=>'<circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/>',
        'pencil'=>'<path d="m4 20 4.5-1 11-11a2.1 2.1 0 0 0-3-3l-11 11L4 20zM14.5 7.5l3 3"/>',
        'shield'=>'<path d="M12 2 4 5v6c0 5 3.4 8.5 8 11 4.6-2.5 8-6 8-11V5z"/><path d="m8 12 3 3 5-6"/>',
        'users'=>'<circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M16 5a3 3 0 0 0 0 6m2 3a5 5 0 0 1 3 5v1"/>',
        'chart'=>'<path d="M4 21V11h4v10M10 21V5h4v16M16 21v-8h4v8M2 21h20"/>',
        'logout'=>'<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9"/>',
        'menu'=>'<path d="M4 6h16M4 12h16M4 18h16"/>'
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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Editar Usuário | App Help Desk</title>
<link rel="icon" href="imagens/logo.png" type="image/x-icon">

<link rel="stylesheet" href="css/edit_usuario.css">
</head>

<body>
<div class="layout">

<aside class="sidebar" id="sidebar">
    <div class="brand">
        <img src="imagens/logo.png" alt="Logo Help Desk">
        Help Desk
        <button class="mobile-close" type="button" aria-label="Fechar menu" onclick="toggleMenu(false)">×</button>
    </div>

    <nav class="nav" aria-label="Menu principal">
        <a href="home.php"><?= icone('home') ?> Início</a>

        <?php foreach($itens as $item): ?>
            <?php if($item['permitido']): ?>
                <a class="<?= $item['link']==='usuarios.php'?'active':'' ?>"
                   href="<?= seguro($item['link']) ?>">
                    <?= icone($item['icone']) ?>
                    <?= seguro($item['titulo']) ?>
                </a>
            <?php else: ?>
                <span class="locked" title="Disponível para outro perfil">
                    <?= icone($item['icone']) ?>
                    <?= seguro($item['titulo']) ?>
                    <span class="lock">🔒</span>
                </span>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <nav class="nav logout">
        <a href="logoff.php"><?= icone('logout') ?> Sair</a>
    </nav>
</aside>

<main class="main">

<header class="topbar">
    <button class="mobile-toggle" type="button" aria-label="Abrir menu" onclick="toggleMenu(true)">
        <?= icone('menu') ?>
    </button>

    <span class="topbar-title">Help Desk</span>

    <div class="account">
        <span class="avatar" aria-hidden="true">●</span>
        <span><?= $nomeLogado ?></span>
    </div>
</header>

<div class="content">

    <div class="form-head">
        <span class="form-icon" aria-hidden="true">
            <?= icone('users') ?>
        </span>

        <div>
            <h1>Editar usuário</h1>
            <p>Atualize as informações e o perfil de acesso do usuário.</p>
        </div>
    </div>

    <div class="form-body">

        <form action="conf_edit_usuario.php" method="POST">

            <input type="hidden" name="id_usuario" value="<?= (int)$row->id_usuario ?>">

            <div class="form-grid">

                <div class="form-group">
                    <label for="id">ID do usuário</label>
                    <input id="id" type="text" class="form-control"
                           value="<?= (int)$row->id_usuario ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input id="nome" name="nome" type="text" class="form-control"
                           value="<?= seguro($row->nome) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input id="email" name="email" type="email" class="form-control"
                           value="<?= seguro($row->email) ?>" required>
                </div>

                <div class="form-group">
                    <label for="perfil">Perfil</label>
                    <select id="perfil" name="perfil" class="form-control" required>
                        <option value="Usuário" <?= $row->perfil==='Usuário'?'selected':'' ?>>Usuário</option>
                        <option value="Adm" <?= $row->perfil==='Adm'?'selected':'' ?>>Administrador</option>
                        <option value="Tec" <?= $row->perfil==='Tec'?'selected':'' ?>>Técnico</option>
                    </select>
                </div>

            </div>

            <div class="form-actions">
                <a class="btn btn-cancelar" href="usuarios.php">Cancelar</a>
                <button class="btn btn-salvar" type="submit">Salvar alterações</button>
            </div>

        </form>

    </div>
</div>

</main>
</div>

<script>
function toggleMenu(open) {
    document.getElementById('sidebar').classList.toggle('open', open);
}
</script>

</body>
</html>
