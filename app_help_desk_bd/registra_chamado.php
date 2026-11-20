<?php
require_once "validador_acesso.php";
require_once 'config.php';

$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
$categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
$id_usuario = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;
$statuschamado = 'Aberto';

if (!empty($titulo) && !empty($categoria) && !empty($descricao) && $id_usuario > 0) {
    // Inserção segura no banco de dados com Prepared Statement
    $stmt = $conexao->prepare("INSERT INTO chamados (titulo, categoria, descricao, id_usuario, statuschamado) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssis", $titulo, $categoria, $descricao, $id_usuario, $statuschamado);
        $res = $stmt->execute();
        $stmt->close();

        if ($res) {
            header('Location: abrir_chamado.php?cadastro=efetuado');
            exit;
        }
    }
}

header('Location: abrir_chamado.php?cadastro=falha');
exit;
?>
