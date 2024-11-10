<?php
session_start();
include 'conexao.php'; // Conexão com o banco de dados


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

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/about.css">
    <title>About us</title>
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
                <a href="#">
                    <img src="img/pet.png" alt="Petshop">
                    <p>Petshop</p>
                </a>
            </div>
        </div>
        <script src="js/script.js"></script>
    </header>

    <section class="banner">
        <h2>QUEM SOMOS</h2>
        <strong>
            PharmaFind é uma plataforma inovadora focada em facilitar o acesso a medicamentos em Moçambique. Nosso sistema conecta utentes diretamente às farmácias locais, permitindo que eles localizem de forma rápida e fácil os medicamentos que precisam, economizando tempo e oferecendo mais comodidade. Com a missão de melhorar a saúde e o bem-estar da comunidade, o PharmaFind ajuda a superar o desafio de encontrar medicamentos disponíveis, especialmente em regiões onde o acesso pode ser mais difícil.
        </strong>       

        <div class="call">
            <a href="contact.php">
                <img src="img/call.png" alt="call-center">
            </a>
        </div>
    </section>

    <br>

    <section class="banner2">
        <h2>OBJECTIVO</h2>
        <strong>
            Nosso principal objetivo é garantir que todas as pessoas em Moçambique tenham acesso rápido e eficaz aos medicamentos de que precisam. Através de uma rede colaborativa de farmácias, proporcionamos aos utentes a capacidade de verificar a disponibilidade de medicamentos nas proximidades, simplificando o processo de busca e garantindo que eles possam receber o tratamento de forma mais ágil.
        </strong>       
    </section>

    <br>

    <section>

        <div class="container">
            <section class="banner3">
                <h2>MISSAO</h2>
                <strong>
                    A missão da PharmaFind é transformar a forma como as pessoas encontram e acessam medicamentos, promovendo um sistema de saúde mais eficiente e acessível. Estamos comprometidos em:
                    <ul>
                        <li>
                            - Facilitar o acesso a medicamentos essenciais para todos os utentes, em qualquer lugar.
                        </li>
                    
                        <li>
                            - Promover a saúde comunitária através da digitalização e otimização da comunicação entre
                            farmácias e utentes.
                        </li>
                        <li>
                            - Empoderar farmácias locais com uma ferramenta que aumenta a visibilidade e eficiência, ao mesmo tempo que melhora o atendimento ao cliente.
                         </li>
                    </ul>
                    
                    
                </strong>       
            </section>
            <div class="image-container">
                <img src="img/lacation.png" alt="Imagem ao lado do formulário">
            </div>
        </div>
    </section>
    <br>
    <footer>
        <p>Copyright Notice © [2024] Pharma FIND. All rights reserved.</p>
        <img src="img/logo.png" alt="logotipo">
        
    </footer>
</body>
</html>