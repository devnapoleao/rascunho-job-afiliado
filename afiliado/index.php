<?php
// Iniciar a sessão
session_start();

// Verifica se o usuário já está logado, se sim, redireciona para a página welcome.php
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    $_SESSION["id"] = $id; // Aqui, $id deve ser o ID do usuário obtido do banco de dados
    header("location: welcome.php");
    exit;
}

// Inclui arquivo de configuração
require_once "config.php";

// Define variáveis e inicializa com valores vazios
$username = $password = "";
$username_err = $password_err = "";

// Processa os dados do formulário quando é submetido
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Verifica se o nome de usuário está vazio
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor, insira o nome de usuário.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Verifica se a senha está vazia
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor, insira sua senha.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Valida credenciais
    if(empty($username_err) && empty($password_err)){
        // Prepara uma declaração de seleção
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Vincula variáveis à instrução preparada como parâmetros
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Define parâmetros
            $param_username = $username;
            
            // Tenta executar a declaração preparada
            if($stmt->execute()){
                // Verifica se o nome de usuário existe, se sim, verifica a senha
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $hashed_password = $row["password"];
                        if(password_verify($password, $hashed_password)){
                            // Senha está correta, então inicia uma nova sessão
                            
                            // Armazena dados em variáveis de sessão
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redireciona o usuário para a página de boas-vindas
                            header("location: welcome.php");
                        } else{
                            // Exibe uma mensagem de erro se a senha não for válida
                            $password_err = "A senha que você inseriu não estava correta.";
                        }
                    }
                } else{
                    // Exibe uma mensagem de erro se o nome de usuário não existir
                    $username_err = "Nenhuma conta encontrada com esse nome de usuário.";
                }
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
    <title>Login</title>
    <!-- Estilos e scripts aqui (se necessário) -->
</head>
<body>
    <div>
        <h2>Login</h2>
        <p>Por favor, preencha seus dados para fazer login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Nome de Usuário</label>
                <input type="text" name="username" value="<?php echo $username; ?>">
                <span><?php echo $username_err; ?></span>
            </div>    
            <div>
                <label>Senha</label>
                <input type="password" name="password">
                <span><?php echo $password_err; ?></span>
            </div>
            <div>
                <input type="submit" value="Login">
            </div>
            <p>Não tem uma conta? <a href="register.php">Registre-se agora</a>.</p>
        </form>
    </div>    
</body>
</html>
