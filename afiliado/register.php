<?php
// Inclui arquivo de configuração
require_once "config.php";

// Define variáveis e inicializa com valores vazios
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Processa os dados do formulário quando é submetido
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Valida nome de usuário
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor, insira um nome de usuário.";
    } else{
        // Prepara uma declaração de seleção
        $sql = "SELECT id FROM users WHERE username = :username";

        if($stmt = $pdo->prepare($sql)){
            // Vincula variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

            // Define parâmetros
            $param_username = trim($_POST["username"]);

            // Tenta executar a declaração preparada
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "Este nome de usuário já está em uso.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fecha declaração
            unset($stmt);
        }
    }
    
    // Valida senha
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor, insira uma senha.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "A senha deve ter pelo menos 6 caracteres.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Valida a confirmação da senha
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Por favor, confirme a senha.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "As senhas não coincidem.";
        }
    }
    
    // Verifica os erros de entrada antes de inserir no banco de dados
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        // Prepara uma declaração de inserção
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";

        if($stmt = $pdo->prepare($sql)){
            // Vincula variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);

            // Define parâmetros
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Cria uma senha hash

            // Tenta executar a declaração preparada
            if($stmt->execute()){
                // Redireciona para a página de login
                header("location: index.php");
            } else{
                echo "Oops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fecha declaração
            unset($stmt);
        }
    }
    
    // Fecha conexão
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilo personalizado -->
    <link href="style.css" rel="stylesheet">
    <title>Cadastro</title>
</head>
<body>
    <div class="form-container">
        <h2>Cadastro</h2>
        <p>Preencha este formulário para criar uma conta.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nome de Usuário</label>
                <input type="text" name="username" value="<?php echo $username; ?>" class="form-control">
                <span class="error"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="password" class="form-control">
                <span class="error"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirme a Senha</label>
                <input type="password" name="confirm_password" class="form-control">
                <span class="error"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Cadastrar" class="btn btn-primary">
            </div>
            <p>Já tem uma conta? <a href="index.php">Faça login aqui</a>.</p>
        </form>
    </div>    
</body>

</html>
