<?php
require_once "validador_perfil.php";
require_once "config.php";
?>

<html>
<head>
  <meta charset="utf-8" />
  <title>App Help Desk</title>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="icon" href="imagens/logo.png" type="image/x-icon">
  <style>
    .table-responsive {
      overflow-x: auto;
    }
    .btn-sm {
      padding: 5px;
      min-width: auto;
    }
    .thead-light th {
      background-color: #ffffff;
      font-weight: bold;
      text-align: center;
    }
    td, th {
      text-align: center;
    }
    .status-finalizado {
      background-color: #28a745;
      color: #ffffff;
      font-weight: bold;
      width: 200px;
    }
    .total-chamados {
      text-align: left;
    }
    @media (max-width: 1000px) {
      .hide-on-small {
        display: none;
      }
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
        <a class="nav-link" href="relatorios.php">VOLTAR</a>
      </li>
    </ul>
  </nav>

  <div class="container">
    <br>
    <?php
        $sqlTotal = "SELECT COUNT(*) as Total FROM chamados WHERE statuschamado = 'Finalizado'";
        $resTotal = $conexao->query($sqlTotal);
        $rowTotal = $resTotal ? $resTotal->fetch_assoc() : ['Total' => 0]; 
        $total = $rowTotal['Total'];

        $sql = "SELECT c.*, u.nome AS nome_usuario FROM chamados c LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario WHERE c.statuschamado = 'Finalizado' ORDER BY c.id_chamado DESC";
        $res = $conexao->query($sql);
        $qtd = $res ? $res->num_rows : 0;

        if($qtd > 0){
            print "<div class='table-responsive'>";
            print "<table class='table table-hover table-bordered table-sm'>";
            print "<thead class='thead-light'>";
            print "<tr>";
            print "<th class='status-finalizado'> Status Finalizado </th>";
            print "<th colspan=4 class='total-chamados' style='text-align: left;'> $total Chamados</th>";
            print "</tr>";
            print "<tr>";
            print "<th scope='col'>Chamado</th>";
            print "<th scope='col'>Título</th>";
            print "<th scope='col' class='hide-on-small'>Categoria</th>";
            print "<th scope='col' class='hide-on-small'>Descrição</th>";
            print "<th scope='col'>Usuário</th>";
            print "</tr>";
            print "</thead>";
            print "<tbody>";

            while($row = $res->fetch_object()){
                print "<tr>";
                print "<td>" . (int)$row->id_chamado . "</td>";
                print "<td>" . htmlspecialchars($row->titulo) . "</td>";
                print "<td class='hide-on-small'>" . htmlspecialchars($row->categoria) . "</td>";
                print "<td class='hide-on-small'>" . htmlspecialchars($row->descricao) . "</td>";
                print "<td>" . htmlspecialchars($row->nome_usuario ?? 'N/D') . "</td>";
                print "</tr>";
            }
            print "</tbody>";
            print "</table>";
            print "</div>";
        } else {
            print "<div class='alert alert-info text-center'>Nenhum chamado finalizado no momento.</div>";
        }
    ?>
  </div>
</body>
</html>
