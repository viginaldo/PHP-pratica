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


// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['medicamento'])) {
    $medicamento = trim($_GET['medicamento']);

    // Preparar a consulta para verificar se o medicamento existe
    $sql = "SELECT nome, preco FROM medicamentos WHERE nome LIKE ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $medicamento);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
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
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaFind</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <img src="img/logoname.png" height="70px" alt="PharmaFind Logo">
        </div>
        <nav>
            <ul>
                <li><a href="Index.php">Inicio</a></li>
                <li><a href="#" onclick="toggleCategories();">Categorias</a></li>
                <li><a href="Historico.php">Historico</a></li>
                <li><a href="geral.php">Lista</a></li>
                <li><a href="about.php">Sobre</a></li>
                
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

    <!-- Banner -->
    <section class="banner">
        <h2>CONECTANDO VOCÊ A SAÚDE COM RAPIDEZ</h2>
        <strong>PharmaFind é a sua plataforma de busca rápida e fácil por medicamentos. Encontre farmácias próximas com o que você precisa, com apenas alguns cliques!</strong>
        <br>
        <br>
        <a class="but" href="about.php">Saiba mais</a>
        <div class="slogan">
            <img src="img/mapl.png" alt="location">
        </div>

        <div class="call">
            <a href="contact.php">
                <img src="img/call.png" alt="call-center">
            </a>
        </div>
    </section>
    <br>

    <section class="recommendations">
        <h3>Mais Encomendados</h3>
        
        <div class="products">
            <div class="product">
                <a href="payment.php?medicamento=<?php echo urlencode("Paracetamol"); ?>&preco=<?php    echo urlencode(300); ?>">
                    <img src="img/Paracetamol.png" alt="Paracetamol">
                </a>
                <h4 class="product-name">Paracetamol</h4>
                
            </div>
            <div class="product">
            <a class="prod" href="payment.php?medicamento=<?php echo urlencode("Vitamina C"); ?>&    preco=<?php    echo urlencode(150); ?>">
                    <img src="img/vitaminaC.png" alt="Vitamina C">
                </a> 
                 <h4 class="product-name">Vitamina C</h4>
                
            </div>
            <div class="product">
                <a href="payment.php?medicamento=<?php echo urlencode("Bentli - 4Flu"); ?>&preco=<?php    echo urlencode(100); ?>">
               
                    <img src="img/Ben.png" alt="Bentli - 4Flu"> 
                </a>
                    <h4 class="product-name">Bentli - 4Flu</h4>
                
            </div>
            <div class="product">
                <a href="payment.php?medicamento=<?php echo urlencode("Ibuprofeno"); ?>&preco=<?php    echo urlencode(100); ?>">
                    <img src="img/Ibu.png" alt="Ibuprofen">
                </a>
                <h4 class="product-name">Ibuprofeno</h4>
                
            </div>
        </div>

    </section>

    <!-- Footer -->
    <footer>
        <p>Copyright Notice © [2024] Pharma FIND. All rights reserved.</p>
        <img src="img/logo.png" alt="logotipo">
    </footer>
</body>
</html>
