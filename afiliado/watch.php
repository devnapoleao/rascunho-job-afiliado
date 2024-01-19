<?php
require_once "config.php";

// Verifica se o ID do produto foi passado na URL
if (isset($_GET['id_produto'])) {
    $id_produto = $_GET['id_produto'];

    // Prepara a consulta para obter o nome do arquivo do vídeo
    $sql = "SELECT link_video FROM produtos WHERE id = :id_produto";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_produto', $id_produto, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Verifica se o produto foi encontrado
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch();
            $videoPath = $row["link_video"];
        } else {
            // Produto não encontrado
            echo "Produto não encontrado.";
            exit;
        }
    } else {
        echo "Erro ao executar a consulta.";
        exit;
    }
} else {
    echo "ID do produto não fornecido.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Assistir Vídeo</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilo personalizado -->
    <link href="style.css" rel="stylesheet">
</head>
<body>
<body>
    <div class="video-container">
        <h2>Assistir Vídeo</h2>
        <?php if (!empty($videoPath)): ?>
            <video controls>
                <source src="<?php echo htmlspecialchars($videoPath); ?>" type="video/mp4">
                Seu navegador não suporta a exibição deste vídeo.
            </video>
        <?php else: ?>
            <p class="error-message">Vídeo não encontrado.</p>
        <?php endif; ?>
    </div>
</body>

</body>
</html>
