<?php
require_once "validador_perfil.php";
require_once "config.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$autorizar = isset($_GET['autorizar']) ? $_GET['autorizar'] : '';

if ($id > 0) {
    if ($autorizar === 'simA') {
        $novoPerfil = 'Adm';
        $redirectParam = 'adm';
    } elseif ($autorizar === 'simT') {
        $novoPerfil = 'Tec';
        $redirectParam = 'tec';
    } else {
        $novoPerfil = 'Usuario';
        $redirectParam = 'usuario';
    }

    $stmt = $conexao->prepare("UPDATE usuarios SET perfil = ? WHERE id_usuario = ?");
    if ($stmt) {
        $stmt->bind_param("si", $novoPerfil, $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: autorizar_usuarios.php?usuario={$redirectParam}");
    exit;
}

header("Location: autorizar_usuarios.php");
exit;
?>
