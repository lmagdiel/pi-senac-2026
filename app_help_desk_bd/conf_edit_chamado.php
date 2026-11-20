<?php
require_once "validador_perfil_tecnico.php";
require_once 'config.php';

$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
$categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
$id_chamado = isset($_POST['id_chamado']) ? (int)$_POST['id_chamado'] : 0;
$descricaotecnico = isset($_POST['descricaotecnico']) ? trim($_POST['descricaotecnico']) : '';
$statuschamado = isset($_POST['status']) ? trim($_POST['status']) : '';
$valor = isset($_POST['valor']) ? (float)str_replace(',', '.', $_POST['valor']) : 0.0;

if ($id_chamado > 0 && !empty($titulo) && !empty($categoria) && !empty($statuschamado)) {
    // Atualização segura dos dados no banco com Prepared Statement
    $stmt = $conexao->prepare("UPDATE chamados SET titulo = ?, categoria = ?, descricaotecnico = ?, statuschamado = ?, valor = ? WHERE id_chamado = ?");
    if ($stmt) {
        $stmt->bind_param("ssssdi", $titulo, $categoria, $descricaotecnico, $statuschamado, $valor, $id_chamado);
        $res = $stmt->execute();
        $stmt->close();

        if ($res) {
            header('Location: editar_chamado.php?edicao=sucesso');
            exit;
        }
    }
}

header('Location: editar_chamado.php?edicao=falha');
exit;
?>
