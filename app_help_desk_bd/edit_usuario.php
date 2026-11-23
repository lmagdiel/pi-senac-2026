
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

<style>
:root{--navy:#0d315d;--navy-dark:#092747;--blue:#0867d5;--text:#122b4a;--muted:#687c94;--bg:#eff5ff;--border:#e2eaf6}
*{box-sizing:border-box}
body{margin:0;font-family:Arial,Helvetica,sans-serif;color:var(--text);background:var(--bg)}
.layout{display:flex;min-height:100vh}
.sidebar{width:224px;flex:none;background:linear-gradient(180deg,#103c70,var(--navy-dark));color:#fff;padding:22px 12px;display:flex;flex-direction:column}
.brand{display:flex;align-items:center;gap:10px;padding:3px 10px 27px;font-size:19px;font-weight:700}
.brand img{width:33px;height:33px;object-fit:contain}
.nav{display:flex;flex-direction:column;gap:5px}
.nav a,.nav .locked{display:flex;align-items:center;gap:12px;min-height:43px;padding:11px 12px;color:#e6f0ff;text-decoration:none;border-radius:7px;font-size:13px}
.nav a:hover,.nav a.active{background:#1769c8;color:#fff}
.nav .locked{opacity:.42;cursor:not-allowed}
.nav svg{width:18px;height:18px;flex:none}
.nav .lock{font-size:11px;margin-left:auto}
.logout{margin-top:auto;border-top:1px solid #ffffff2c;padding-top:12px}
.main{flex:1;min-width:0}
.topbar{height:66px;background:linear-gradient(90deg,#0c3d78,#0961c5);display:flex;align-items:center;justify-content:space-between;padding:0 31px;color:#fff;box-shadow:0 2px 9px #123e7220}
.topbar-title{font-size:16px;font-weight:700}
.account{display:flex;align-items:center;gap:10px;font-size:13px;font-weight:700}
.avatar{width:32px;height:32px;border-radius:50%;display:grid;place-items:center;background:#2d83e5;font-size:17px}

.content{padding:0;max-width:none;margin:0;min-height:calc(100vh - 66px);background:#fff}
.form-head{display:flex;align-items:center;gap:12px;padding:22px 28px;background:#f7faff;border-bottom:1px solid var(--border)}
.form-icon{width:38px;height:38px;border-radius:10px;display:grid;place-items:center;background:#e8f2ff;color:var(--blue)}
.form-icon svg{width:21px;height:21px}
.form-head h1{font-size:19px;margin:0 0 5px}
.form-head p{font-size:13px;color:var(--muted);margin:0}
.form-body{padding:26px 28px 30px}

.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}
.form-group{display:flex;flex-direction:column;gap:8px}
.form-group label{font-size:13px;font-weight:700;color:#294363}
.form-control{width:100%;height:44px;padding:10px 13px;border:1px solid #d7e2f0;border-radius:7px;font-family:inherit;font-size:14px;color:var(--text);background:#fff;outline:none}
.form-control:focus{border-color:#3486e6;box-shadow:0 0 0 3px #3486e622}
.form-control:disabled{background:#f2f6fc;color:#71849d;cursor:not-allowed}
.form-group.full{grid-column:1/-1}
.form-actions{display:flex;justify-content:flex-end;gap:12px;margin-top:30px;padding-top:22px;border-top:1px solid var(--border)}
.btn{display:inline-flex;align-items:center;justify-content:center;min-width:130px;height:42px;padding:0 20px;border-radius:7px;font-family:inherit;font-size:13px;font-weight:700;text-decoration:none;cursor:pointer}
.btn-cancelar{background:#f0f4fa;color:#425a76;border:1px solid #dce5f0}
.btn-cancelar:hover{background:#e4ebf5}
.btn-salvar{background:#0867d5;color:#fff;border:1px solid #0867d5}
.btn-salvar:hover{background:#0758b7}
.mobile-toggle,.mobile-close{display:none}

@media(max-width:850px){
.sidebar{width:190px}
}
@media(max-width:650px){
.layout{display:block}
.sidebar{position:fixed;z-index:20;top:0;bottom:0;left:0;width:255px;transform:translateX(-100%);transition:transform .2s;box-shadow:10px 0 30px #0002}
.sidebar.open{transform:translateX(0)}
.mobile-close{display:block;margin-left:auto;background:none;border:0;color:#fff;font-size:25px;cursor:pointer}
.mobile-toggle{display:inline-flex;align-items:center;justify-content:center;border:0;background:none;color:#fff;padding:6px;cursor:pointer}
.mobile-toggle svg{width:24px;height:24px}
.topbar{padding:0 14px;gap:10px}
.topbar-title{margin-right:auto}
.account{font-size:12px}
.form-head,.form-body{padding:19px}
.form-grid{grid-template-columns:1fr}
.form-actions{flex-direction:column-reverse}
.btn{width:100%}
}
</style>
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
