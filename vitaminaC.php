<?php

session_start();
include 'conexao.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Recupera o nome do usuário a partir do banco de dados
$sql = "SELECT nome FROM usuarios WHERE id = '$user_id'";
$result = mysqli_query($con, $sql);

if ($result) {
    $user_data = mysqli_fetch_assoc($result);
    $user_name = $user_data['nome'];
    
    
    $initial = strtoupper(substr($user_name, 0, 2));
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['medicamento'])) {
    $medicamento = trim($_GET['medicamento']);

    // Preparar a consulta para verificar se o medicamento existe
    $sql = "SELECT nome, preco FROM medicamentos WHERE nome LIKE ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $medicamento);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Medicamento encontrado, pegar o preço e redirecionar para buy.php
        $row = $result->fetch_assoc();
        $preco = $row['preco'];
        header("Location: payment.php?medicamento=" . urlencode($medicamento) . "&preco=" . urlencode($preco));
        exit;
    } else {
        // Medicamento não encontrado
        echo "<script>
        var confirma = alert('Medicamento não encontrado.');
        if (confirma) {
            window.location.href = 'Index.php';
        }
    </script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/buy.css">
    <title>Suplemento Vitamina C</title>
</head>
<body>
    <header>
        <div class="logo">
            <img src="img/logoname.png" height="70px" alt="PharmaFind Logo">
        </div>
        <nav>
            <ul>
                <li><a href="Index.php">Inicio</a></li>
                <li><a href="#" onclick="toggleCategories();">Categorias</a></li>
                <li><a href="Historico.php">Historico</a></li>
                <li><a href="about.php">Sobre nos</a></li>
                <li><a href="contact.php">Contacto</a></li>
            </ul>
        </nav>
        <div class="icons">
            <form method="GET" action="">
                <input type="text" name="medicamento" placeholder="Pesquise o seu medicamento aqui..." required>
                <button class="but" type="submit">Pesquisar</button>
            </form>
            <a href="#">
                <div style="width: 50px; height: 50px; background-color: #ffff; color: #003366; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 27px;">
                    <?php echo $initial; ?>
                </div>
            </a>
            <a href="Login.php"><img src="img/logout.png" height="50px" alt="Cart Icon"></a>
        </div>

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
    
 
    <section>
        <div class="container">
            <section class="banner3">
                <img src="img/vitaminaC.png" alt="Suplemento Vitamina C">
            </section>
            <div class="container-txt">
                <h2 class="tit">Suplemento Vitamina C</h2>
                <p>Suplemento para fortalecer o sistema imunológico. <strong>Como usar:</strong> Tomar 1 cápsula ao dia, preferencialmente após uma refeição.</p>
                <br>
                <p class="sec">
                    Indicado para aumentar a imunidade e combater os radicais livres.
                </p>
                <div class="container-card">
                    <div class="acction">
                        <a href="payment.php?medicamento=<?php echo urlencode("Vitamina C"); ?>&preco=<?php echo urlencode(150); ?>">
                            <img src="img/buy.png" alt="Comprar">
                            <span>Comprar</span>
                        </a>
                    </div>
                    <div class="acction">
                        <img src="img/bloc.png" alt="Localização">
                        <span>Localização</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <br>
    <section class="produtos-info">
        <h2>Informações e Produtos Recomendados</h2>
        <div class="grid-container">
            <div class="card">
                <img src="img/vitc1.png" alt="Alternativa 1">
                <h3 class="nam">Vitamina C 1000mg</h3>
                <p class="desc">
                    <ul>
                        <li>Melhora a imunidade</li>
                        <li>Comprimidos de 1000mg</li>
                    </ul>
                </p>
            </div>
            <div class="card">
                <img src="img/vitc2.png" alt="Alternativa 2">
                <h3 class="nam">Vitamina C 500mg</h3>
                <p class="desc">
                    <ul>
                        <li>Ideal para o uso diário</li>
                        <li>Fortalece o sistema imunológico</li>
                    </ul>
                </p>
            </div>
            <div class="card">
                <img src="img/vitc3.png" alt="Alternativa 3">
                <h3 class="nam">Suplemento com Vitamina C e Zinco</h3>
                <p class="desc">
                    <ul>
                        <li>Aumenta a proteção imunológica</li>
                        <li>Com vitamina C e zinco</li>
                    </ul>
                </p>
            </div>
            <div class="card">
                <img src="img/vitc4.png" alt="Alternativa 4">
                <h3 class="nam">Pó de Vitamina C</h3>
                <p class="desc">
                    <ul>
                        <li>Fórmula em pó para misturar em líquidos</li>
                        <li>Ajuda a aumentar a energia e imunidade</li>
                    </ul>
                </p>
            </div>
        </div>
    </section>
</body>
</html>
