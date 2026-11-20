<?php
require_once "validador_perfil.php";
require_once 'config.php';

$id_usuario = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Evita que o próprio administrador logado se autoexclua
if ($id_usuario > 0 && $id_usuario !== (int)$_SESSION['id']) {
    $stmt = $conexao->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: usuarios.php?delete=sucesso');
exit;
?>
