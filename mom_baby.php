
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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mamã e bebé - Pharma Find</title>
    <link rel="stylesheet" href="css/mom_baby.css">
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
                <a href="Petshop.php">
                    <img src="img/pet.png" alt="Petshop">
                    <p>Petshop</p>
                </a>
            </div>
        </div>
        <script src="js/script.js"></script>
    </header>

    <!-- Seção Principal -->
    <main>
        <section class="intro">
            <h1></h1>
            <p></p>
        </section>

        <section class="banner">
            <h2>Mamã e bebé</h2>
            <strong>
                Encontre informações e produtos para o seu Bebe.
            </strong>    
    
            <div class="call">
                <a href="contact.php">
                    <img src="img/call.png" alt="call-center">
                </a>
            </div>
        </section>

        <section class="produtos-info">
            <h2>Informações e Produtos Recomendados</h2>
            <div class="grid-container">
                <div class="card">
                    <a href="Oleo_johnson.php">
                        <img src="img/bebe 1.jpeg" alt="Produto 1">
                    </a>
                    <h3 class="nam">Oleo Johnson's baby</h3>
                    <p class="desc">
                        <ul>
                            <li>Hidratação profunda</li>
                            <li>Suavidade e proteção</li>
                            <li>Adequado para peles sensíveis</li>
                        </ul>
                    </p>
                </div>
                <div class="card">
                    <a href="Creme_Johnson.php">
                        <img src="img/bebe 2.webp" alt="Produto 2">
                    </a>
                    <h3>Creme Johnson's Hidratante </h3>
                    <p>
                        <ul>
                            <li>
                                Hidratação prolongada
                            </li>
                            <li>
                                Textura suave e de fácil absorção
                            </li>
                            <li>
                                Fórmula hipoalergénica
                            </li>
                        </ul>
                    </p>
                </div>
                <div class="card">
                    <a href="Corine_Farme.php">
                        <img src="img/bebe 3.jpg" alt="Produto 3">
                    </a>
                    <h3>Corine De Farme - Baby</h3>
                    <p>
                        <ul>
                            <li>Perfume delicado</li>
                            <li>Ideal para peles sensíveis</li>
                            <li>Sem parabenos e sem álcool</li>
                        </ul>
                        
                    </p>
                </div>
                <div class="card">
                <a href="ATL_Babu.php">
                        <img src="img/bebe 4.png" alt="Produto 4">
                </a>
                    <h3>ATL Baby - Muda de Fralda</h3>
                    <p>
                        <ul>
                            <li>Proteção contra assaduras </li>
                            <li>Textura leve e fácil de espalhar</li> 
                            <li>Adequado para peles sensíveis</li>
                            <li>Ação calmante</li>
                        </ul>
                    </p>
                </div>
                <div class="card">
                    <a href="Fralda.php">
                        <img src="img/fralda.png" alt="Produto 4">
                    </a>
                    <h3>Fralda Pampers Super Pants Tamanho G Com 30 Unidades</h3>
                    <p>
                        <ul>
                            <li>Veste como shortinho</li>
                            <li>Conta com cintura elástica de 360°</li> 
                            <li>Camadas de gel super absorvente</li>
                            <li>conforto e proteção para o bebê por até 12 horas</li>
                        </ul>
                    </p>
                </div>
            </div>
        </section>

        <section class="videos">
            <h2>Vídeos Recomendados</h2>
            <div class="video-container">
                <video controls>
                    <source  src="vds/Mom_impactos.mp4" type="video/mp4">
                    Seu navegador não suporta vídeos.
                </video>
                <video controls>
                    <source src="vds/Mom_saude.mp4" type="video/mp4">
                    Seu navegador não suporta vídeos.
                </video>
                <video controls>
                    <source src="vds/MOM_INSEGURANÇAS.mp4" type="video/mp4">
                    Seu navegador não suporta vídeos.
                </video>
                
            </div>
        </section>
                   
    </main>

    <!-- Rodapé -->
    <br>
    <footer>
        <p>Copyright Notice © [2024] Pharma FIND. All rights reserved.</p>
        <img src="img/logo.png" alt="logotipo">
        
    </footer>

</body>
</html>
