<?php
require_once "validador_perfil.php";
require_once 'config.php';

$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$perfil = isset($_POST['perfil']) ? trim($_POST['perfil']) : '';
$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;

if ($id_usuario > 0 && !empty($nome) && !empty($email) && !empty($perfil)) {
    // Atualização segura com Prepared Statement
    $stmt = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ?, perfil = ? WHERE id_usuario = ?");
    if ($stmt) {
        $stmt->bind_param("sssi", $nome, $email, $perfil, $id_usuario);
        $res = $stmt->execute();
        $stmt->close();

        if ($res) {
            header('Location: usuarios.php?edicao=sucesso');
            exit;
        }
    }
}

header('Location: usuarios.php?edicao=falha');
exit;
?>
