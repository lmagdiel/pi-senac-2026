<?php
require_once "validador_perfil_tecnico.php";
require_once "config.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$row = null;

if ($id > 0) {
    $stmt = $conexao->prepare("SELECT * FROM chamados WHERE id_chamado = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_object();
        $stmt->close();
    }
}

if (!$row) {
    header('Location: editar_chamado.php');
    exit;
}
?>

<html>

<head>
  <meta charset="utf-8" />
  <title>App Help Desk</title>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="icon" href="imagens/logo.png" type="image/x-icon">

  <style>
    .card-abrir-chamado {
      padding: 30px 0 0 0;
      width: 100%;
      margin: 0 auto;
    }
    .hide-on-small {
      display: none;
    }
    @media (min-width: 1000px) {
      .hide-on-small {
        display: inline;
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
        <a class="nav-link" href="#">Editar Chamado</a>
      </li>
    </ul>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="editar_chamado.php">VOLTAR</a>
      </li>
    </ul>
  </nav>

  <div class="container">
    <div class="row">
      <div class="card-abrir-chamado">
        <div class="card">
          <div class="card-header">Ordem de Serviço <?php echo (int)$row->id_chamado; ?></div>
          <div class="card-body">
            <div class="row">
              <div class="col">
                <form action="conf_edit_chamado.php" method="POST">

                  <input name="id_chamado" type="hidden" class="form-control" value="<?php echo (int)$row->id_chamado; ?>" required>

                  <div class="form-group">
                    <label>Título</label>
                    <input name="titulo" type="text" class="form-control" value="<?php echo htmlspecialchars($row->titulo); ?>" required autofocus>
                  </div>

                  <div class="card-body">
                    <div class="row">
                      <div class="col-6 d-flex justify-content-center">
                        <label class="col-2 d-flex justify-content-center align-self-center hide-on-small">Categoria</label>
                        <select name="categoria" class="form-control" required>
                          <option value="Impressora" <?php if ($row->categoria === 'Impressora') echo 'selected'; ?>>Impressora</option>
                          <option value="Hardware" <?php if ($row->categoria === 'Hardware') echo 'selected'; ?>>Hardware</option>
                          <option value="Software" <?php if ($row->categoria === 'Software') echo 'selected'; ?>>Software</option>
                          <option value="Rede" <?php if ($row->categoria === 'Rede') echo 'selected'; ?>>Rede</option>
                          <option value="Formatação" <?php if ($row->categoria === 'Formatação') echo 'selected'; ?>>Formatação</option>
                          <option value="Orçamento" <?php if ($row->categoria === 'Orçamento') echo 'selected'; ?>>Orçamento</option>
                          <option value="Outros" <?php if ($row->categoria === 'Outros') echo 'selected'; ?>>Outros</option>
                        </select>
                      </div>

                      <div class="col-6 d-flex justify-content-center">
                        <label class="col-2 d-flex justify-content-center align-self-center hide-on-small">Status</label>
                        <select name="status" class="form-control" required>
                          <option value="Aberto" <?php if ($row->statuschamado === 'Aberto') echo 'selected'; ?>>Aberto</option>
                          <option value="Em andamento" <?php if ($row->statuschamado === 'Em andamento' || $row->statuschamado === 'Andamento') echo 'selected'; ?>>Em andamento</option>
                          <option value="Finalizado" <?php if ($row->statuschamado === 'Finalizado') echo 'selected'; ?>>Finalizado</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Descrição do Usuário</label>
                    <textarea name="descricao" class="form-control" rows="3" disabled><?php echo htmlspecialchars(trim($row->descricao)); ?></textarea>
                  </div>

                  <div class="form-group">
                    <label>Descrição do Técnico</label>
                    <textarea name="descricaotecnico" class="form-control" rows="3"><?php echo htmlspecialchars(trim($row->descricaotecnico ?? '')); ?></textarea>
                  </div>

                  <div class="form-group">
                    <label>Valor (R$)</label>
                    <input name="valor" type="text" class="form-control" value="<?php echo htmlspecialchars($row->valor ?? '0'); ?>" required>
                  </div>

                  <div class="row mt-5">
                    <div class="col-6">
                      <a class="btn btn-lg btn-warning btn-block" href="editar_chamado.php">Cancelar</a>
                    </div>

                    <div class="col-6">
                      <button class="btn btn-lg btn-info btn-block" type="submit">Salvar</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
