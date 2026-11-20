<?php
require_once "validador_perfil_tecnico.php";
require_once 'config.php';

$id_chamado = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_chamado > 0) {
    // Exclusão segura com Prepared Statement
    $stmt = $conexao->prepare("DELETE FROM chamados WHERE id_chamado = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_chamado);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: editar_chamado.php?delete=sucesso');
exit;
?>
