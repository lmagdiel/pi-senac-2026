<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

$usuarioAutenticado = false;

// Recebendo dados via método POST (com fallback para GET se aplicável)
$emailUsuario = isset($_POST['email']) ? trim($_POST['email']) : (isset($_GET['email']) ? trim($_GET['email']) : '');
$senhaUsuario = isset($_POST['senha']) ? $_POST['senha'] : (isset($_GET['senha']) ? $_GET['senha'] : '');

if (!empty($emailUsuario) && !empty($senhaUsuario)) {
    // Busca segura no banco de dados com Prepared Statement (previne SQL Injection)
    $stmt = $conexao->prepare("SELECT id_usuario, nome, email, senha, perfil FROM usuarios WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $emailUsuario);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_object()) {
            // 1. Verificação com password_verify (hash seguro bcrypt/Argon2)
            if (password_verify($senhaUsuario, $row->senha)) {
                $usuarioAutenticado = true;
            }
            // 2. Compatibilidade legada com MD5 e migração automática de hash
            elseif (md5($senhaUsuario) === $row->senha) {
                $usuarioAutenticado = true;
                $novoHash = password_hash($senhaUsuario, PASSWORD_DEFAULT);
                $stmtUpgrade = $conexao->prepare("UPDATE usuarios SET senha = ? WHERE id_usuario = ?");
                if ($stmtUpgrade) {
                    $stmtUpgrade->bind_param("si", $novoHash, $row->id_usuario);
                    $stmtUpgrade->execute();
                    $stmtUpgrade->close();
                }
            }

            if ($usuarioAutenticado) {
                $_SESSION['id'] = $row->id_usuario;
                $_SESSION['perfil'] = $row->perfil;
                $_SESSION['nome'] = $row->nome;
            }
        }
        $stmt->close();
    }
}

if ($usuarioAutenticado) {
    $_SESSION['autenticado'] = 'sim';
    header('Location: home.php');
    exit;
} else {
    $_SESSION['autenticado'] = 'nao';
    header('Location: index.php?login=erro');
    exit;
}
?>
