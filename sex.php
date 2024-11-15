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
    <title>Saúde Sexual - Pharma Find</title>
    <link rel="stylesheet" href="css/sex.css">
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

    <!-- Seção Principal -->
    <main>
        <section class="banner">
            <h2>Saúde Sexual</h2>
            <strong>
                Encontre informações e produtos para uma vida sexual saudável e segura.
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
                    <a href='Saude_sexual.php'>
                        <img src="img/sex1.png" alt="Produto 1">
                    </a>
                    <h3 class="nam">Saúde Sexual para Homens c/ 30 doses</h3>
                    <p class="desc">
                        <ul>
                            <li>Aumentar a potência sexual</li>
                            <li>aAmentar o desejo sexual</li>
                            <li>Disfunção erétil</li>
                            <li>Retardar ejaculação.</li>
                        </ul>
                    </p>
                </div>
                <div class="card">
                    <a href="Condom.php">
                        <img src="img/cond.png" alt="Produto 2">
                    </a>
                    <h3 class="nam">Latex Condoms-24 Count</h3>
                    <p class="desc">
                        <ul>
                            <li>
                                Estimulação extra é igual a prazer máximo
                            </li>
                            <li>
                                Lubrificado para conforto de deslizamento e sensação natural aprimorada
                            </li>
                            <li>
                                Inclui Lunamax Elegante Bolsa de Viagem/Bolso de Latão Discretamente Segura e Protege Dois Preservativos
                            </li>
                        </ul>
                    </p>
                </div>
                <div class="card">
                    <a href="composto_sexual.php">
                        <img src="img/sex2.png" alt="Produto 2">
                    </a>
                    <h3 class="nam">Composto Saúde Sexual - 30caps</h3>
                    <p class="desc">
                        <ul>
                            <li>Estimulante Sexual (indicação principal);</li>
                            <li>Disfunção erétil;</li>
                            <li>Ação vasodilatadora</li>
                            <li>Aumento da libído</li>
                            <li>Aumento da performance</li>
                        </ul>
                    </p>
                </div>
                <div class="card">
                    <a href="Feminina_Sexual.php">
                        <img src="img/sex3.png" alt="Produto 2">
                    </a>
                    <h3 class="nam">Disposição Sexual Feminina Sex Femme - 60 Comprimidos</h3>
                    <p class="desc">
                        <ul>
                            <li>Contribui para ganho de massa muscular; </li>
                            <li>Melhora o desempenho sexual;</li> 
                            <li>Colabora para melhores ereções;</li>
                            <li>Aumento da libido;</li>
                            <li>Melhora os níveis de energia e vitalidade.</li>
                        </ul>
                    </p>
                </div>
            </div>
        </section>

        <!-- Seção de Vídeos Informativos -->
        <section class="videos">
            <h2>Vídeos Educativos</h2>
            <div class="video-container">
                <video controls>
                    <source  src="vds/vds3.mp4" type="video/mp4">
                    Seu navegador não suporta vídeos.
                </video>
                <video controls>
                    <source src="vds/vds1.mp4" type="video/mp4">
                    Seu navegador não suporta vídeos.
                </video>
                <video controls>
                    <source src="vds/vds2.mp4" type="video/mp4">
                    Seu navegador não suporta vídeos.
                </video>
                <!-- Adicione mais vídeos conforme necessário -->
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
