<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    
    $stmt = $con->prepare("SELECT * FROM usuarios WHERE nome = ?");
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Este e-mail já está cadastrado!";
    } else {
       
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

       
        $stmt = $con->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $email, $senhaHash);

       
        if ($stmt->execute()) {
            $success = "Usuário registrado com sucesso!";
            header("Location: Login.php");
        } else {
            $error = "Erro ao registrar usuário: " . $stmt->error;
        }
    }
    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Novo Usuário</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f3f3f3;
            font-family: Roboto condensed;
        }

        .register-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .register-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .register-container input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }

        .register-container button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .register-container button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .register-container img {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }
        
        .register-container p {
            margin-top: 15px;
            font-size: 14px;
        }

        .register-container a {
            color: #4CAF50;
            text-decoration: none;
        }

        .register-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
    <img src="img/logo.png" alt="Logo da Farmácia">
        <h2>Registrar Novo Usuário</h2>
        <form action="Registar.php" method="POST">
            <input type="text" name="nome" placeholder="Nome" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Registrar</button>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php elseif (isset($success)): ?>
                <p class="success"><?php echo $success; ?></p>
            <?php endif; ?>

            <p>Tem um conta? <a href="Login.php">Clique aqui</a></p>
        </form>
    </div>
</body>
</html>
