<?php
require_once "config.php";

// Verifica se os IDs do produto e do usuário foram passados na URL
if (isset($_GET['id_produto']) && isset($_GET['id_usuario'])) {
    $id_produto = $_GET['id_produto'];
    $id_usuario = $_GET['id_usuario'];

    try {
        // Inicia transação
        $pdo->beginTransaction();

        // Incrementa o contador de cliques para o produto específico
        $updateProduto = "UPDATE produtos SET cliques = cliques + 1 WHERE id = :id_produto";
        $stmt = $pdo->prepare($updateProduto);
        $stmt->bindParam(':id_produto', $id_produto, PDO::PARAM_INT);
        $stmt->execute();

        // Insere um registro na tabela cliques_afiliados
        $insertClique = "INSERT INTO cliques_afiliados (id_produto, id_usuario) VALUES (:id_produto, :id_usuario)";
        $stmt = $pdo->prepare($insertClique);
        $stmt->bindParam(':id_produto', $id_produto, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        // Commit das transações
        $pdo->commit();

        // Redireciona para watch.php com o ID do produto na URL
        header("Location: watch.php?id_produto=" . $id_produto);
        exit();
    } catch (Exception $e) {
        // Em caso de erro, realiza rollback
        $pdo->rollback();
        echo "Ocorreu um erro: " . $e->getMessage();
    }
} else {
    echo "ID do produto ou do usuário não fornecido.";
}
?>
