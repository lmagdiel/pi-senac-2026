<?php
require_once 'config.php';

$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha = isset($_POST['senha']) ? $_POST['senha'] : '';
$perfil = isset($_POST['perfil']) ? trim($_POST['perfil']) : '';

// Validação dos campos obrigatórios
if (empty($nome) || empty($email) || empty($senha) || empty($perfil) || $perfil === '-- Selecione --') {
    header('Location: cadastro.php?validaperfil=erro');
    exit;
}

// Verifica se o e-mail já está cadastrado com Prepared Statement
$stmt = $conexao->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header('Location: cadastro.php?email=erro');
        exit;
    }
    $stmt->close();
}

// Criptografia da senha usando password_hash (bcrypt)
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Inserção segura dos dados no banco com Prepared Statement
$stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("ssss", $nome, $email, $senhaHash, $perfil);
    $res = $stmt->execute();
    $stmt->close();

    if ($res) {
        header('Location: index.php?usuario=sucesso');
        exit;
    }
}

header('Location: cadastro.php?usuario=falha');
exit;
?>
