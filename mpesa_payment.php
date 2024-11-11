<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Verifica se o pagamento foi confirmado
if (isset($_GET['confirmado']) && $_GET['confirmado'] === 'true' && isset($_GET['medicamento']) && isset($_GET['quantidade']) && isset($_GET['entrega'])) {
    $medicamentoNome = $_GET['medicamento'];
    $quantidadeComprada = $_GET['quantidade'];
    $entrega = $_GET['entrega'];
    $contacto = $_GET['phoneNumber'];

    // Consulta para verificar o estoque em várias farmácias
    $sql = "SELECT id, quant, farmacia_id, preco FROM medicamentos WHERE nome = ? AND quant >= ?"; // Filtra farmácias com estoque suficiente
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $medicamentoNome, $quantidadeComprada);
    $stmt->execute();
    $result = $stmt->get_result();

    $pagamentoFeito = false;
    $farmaciaEscolhida = null;

    // Verifica se existe uma farmácia com estoque suficiente
    while ($row = $result->fetch_assoc()) {
        // Se encontrar uma farmácia com a quantidade desejada
        $farmaciaEscolhida = $row;
        break;  // Sai do loop, já encontramos a farmácia com o estoque suficiente
    }

    // Se encontrar uma farmácia com estoque suficiente, processa a venda
    if ($farmaciaEscolhida) {
        $medicamentoId = $farmaciaEscolhida['id'];
        $quantidadeAtual = $farmaciaEscolhida['quant'];
        $farmacia_id = $farmaciaEscolhida['farmacia_id'];
        $preco = $farmaciaEscolhida['preco'];

        // Atualiza o estoque da farmácia
        $novaQuantidade = $quantidadeAtual - $quantidadeComprada;
        $updateSql = "UPDATE medicamentos SET quant = ? WHERE id = ?";
        $updateStmt = $con->prepare($updateSql);
        $updateStmt->bind_param("ii", $novaQuantidade, $medicamentoId);

        // Realiza o pagamento
        $total = $preco * $quantidadeComprada;
        $pagamento = "M-pesa";
        if ($entrega == 1) {
            $total += 100; // Taxa de entrega
        }

        if ($updateStmt->execute()) {
            // Registra a venda
            $insertVendaSql = "INSERT INTO vendas (us_id, farmacia_id, medicamento_id, quantidade, preco, m_pagamento, total, entrega, contacto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insertVendaStmt = $con->prepare($insertVendaSql);
            $insertVendaStmt->bind_param("iiiidsdis", $user_id, $farmacia_id, $medicamentoId, $quantidadeComprada, $preco, $pagamento, $total, $entrega, $contacto);

            if ($insertVendaStmt->execute()) {
                $pagamentoFeito = true;
                echo '<script>
                    alert("Pagamento Confirmado!");
                    window.location.href ="Index.php";
                </script>';
            }
        }
    }

    // Caso não haja farmácia com estoque suficiente
    if (!$pagamentoFeito) {
        echo '<script>
            alert("Quantidade insuficiente em estoque!");
            window.history.back();
        </script>';
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
    <title>PharmaFind - Pagamento M-Pesa</title>
    <style>
        /* Estilos principais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            align-items: center;
            justify-content: center;
        }

        body {
            padding-top: 81px;
            font-family: Roboto condensed;
            justify-content: center;
            display: flex;
        }
        header {
            background-color: #003366;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            position: fixed;
            width: 100%;
            height: 81px;
            top: 0;
            left: 0;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Sombra basica*/
            z-index: 1000; /*cabeçalho sobre o conteúdo */
        }

        header .logo span {
            color: #00ffcc;
        }

        header nav ul {
            list-style: none;
            display: flex;
            gap: 15px;
            padding-top: 50px;
        }

        header nav ul li a {
            font-size: 18px;
            padding: 8px;
            color: #fff;
            text-decoration: none;
        }

        /* Menu principal */
        .menu {
            display: flex;
            list-style: none;
            background-color: #003366;
            padding: 10px;
            color: white;
        }

        .menu li {
            margin-right: 20px;
        }

        /* Menu de categorias suspenso */
        .categories-menu {
            display: none; 
            position: fixed;
            top: 82px;
            left: 0;
            width: 100%;
            background-color: #003366;
            padding: 20px 0px;
            justify-content: space-around;
            z-index: 900;
        }

        .category-item, a {
            text-align: center;
            color: white;
            text-decoration: none;
        }

        .category-item img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 5px;
        }



        header .icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }


        header .icons input {
            padding: 5px;
            border-radius: 8px;
            border: 1px  #9b9595;
            width: 300px;
            height: 30px;
        }
        .container {
            background-color: white;
            width: 100%;
            max-width: 400px;
            padding: 20px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .title {
            color: #b22222;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            color: #b22222;
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
            text-align: left;
        }

        input[type="text"] {
            width: 99%;
            padding: 10px;
            margin-bottom: 10px;
            border: 2px solid #b22222;
            border-radius: 4px;
        }

        .submit-button {
            background-color: #b22222;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-bottom: 10px;
        }

        .submit-button:hover {
            background-color: #003366;
        }

        #codeSection {
            margin-top: 20px;
        }
        .container img {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<header>
        <div class="logo">
            <img src="img/logoname.png" height="70px" alt="PharmaFind Logo">
        </div>
        <nav>
            <ul>
                <li><a href="Index.php">Home</a></li>
                <li><a href="#" onclick="toggleCategories();">Category</a></li>
                <li><a href="Historico.php">Historico</a></li>
                <li><a href="about.php">About us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
        <div class="icons">
            <a href="#"><img src="img/user.png" height="50px" alt="User Icon"></a>
            <a href="Login.php"><img src="img/logout.png" height="50px" alt="Cart Icon"></a>
        </div>
        
        <!-- Menu de categorias-->
        <div class="categories-menu" id="categoriesMenu">
            <div class="category-item">
                <a href="mom_baby.php">
                    <img src="img/mom.png" alt="Mamã e bebé">
                    <p>Mamã e bebé</p>
                </a>
            </div>
            <div class="category-item">
                <a href="beleza.php">
                    <img src="img/saude.png" alt="Saúde e beleza">
                    <p>Saúde e beleza</p>
                </a>
            </div>
            <div class="category-item">
                <a href="vida_saudavel.php">
                    <img src="img/vida.png" alt="Vida saudável">
                    <p>Vida saudável</p>
                </a>
            </div>
            <div class="category-item">
                <a href="sex.php">
                    <img src="img/sexualidade.png" alt="Sexualidade">
                    <p>Sexualidade</p>
                </a>
            </div>
            <div class="category-item">
                <a href="fitnes.php">
                    <img src="img/fit.png" alt="Fitness">
                    <p>Fitness</p>
                </a>
            </div>
            <div class="category-item">
                <a href="Petshop.php">
                    <img src="img/pet.png" alt="Petshop">
                    <p>Petshop</p>
                </a>
            </div>
        </div>
        <script src="js/script.js"></script>
    </header>


    <div class="container">
        <img src="img/mpesa.png" alt="Logo da Farmácia">
        <h1 class="title">Pagamento com M-Pesa</h1>
        
        
        <form id="mpesaForm" action="#" method="POST">
            
            <label for="phoneNumber">Número de Celular:</label>
            <input type="text" id="phoneNumber" name="phoneNumber" placeholder="Digite seu número" required>

            <div id="codeSection" style="display: none;">
                <label for="confirm">Código:</label>
                <input type="text" id="confirm" name="confirm" placeholder="Digite o código recebido" required>
            </div>

            <button type="button" class="submit-button" onclick="confirmMPesaPayment()">Confirmar Pagamento</button>
            
            <button type="button" class="submit-button" onclick="window.history.back();">Voltar</button>
        </form>
    </div>

    <script>
        let codeSent = false; // Controla se o código já foi enviado
        let pagamentoConfirmado = false;

        function confirmMPesaPayment() {
            const phoneNumber = document.getElementById('phoneNumber').value.trim();
            const confirmCode = document.getElementById('confirm').value.trim();
            const codeSection = document.getElementById('codeSection');

            if (!codeSent) {
                if (phoneNumber === '') {
                    alert("Por favor, insira o número de celular.");
                    return;
                }

                alert("Um código de confirmação foi enviado para " + phoneNumber);
                codeSent = true;
                codeSection.style.display = 'block';
                document.getElementById('confirm').focus();
            } else {
                if (confirmCode === '') {
                    alert("Por favor, insira o código recebido.");
                    return;
                }

                const medicamentoNome = encodeURIComponent('<?php echo $_GET['medicamento']; ?>');
                const quantidade = encodeURIComponent('<?php echo $_GET['quantidade']; ?>');
                const entrega = encodeURIComponent('<?php echo $_GET['entrega']; ?>');
                
                window.location.href = `mpesa_payment.php?medicamento=${medicamentoNome}&quantidade=${quantidade}&entrega=${entrega}&confirmado=true&phoneNumber=${phoneNumber}`;
            }
        }

    </script>
</body>
</html>
