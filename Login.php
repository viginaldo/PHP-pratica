

<?php
session_start();
include('conexao.php'); // Conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta no banco de dados
    $stmt = $con->prepare("SELECT * FROM usuarios WHERE nome = ? LIMIT 1");
    $stmt->bind_param("s", $username); // Bind do parâmetro
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();


    //usuário e da senha
    if ($user && password_verify($password, $user['senha'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['nome'];
        $_SESSION['id'] = $user['id'];
        header("Location: Index.php"); 
        exit;
    } else {
        $error = "USER / SENHA INCORRETO! :)";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Farmácia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Roboto condensed;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f3f3f3;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .login-container img {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-container button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #45a049;
        }

        .login-container p {
            margin-top: 15px;
            font-size: 14px;
        }

        .login-container a {
            color: #4CAF50;
            text-decoration: none;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="img/logo.png" alt="Logo da Farmácia">
        <h2>PharmaFind - Login</h2>
        <form action="login.php" method="POST">
            <input type="text" name="username" id="username" placeholder="Nome de usuário" required>
            <input type="password" name="password" id="password" placeholder="Senha" required>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <button type="submit">Entrar</button>
            <p id="errorMsg" class="error"></p>
        </form>
        <p>Nao tem um conta? <a href="Registar.php">Clique aqui</a></p>
    </div>
</body>
</html>
