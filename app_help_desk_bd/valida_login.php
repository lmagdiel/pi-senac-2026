<?php
// Carrega o script de validação de login na pasta segura fora da pasta pública
if (file_exists(__DIR__ . '/../app_help_desk_bd_cima/valida_login.php')) {
    require __DIR__ . '/../app_help_desk_bd_cima/valida_login.php';
} elseif (file_exists(__DIR__ . '/../../app_help_desk_bd_cima/valida_login.php')) {
    require __DIR__ . '/../../app_help_desk_bd_cima/valida_login.php';
} else {
    require __DIR__ . '/app_help_desk_bd_cima/valida_login.php';
}
?>
