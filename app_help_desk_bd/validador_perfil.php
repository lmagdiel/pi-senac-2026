<?php
require_once "validador_acesso.php";

// Apenas perfil de Administrador (Adm) tem acesso às funcionalidades de administração
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'Adm') {
    header('Location: home.php?permissao=nao');
    exit;
}
?>
