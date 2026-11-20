<?php
require_once "validador_acesso.php";

// Apenas Administradores (Adm) e Técnicos (Tec) têm permissão para editar/gerenciar chamados
if (!isset($_SESSION['perfil']) || ($_SESSION['perfil'] !== 'Adm' && $_SESSION['perfil'] !== 'Tec')) {
    header('Location: home.php?permissao=nao');
    exit;
}
?>
