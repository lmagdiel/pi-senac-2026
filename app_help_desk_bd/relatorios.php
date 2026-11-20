<?php
require_once "validador_perfil.php";
require_once "config.php";

// Inicializando as variáveis 
$totalAbertos = 0; 
$totalAndamento = 0; 
$totalFinalizados = 0; 

// Consulta para contagem dos chamados por status
$sql = "SELECT statuschamado, COUNT(*) as qtd FROM chamados GROUP BY statuschamado";
$res = $conexao->query($sql);

if ($res) { 
  while ($row = $res->fetch_assoc()) { 
    if ($row['statuschamado'] === 'Aberto') {
      $totalAbertos = (int)$row['qtd'];
    } elseif ($row['statuschamado'] === 'Em andamento' || $row['statuschamado'] === 'Andamento') {
      $totalAndamento += (int)$row['qtd'];
    } elseif ($row['statuschamado'] === 'Finalizado') {
      $totalFinalizados = (int)$row['qtd'];
    }
  } 
}
?>

<html>

<head>
  <meta charset="utf-8" />
  <title>App Help Desk</title>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="icon" href="imagens/logo.png" type="image/x-icon">

  <style>
    .card-home {
      padding: 30px 0 0 0;
      width: 100%;
      margin: 0 auto;
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-dark bg-dark">
    <a class="navbar-brand" href="home.php">
      <img src="imagens/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
      App Help Desk
    </a>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="home.php">VOLTAR</a>
      </li>
    </ul>
  </nav>

  <div class="container">
    <div class="row">

      <div class="card-home">
        <div class="card">
          <div class="card-header">
            Relatórios de Chamados
          </div>

          <div class="card-body">
            <div class="row">
              <div class="col-4 d-flex justify-content-center">
                <a href="abertos.php" class="text-danger text-center">
                  <img src="imagens/abertos.png" width="70" height="70">
                  <p class="mt-2 font-weight-bold">Abertos (<?php echo $totalAbertos; ?>)</p>
                </a>
              </div>

              <div class="col-4 d-flex justify-content-center">
                <a href="andamento.php" style="color:darkorange" class="text-center">
                  <img src="imagens/andamento.png" width="70" height="70">
                  <p class="mt-2 font-weight-bold">Em andamento (<?php echo $totalAndamento; ?>)</p>
                </a>
              </div>

              <div class="col-4 d-flex justify-content-center">
                <a href="finalizado.php" style="color:teal" class="text-center">
                  <img src="imagens/finalizados.png" width="70" height="70">
                  <p class="mt-2 font-weight-bold">Finalizados (<?php echo $totalFinalizados; ?>)</p>
                </a>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
