<?php

require_once "validador_acesso.php";

require_once "config.php";



// Informações do usuário logado

$idUsuario = (int)($_SESSION['id'] ?? 0);

$stmtPerfil = $conexao->prepare(
    "SELECT perfil FROM usuarios WHERE id_usuario = ?"
);
$stmtPerfil->bind_param("i", $idUsuario);
$stmtPerfil->execute();

$consultaPerfil = $stmtPerfil->get_result();
$usuarioAtual = $consultaPerfil ? $consultaPerfil->fetch_object() : null;
$stmtPerfil->close();

$perfil = $usuarioAtual->perfil ?? '';



$tecnico = in_array($perfil, ['Tec', 'Adm'], true);

$administrador = $perfil === 'Adm';



if (!$administrador) {

    http_response_code(403);

    exit("Acesso restrito a administradores.");

}



$nome = htmlspecialchars($_SESSION['nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8');



// Filtro mensal. Sem data de abertura não é possível reconstruir meses anteriores.

$mesSelecionado = $_GET['mes'] ?? date('Y-m');

if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mesSelecionado)) {

    $mesSelecionado = date('Y-m');

}

$inicio = $mesSelecionado . '-01';

$fim = date('Y-m-d', strtotime($inicio . ' +1 month'));

$mesAnterior = date('Y-m', strtotime($inicio . ' -1 month'));

$mesSeguinte = date('Y-m', strtotime($inicio . ' +1 month'));

$mesAtual = date('Y-m');



$totalAbertos = 0;

$totalAndamento = 0;

$totalFinalizados = 0;

$totalArrecadado = 0.0;

$receitaSemanas = array_fill(0, 5, 0.0);

$temDataAbertura = false;

$temValor = false;

$erroConsulta = '';



$colunas = $conexao->query('SHOW COLUMNS FROM chamados');

if ($colunas) {

    while ($coluna = $colunas->fetch_assoc()) {

        if ($coluna['Field'] === 'data_abertura') $temDataAbertura = true;

        if ($coluna['Field'] === 'valor') $temValor = true;

    }

}



if ($temDataAbertura) {

    $sqlMensal = 'SELECT statuschamado, data_abertura' . ($temValor ? ', valor' : '') .

        ' FROM chamados WHERE data_abertura >= ? AND data_abertura < ?';

    $stmt = $conexao->prepare($sqlMensal);

    if ($stmt) {

        $stmt->bind_param('ss', $inicio, $fim);

        if ($stmt->execute()) {

            $resultado = $stmt->get_result();

            while ($chamado = $resultado->fetch_assoc()) {

                $status = trim((string)($chamado['statuschamado'] ?? ''));

                if ($status === 'Aberto') $totalAbertos++;

                elseif ($status === 'Em andamento') $totalAndamento++;

                elseif ($status === 'Finalizado') {

                    $totalFinalizados++;

                    // Considera valores de chamados finalizados; não comprova pagamento recebido.

                    $valor = $temValor ? (float)($chamado['valor'] ?? 0) : 0;

                    $totalArrecadado += $valor;

                    $dia = (int)date('j', strtotime($chamado['data_abertura']));

                    $semana = min(4, intdiv($dia - 1, 7));

                    $receitaSemanas[$semana] += $valor;

                }

            }

        } else $erroConsulta = 'Não foi possível carregar os dados do período.';

        $stmt->close();

    } else $erroConsulta = 'Não foi possível preparar a consulta mensal.';

}

$totalChamados = $totalAbertos + $totalAndamento + $totalFinalizados;

$maxChamados = max(1, $totalAbertos, $totalAndamento, $totalFinalizados);

$maxReceita = max(1, ...$receitaSemanas);

$rotulosSemanas = ['01–07', '08–14', '15–21', '22–28', '29–31'];

$nomeMes = ['01'=>'Janeiro','02'=>'Fevereiro','03'=>'Março','04'=>'Abril',

    '05'=>'Maio','06'=>'Junho','07'=>'Julho','08'=>'Agosto','09'=>'Setembro',

    '10'=>'Outubro','11'=>'Novembro','12'=>'Dezembro'];

$periodoTitulo = $nomeMes[substr($mesSelecionado, 5, 2)] . ' de ' . substr($mesSelecionado, 0, 4);





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



<title>Relatórios | App Help Desk</title>

<link rel="icon" href="imagens/logo.png" type="image/x-icon">



<link rel="stylesheet" href="css/relatorios.css">

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

                <a class="<?= $item['link']==='relatorios.php'?'active':'' ?>"

                   href="<?= seguro($item['link']) ?>">

                    <?= icone($item['icone']) ?>

                    <?= seguro($item['titulo']) ?>

                </a>

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

        <span><?= $nome ?></span>

    </div>

</header>



<div class="content">



    <div class="form-head">

        <span class="form-icon" aria-hidden="true">

            <?= icone('chart') ?>

        </span>



        <div>

            <h1>Relatórios de chamados</h1>

            <p>Acompanhe a quantidade de chamados por situação.</p>

        </div>

    </div>



    <div class="form-body">



        <div class="periodo-bar">

            <div>

                <h2 class="section-title">Visão geral dos chamados</h2>

                <p class="section-description">Chamados abertos em <?= seguro($periodoTitulo) ?>. Selecione uma categoria para ver detalhes.</p>

            </div>

            <form class="periodo-controles" method="get" action="relatorios.php">

                <label class="periodo-label" for="mes">Mês do relatório</label>

                <input type="month" id="mes" name="mes" value="<?= seguro($mesSelecionado) ?>" required>

                <button type="submit">Filtrar</button>

                <a href="relatorios.php">Mês atual</a>

            </form>

        </div>

        <?php if (!$temDataAbertura): ?>

            <div class="aviso-dados">O banco ainda não possui a coluna <strong>data_abertura</strong> na tabela chamados. Não é possível separar os chamados por mês sem essa informação. Os indicadores abaixo ficarão zerados até a data ser registrada. Não alteramos o banco automaticamente.</div>

        <?php elseif ($erroConsulta): ?>

            <div class="aviso-dados"><?= seguro($erroConsulta) ?></div>

        <?php endif; ?>

        <?php if (!$temValor): ?>

            <div class="aviso-dados">A coluna <strong>valor</strong> não foi encontrada. A arrecadação não poderá ser calculada até que os valores estejam disponíveis.</div>

        <?php endif; ?>



        <div class="stats">



            <a class="stat-card abertos" href="abertos.php">

                <div class="stat-top">

                    <span class="stat-icon">

                        <svg viewBox="0 0 24 24">

                            <circle cx="12" cy="12" r="9"/>

                            <path d="M12 7v6M12 17h.01"/>

                        </svg>

                    </span>

                </div>



                <div>

                    <div class="stat-number"><?= $totalAbertos ?></div>

                    <div class="stat-label">Chamados abertos</div>

                </div>



                <div class="stat-footer">

                    <span>Visualizar chamados</span>

                    <strong>Ver detalhes →</strong>

                </div>

            </a>



            <a class="stat-card andamento" href="andamento.php">

                <div class="stat-top">

                    <span class="stat-icon">

                        <svg viewBox="0 0 24 24">

                            <circle cx="12" cy="12" r="9"/>

                            <path d="M12 7v5l4 2"/>

                        </svg>

                    </span>

                </div>



                <div>

                    <div class="stat-number"><?= $totalAndamento ?></div>

                    <div class="stat-label">Em andamento</div>

                </div>



                <div class="stat-footer">

                    <span>Visualizar chamados</span>

                    <strong>Ver detalhes →</strong>

                </div>

            </a>



            <a class="stat-card finalizados" href="finalizado.php">

                <div class="stat-top">

                    <span class="stat-icon">

                        <svg viewBox="0 0 24 24">

                            <circle cx="12" cy="12" r="9"/>

                            <path d="m8 12 3 3 5-6"/>

                        </svg>

                    </span>

                </div>



                <div>

                    <div class="stat-number"><?= $totalFinalizados ?></div>

                    <div class="stat-label">Chamados finalizados</div>

                </div>



                <div class="stat-footer">

                    <span>Visualizar chamados</span>

                    <strong>Ver detalhes →</strong>

                </div>

            </a>



        </div>



        <div class="dashboard-graficos">

            <section class="grafico-card" aria-label="Gráfico de situação dos chamados">

                <h3>Situação dos chamados</h3>

                <p class="grafico-subtitulo">Distribuição dos chamados abertos em <?= seguro($periodoTitulo) ?>, pelo status atual.</p>

                <?php foreach ([

                    ['Abertos', $totalAbertos, 'barra-abertos'],

                    ['Em andamento', $totalAndamento, 'barra-andamento'],

                    ['Finalizados', $totalFinalizados, 'barra-finalizados']

                ] as $item): ?>

                    <div class="barra-item">

                        <div class="barra-info"><span><?= seguro($item[0]) ?></span><strong><?= (int)$item[1] ?></strong></div>

                        <div class="barra-fundo" role="img" aria-label="<?= seguro($item[0]) ?>: <?= (int)$item[1] ?> chamados">

                            <div class="barra-preenchimento <?= seguro($item[2]) ?>" style="--barra-width:<?= round($item[1] / $maxChamados * 100, 2) ?>%"></div>

                        </div>

                    </div>

                <?php endforeach; ?>

                <p class="nota-grafico"><strong><?= $totalChamados ?></strong> chamado(s) no período selecionado.</p>

            </section>

            <section class="grafico-card" aria-label="Gráfico de valores dos chamados finalizados">

                <h3>Valores dos atendimentos</h3>

                <p class="grafico-subtitulo">Valores registrados nos chamados finalizados, agrupados pela semana de abertura.</p>

                <div class="receita-destaque">R$ <?= number_format($totalArrecadado, 2, ',', '.') ?>

                    <small>Total dos valores de chamados finalizados no mês</small>

                </div>

                <?php foreach ($receitaSemanas as $indice => $valorSemana): ?>

                    <div class="barra-item barra-item-receita">

                        <div class="barra-info"><span>Dias <?= seguro($rotulosSemanas[$indice]) ?></span><strong>R$ <?= number_format($valorSemana, 2, ',', '.') ?></strong></div>

                        <div class="barra-fundo" role="img" aria-label="Dias <?= seguro($rotulosSemanas[$indice]) ?>: R$ <?= number_format($valorSemana, 2, ',', '.') ?>">

                            <div class="barra-preenchimento barra-receita" style="--barra-width:<?= round($valorSemana / $maxReceita * 100, 2) ?>%"></div>

                        </div>

                    </div>

                <?php endforeach; ?>

                <p class="nota-grafico">Os valores são associados ao mês de <strong>abertura</strong> dos chamados. O campo valor não confirma que o pagamento foi recebido.</p>

            </section>

        </div>



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
