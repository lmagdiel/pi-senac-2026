
<?php
require_once "validador_acesso.php";
require_once "config.php";

// Identifica o perfil do usuário conectado
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

$nome = htmlspecialchars(
    $_SESSION['nome'] ?? 'Usuário',
    ENT_QUOTES,
    'UTF-8'
);

// Opções do menu conforme o perfil
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

// Ícones do sistema
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

// Resultado do cadastro do chamado
$cadastro = $_GET['cadastro'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Abrir Chamado | App Help Desk</title>

    <link rel="icon" href="imagens/logo.png" type="image/png">

    <link rel="stylesheet" href="css/abrir_chamado.css">
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

            <a href="home.php">
                <?= icone('home') ?>
                Início
            </a>

            <?php foreach ($itens as $item): ?>

                <?php if ($item['permitido']): ?>

                    <a
                        class="<?= $item['link'] === 'abrir_chamado.php' ? 'active' : '' ?>"
                        href="<?= htmlspecialchars($item['link'], ENT_QUOTES, 'UTF-8') ?>">

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

    <!-- ÁREA PRINCIPAL -->
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

        <!-- CONTEÚDO -->
        <div class="content">

            <section class="form-card">

                <div class="form-head">

                    <span class="form-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.9"
                             stroke-linecap="round"
                             stroke-linejoin="round">

                            <path d="M21 11.5a8.4 8.4 0 0 1-9 8.5 9 9 0 0 1-4-.9L3 21l1.9-5A9 9 0 1 1 21 11.5Z"/>
                            <path d="M8 12h.01M12 12h.01M16 12h.01"/>

                        </svg>
                    </span>

                    <div>
                        <h1>Abrir Chamado</h1>
                        <p>Registre uma nova solicitação de suporte.</p>
                    </div>

                </div>

                <div class="form-body">

                    <!-- MENSAGENS DE RESULTADO -->
                    <?php if ($cadastro === 'efetuado'): ?>

                        <div class="alerta sucesso" role="status">
                            Chamado cadastrado com sucesso!
                        </div>

                    <?php elseif ($cadastro === 'falha'): ?>

                        <div class="alerta erro" role="alert">
                            Erro ao cadastrar o chamado. Contate o administrador.
                        </div>

                    <?php endif; ?>

                    <!-- FORMULÁRIO -->
                    <form action="registra_chamado.php" method="POST">

                        <div class="campo">
                            <label for="titulo">
                                Título do chamado *
                            </label>

                            <input
                                id="titulo"
                                name="titulo"
                                type="text"
                                placeholder="Ex.: Problema no computador"
                                required
                                autofocus>
                        </div>

                        <div class="campo">
                            <label for="categoria">
                                Categoria *
                            </label>

                            <select
                                id="categoria"
                                name="categoria"
                                required>

                                <option value="" disabled selected>
                                    Selecione uma categoria
                                </option>

                                <option value="Impressora">Impressora</option>
                                <option value="Hardware">Hardware</option>
                                <option value="Software">Software</option>
                                <option value="Rede">Rede</option>
                                <option value="Formatação">Formatação</option>
                                <option value="Orçamento">Orçamento</option>
                                <option value="Outros">Outros</option>

                            </select>
                        </div>

                        <div class="campo">
                            <label for="descricao">
                                Descrição *
                            </label>

                            <textarea
                                id="descricao"
                                name="descricao"
                                rows="3"
                                placeholder="Descreva o problema ou solicitação..."
                                required></textarea>
                        </div>

                        <div class="acoes">

                            <button
                                type="submit"
                                class="botao primario">
                                ▣ &nbsp; Cadastrar Chamado
                            </button>

                            <a
                                href="home.php"
                                class="botao secundario">
                                Cancelar
                            </a>

                        </div>

                    </form>

                </div>

            </section>

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
