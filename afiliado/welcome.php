<?php
session_start();

// Verifica se o usuário está logado, se não, redireciona para a página de login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

require_once "config.php";

// Mensagem de status para operações CRUD
$statusMsg = "";

// Adicionando um novo produto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $titulo = $_POST['titulo_produto'];

    // Insere o título na tabela, inicialmente sem o link do vídeo
    $insertQuery = "INSERT INTO produtos (titulo) VALUES (:titulo)";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->bindParam(':titulo', $titulo);

    if ($stmt->execute()) {
        // Obtém o ID do último produto inserido
        $ultimoId = $pdo->lastInsertId();

        // Verifica se o arquivo de vídeo foi carregado
        if (isset($_FILES["video"]) && $_FILES["video"]["error"] == 0) {
            $videoPath = "videos/" . $ultimoId . "." . pathinfo($_FILES["video"]["name"], PATHINFO_EXTENSION);
            
            // Move o arquivo para a pasta 'videos'
            if (move_uploaded_file($_FILES["video"]["tmp_name"], $videoPath)) {
                // Atualiza a entrada do produto com o link do vídeo
                $updateQuery = "UPDATE produtos SET link_video = :link_video WHERE id = :id";
                $stmt = $pdo->prepare($updateQuery);
                $stmt->bindParam(':link_video', $videoPath);
                $stmt->bindParam(':id', $ultimoId);
                $stmt->execute();

                $statusMsg = "Produto adicionado com sucesso!";
            } else {
                $statusMsg = "Erro ao fazer upload do vídeo.";
            }
        } else {
            $statusMsg = "Erro ao fazer upload do vídeo.";
        }
    } else {
        $statusMsg = "Erro ao adicionar produto.";
    }
}


// Recuperar produtos para exibir
$selectQuery = "SELECT * FROM produtos";
$stmt = $pdo->query($selectQuery);
$produtos = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Produtos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilo personalizado -->
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Adicionar Produto</h2>
        <p class="text-success"><?php echo $statusMsg; ?></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
            <div class="form-group">
                <label>Título do Produto:</label>
                <input type="text" name="titulo_produto" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Vídeo:</label>
                <input type="file" name="video" class="form-control-file" required>
            </div>
            <div>
                <input type="submit" name="submit" value="Adicionar Produto" class="btn btn-primary">
            </div>
        </form>
    </div>

    <div class="container">
        <h2>Lista de Produtos</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Título do Produto</th>
                    <th>Link do Vídeo</th>
                    <th>Link Afiliado</th>
                    <th>Cliques</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produto['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($produto['link_video']); ?></td>
                    <td>
                        <a href="#" class="copy-link" data-link="click.php?id_produto=<?php echo $produto['id']; ?>&id_usuario=<?php echo $_SESSION['id']; ?>">Link Afiliado</a>
                    </td>
                    <td><?php echo htmlspecialchars($produto['cliques']); ?></td>
                    <td>
                        <!-- Botões de editar e excluir -->
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    var copyLinkElements = document.querySelectorAll('.copy-link');

    copyLinkElements.forEach(function(elem) {
        elem.addEventListener('click', function(e) {
            e.preventDefault();
            var linkPath = elem.getAttribute('data-link');
            var currentUrl = window.location.href;
            var baseUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/'));
            var fullLink = baseUrl + '/' + linkPath;
            var dummy = document.createElement('input');
            document.body.appendChild(dummy);
            dummy.value = fullLink;
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);
            alert('Link copiado para a área de transferência: ' + fullLink);
        });
    });
});
</script>



</body>
</html>
