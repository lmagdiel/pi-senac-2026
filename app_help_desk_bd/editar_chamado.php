<?php
require_once "validador_perfil_tecnico.php";
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
    @media (max-width: 1000px) {
      .hide-on-small {
        display: none;
      }
    }
    @media (max-width: 390px) {
      .hide-on-extra-small {
        display: none;
      }
    }
    @media (max-width: 300px) {
      .hide-on-very-small {
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
        <a class="nav-link" href="home.php">VOLTAR</a>
      </li>
    </ul>
  </nav>

    <?php //VALIDA SE O CHAMADO FOI DELETADO
        if(isset($_GET['delete']) && $_GET['delete'] === 'sucesso') { ?>
            <script> alert('Chamado excluído!');</script>
        <?php } ?>    
        
        <?php //VALIDA SE O CHAMADO FOI EDITADO
        if(isset($_GET['edicao']) && $_GET['edicao'] === 'sucesso') { ?>
            <script> alert('Chamado Editado!');</script>
        <?php } ?>

    <div class="container">
        <br>
        <?php
            $sql = "SELECT c.*, u.nome AS nome_usuario FROM chamados c LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario ORDER BY c.id_chamado DESC";
            $res = $conexao->query($sql);
            $qtd = $res ? $res->num_rows : 0;

            if($qtd > 0){
                print "<div class='table-responsive'>";
                print "<table class='table table-hover table-bordered table-sm'>";
                print "<thead class='thead-light'>";
                print "<tr>";
                print "<th scope='col'>Chamado</th>";
                print "<th scope='col' class='hide-on-very-small'>Título</th>";
                print "<th scope='col' class='hide-on-small'>Categoria</th>";
                print "<th scope='col' class='hide-on-small'>Descrição do Usuário</th>";
                print "<th scope='col' class='hide-on-small'>Descrição do Técnico</th>";
                print "<th scope='col' class='hide-on-small'>Status</th>";
                print "<th scope='col' class='hide-on-small'>Valor</th>";
                print "<th scope='col' class='hide-on-small'>Usuário</th>";
                print "<th scope='col'>Editar</th>";
                print "<th scope='col' class='hide-on-extra-small'>Excluir</th>";
                print "</tr>";
                print "</thead>";
                print "<tbody>";

                while($row = $res->fetch_object()){
                    $nomeUsuario = htmlspecialchars($row->nome_usuario ?? 'N/D');
                    $titulo = htmlspecialchars($row->titulo);
                    $categoria = htmlspecialchars($row->categoria);
                    $descricao = htmlspecialchars($row->descricao);
                    $descTecnico = htmlspecialchars($row->descricaotecnico ?? '');
                    $status = htmlspecialchars($row->statuschamado);
                    $valor = number_format((float)$row->valor, 2, ',', '.');

                    print "<tr>";
                    print "<td>" . (int)$row->id_chamado . "</td>";
                    print "<td class='hide-on-very-small'>" . $titulo . "</td>";
                    print "<td class='hide-on-small'>" . $categoria . "</td>";
                    print "<td class='hide-on-small'>" . $descricao . "</td>";
                    print "<td class='hide-on-small'>" . $descTecnico . "</td>";
                    print "<td class='hide-on-small'>" . $status . "</td>";                    
                    print "<td class='hide-on-small'>R$ " . $valor . "</td>";                    
                    print "<td class='hide-on-small'>" . $nomeUsuario . "</td>";
                    print "<td><button class='btn btn-success btn-sm' onclick=\"location.href='edit_chamado.php?id=" . (int)$row->id_chamado . "';\">Editar</button></td>";
                    print "<td class='hide-on-extra-small'><button class='btn btn-danger btn-sm' onclick=\"confirmDeletion(" . (int)$row->id_chamado . ");\">Excluir</button></td>";
                    print "</tr>";        
                }
                print "</tbody>";
                print "</table>";
                print "</div>";
            } else {
                print "<div class='alert alert-info text-center'>Nenhum chamado encontrado.</div>";
            }
        ?>
    </div>

    <script>
      function confirmDeletion(id) {
          if (confirm("Você realmente deseja excluir este chamado?")) {
              window.location.href = 'deletar_chamado.php?id=' + id;
          }
      }
    </script>

</body>

</html>
