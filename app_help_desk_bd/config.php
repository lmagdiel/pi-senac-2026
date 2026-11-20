<?php
// Carrega o arquivo de configuração localizado fora da pasta pública (pasta segura)
if (file_exists(__DIR__ . '/../app_help_desk_bd_cima/config.php')) {
    require_once __DIR__ . '/../app_help_desk_bd_cima/config.php';
} elseif (file_exists(__DIR__ . '/../../app_help_desk_bd_cima/config.php')) {
    require_once __DIR__ . '/../../app_help_desk_bd_cima/config.php';
} else {
    require_once __DIR__ . '/app_help_desk_bd_cima/config.php';
}
?>
