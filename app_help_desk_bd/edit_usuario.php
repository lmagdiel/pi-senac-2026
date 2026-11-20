<?php
require_once "validador_perfil.php";
require_once "config.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$row = null;

if ($id > 0) {
    $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_object();
        $stmt->close();
    }
}

if (!$row) {
    header('Location: usuarios.php');
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
  </style>
</head>

<body>
  <nav class="navbar navbar-dark bg-dark">
    <a class="navbar-brand" href="home.php">
      <img src="imagens/logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
      App Help Desk
    </a>
  </nav>

  <div class="container">
    <div class="row">
      <div class="card-abrir-chamado">
        <div class="card">
          <div class="card-header">Editar Usuário</div>
          <div class="card-body">
            <div class="row">
              <div class="col">
                <form action="conf_edit_usuario.php" method="POST">
                  
                  <input name="id_usuario" type="hidden" value="<?php echo (int)$row->id_usuario; ?>" required>
                
                  <div class="form-group">
                    <label>ID: </label>
                    <input name="id" type="text" class="form-control" value="<?php echo (int)$row->id_usuario; ?>" disabled>
                  </div>         
                  
                  <div class="form-group">
                    <label>Nome: </label>
                    <input name="nome" type="text" class="form-control" value="<?php echo htmlspecialchars($row->nome); ?>" required autofocus>
                  </div>
                  
                  <div class="form-group">
                    <label>E-mail:</label>
                    <input name="email" type="email" class="form-control" value="<?php echo htmlspecialchars($row->email); ?>" required>
                  </div>

                  <div class="form-group">
                    <label>Perfil</label>
                    <select name="perfil" class="form-control" required>
                      <option value="Usuario" <?php if ($row->perfil === 'Usuario') echo 'selected'; ?>>Usuario</option>
                      <option value="Tec" <?php if ($row->perfil === 'Tec' || $row->perfil === 'Tecnico') echo 'selected'; ?>>Tec</option>
                      <option value="Adm" <?php if ($row->perfil === 'Adm' || $row->perfil === 'Administrador') echo 'selected'; ?>>Adm</option>
                    </select>
                  </div>

                  <div class="row mt-5">
                    <div class="col-6">
                      <a class="btn btn-lg btn-warning btn-block" href="usuarios.php">Cancelar</a>
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
