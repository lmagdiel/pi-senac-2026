<?php
require_once "validador_acesso.php";
require_once "config.php";

// Identifica o perfil do usuário conectado
$idUsuario = (int) $_SESSION['id'];

// Consulta preparada para maior segurança
$stmt = $conexao->prepare(
    "SELECT perfil FROM usuarios WHERE id_usuario = ?"
);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$res = $stmt->get_result();
$row = $res->fetch_object();
$stmt->close();

$perfil = $row ? $row->perfil : 'Usuario';

$tecnico = ($perfil === 'Tec' || $perfil === 'Adm');
$administrador = ($perfil === 'Adm');

$nome = isset($_SESSION['nome'])
    ? htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8')
    : 'Usuário';

// Funcionalidades disponíveis conforme o perfil
$itens = [
    [
        'titulo' => 'Abrir chamado',
        'icone' => 'file-plus',
        'link' => 'abrir_chamado.php',
        'permitido' => true
    ],
    [
        'titulo' => 'Consultar chamados',
        'icone' => 'search',
        'link' => 'consultar_chamado.php',
        'permitido' => true
    ],
    [
        'titulo' => 'Editar chamado',
        'icone' => 'pencil',
        'link' => 'editar_chamado.php',
        'permitido' => $tecnico
    ],
    [
        'titulo' => 'Autorizar',
        'icone' => 'shield',
        'link' => 'autorizar_usuarios.php',
        'permitido' => $administrador
    ],
    [
        'titulo' => 'Usuários',
        'icone' => 'users',
        'link' => 'usuarios.php',
        'permitido' => $administrador
    ],
    [
        'titulo' => 'Relatórios',
        'icone' => 'chart',
        'link' => 'relatorios.php',
        'permitido' => $administrador
    ]
];

// Ícones SVG
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

    return '<svg viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.9"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true">'
        . ($paths[$nome] ?? '') .
        '</svg>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Início | App Help Desk</title>

    <link rel="icon" href="imagens/logo.png" type="image/png">

    <style>
        :root {
            --navy: #0d315d;
            --navy-dark: #092747;
            --blue: #0867d5;
            --text: #122b4a;
            --muted: #687c94;
            --bg: #eff5ff;
            --border: #e2eaf6;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
            background: var(--bg);
        }

        /* ESTRUTURA PRINCIPAL */

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* MENU LATERAL */

        .sidebar {
            width: 224px;
            flex: none;
            background: linear-gradient(
                180deg,
                #103c70,
                var(--navy-dark)
            );
            color: #fff;
            padding: 22px 12px;
            display: flex;
            flex-direction: column;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 3px 10px 27px;
            font-size: 19px;
            font-weight: 700;
        }

        .brand img {
            width: 33px;
            height: 33px;
            object-fit: contain;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 43px;
            padding: 11px 12px;
            color: #e6f0ff;
            text-decoration: none;
            border-radius: 7px;
            font-size: 13px;
        }

        .nav a:hover,
        .nav a.active {
            background: #1769c8;
            color: #fff;
        }

        .nav svg {
            width: 18px;
            height: 18px;
            flex: none;
        }

        .logout {
            margin-top: auto;
            border-top: 1px solid #ffffff2c;
            padding-top: 12px;
        }

        /* ÁREA PRINCIPAL */

        .main {
            flex: 1;
            min-width: 0;
        }

        /* CABEÇALHO */

        .topbar {
            height: 66px;
            background: linear-gradient(
                90deg,
                #0c3d78,
                #0961c5
            );
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 31px;
            color: #fff;
            box-shadow: 0 2px 9px #123e7220;
        }

        .topbar-title {
            font-size: 16px;
            font-weight: 700;
        }

        .account {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 700;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: #2d83e5;
            font-size: 17px;
        }

        /* CONTEÚDO */

        .content {
            padding: 31px 36px;
            max-width: 1180px;
            margin: 0 auto;
        }

        /* BOAS-VINDAS */

        .welcome {
            background: linear-gradient(
                110deg,
                #fff 68%,
                #e5f1ff
            );
            border-radius: 15px;
            padding: 26px 28px;
            margin-bottom: 24px;
            box-shadow: 0 5px 18px #1c4a8010;
            position: relative;
            overflow: hidden;
        }

        .welcome::after {
            content: "";
            position: absolute;
            right: -35px;
            top: -75px;
            width: 190px;
            height: 190px;
            border: 26px solid #d3e8ff;
            border-radius: 50%;
            opacity: .6;
        }

        .welcome h1 {
            font-size: 25px;
            margin: 0 0 8px;
            position: relative;
            z-index: 1;
        }

        .welcome p {
            font-size: 14px;
            margin: 0;
            color: var(--muted);
            position: relative;
            z-index: 1;
        }

        /* CARTÕES */

        .section-title {
            font-size: 17px;
            margin: 0 0 17px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 17px;
        }

        .card {
            min-height: 155px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 15px;
            box-shadow: 0 5px 15px #123a7212;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 14px;
            text-align: center;
            padding: 18px 12px;
            text-decoration: none;
            color: #0e2d51;
            transition: transform .15s, box-shadow .15s;
        }

        .card svg {
            width: 39px;
            height: 39px;
            color: #0766d6;
        }

        .card strong {
            font-size: 14px;
            line-height: 1.35;
            max-width: 130px;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 22px #123a7224;
        }

        .note {
            color: var(--muted);
            font-size: 12px;
            margin-top: 18px;
        }

        /* RESPONSIVIDADE */

        .mobile-toggle,
        .mobile-close {
            display: none;
        }

        @media (max-width: 850px) {
            .sidebar {
                width: 190px;
            }

            .content {
                padding: 25px 22px;
            }

            .cards {
                gap: 12px;
            }

            .card {
                min-height: 145px;
            }
        }

        @media (max-width: 650px) {
            .layout {
                display: block;
            }

            .sidebar {
                position: fixed;
                z-index: 20;
                top: 0;
                bottom: 0;
                left: 0;
                width: 255px;
                transform: translateX(-100%);
                transition: transform .2s;
                box-shadow: 10px 0 30px #0002;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .mobile-close {
                display: block;
                margin-left: auto;
                background: none;
                border: 0;
                color: #fff;
                font-size: 25px;
                cursor: pointer;
            }

            .mobile-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 0;
                background: none;
                color: #fff;
                padding: 6px;
                cursor: pointer;
            }

            .mobile-toggle svg {
                width: 24px;
                height: 24px;
            }

            .topbar {
                padding: 0 14px;
                gap: 10px;
            }

            .topbar-title {
                margin-right: auto;
            }

            .account {
                font-size: 12px;
            }

            .content {
                padding: 20px 15px;
            }

            .welcome {
                padding: 23px 18px;
            }

            .welcome h1 {
                font-size: 21px;
            }

            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 11px;
            }

            .card {
                min-height: 140px;
                padding: 14px 8px;
            }

            .card svg {
                width: 32px;
                height: 32px;
            }

            .card strong {
                font-size: 12px;
            }
        }

        @media (max-width: 360px) {
            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- MENU LATERAL -->
    <aside class="sidebar" id="sidebar">

        <div class="brand">
            <img src="imagens/logo.png" alt="Logo Help Desk">
            Help Desk

            <button
                class="mobile-close"
                type="button"
                aria-label="Fechar menu"
                onclick="toggleMenu(false)">
                ×
            </button>
        </div>

        <nav class="nav" aria-label="Menu principal">

            <a class="active" href="home.php">
                <?= icone('home') ?>
                Início
            </a>

            <?php foreach ($itens as $item): ?>

                <?php if ($item['permitido']): ?>

                    <a href="<?= htmlspecialchars($item['link'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= icone($item['icone']) ?>
                        <?= htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8') ?>
                    </a>

                <?php endif; ?>

            <?php endforeach; ?>

        </nav>

        <nav class="nav logout">
            <a href="logoff.php">
                <?= icone('logout') ?>
                Sair
            </a>
        </nav>

    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main">

        <!-- CABEÇALHO -->
        <header class="topbar">

            <button
                class="mobile-toggle"
                type="button"
                aria-label="Abrir menu"
                onclick="toggleMenu(true)">
                <?= icone('menu') ?>
            </button>

            <span class="topbar-title">
                Help Desk
            </span>

            <div class="account">
                <span class="avatar" aria-hidden="true">●</span>
                <span><?= $nome ?></span>
            </div>

        </header>

        <!-- ÁREA DE CONTEÚDO -->
        <div class="content">

            <section class="welcome">
                <h1>Olá, <?= $nome ?>! 👋</h1>
                <p>Bem-vindo ao sistema de Help Desk.</p>
            </section>

            <h2 class="section-title">
                Acesso rápido
            </h2>

            <!-- CARTÕES PERMITIDOS -->
            <section class="cards" aria-label="Funcionalidades do sistema">

                <?php foreach ($itens as $item): ?>

                    <?php if ($item['permitido']): ?>

                        <a
                            class="card"
                            href="<?= htmlspecialchars($item['link'], ENT_QUOTES, 'UTF-8') ?>">

                            <?= icone($item['icone']) ?>

                            <strong>
                                <?= htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8') ?>
                            </strong>

                        </a>

                    <?php endif; ?>

                <?php endforeach; ?>

            </section>

            <p class="note">
                As opções disponíveis dependem do perfil da sua conta.
            </p>

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
