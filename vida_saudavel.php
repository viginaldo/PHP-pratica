
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
    <title>Vida saudável - Pharma Find</title>
    <link rel="stylesheet" href="css/vida.css">
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
            <h2>VIDA SAUDAVÁVEL</h2>
            <strong>
                Viva com Equilíbrio, Cultive Saúde e Bem-Estar!
            </strong>    
    
            <div class="call">
                <a href="contact.php">
                    <img src="img/call.png" alt="call-center">
                </a>
            </div>
        </section>

        <div class="container">
            <div class="section">
                <img src="img/alimentacao.png" alt="Alimentação Equilibrada">
                <div>
                    <h2>Alimentação Equilibrada</h2>
                    <p>Uma alimentação balanceada fornece os nutrientes necessários para o corpo. Inclua frutas, vegetais, grãos integrais e proteínas magras para uma saúde ideal.</p>
                </div>
            </div>
    
            <div class="section">
                <img src="img/fisica.png" alt="Atividade Física Regular">
                <div>
                    <h2>Atividade Física Regular</h2>
                    <p>Praticar exercícios fortalece o coração, melhora a circulação e libera endorfinas, ajudando no controle de peso e na redução do estresse.</p>
                </div>
            </div>
    
            <div class="section">
                <img src="img/sono.png" alt="Sono de Qualidade">
                <div>
                    <h2>Sono de Qualidade</h2>
                    <p>O sono permite a recuperação do corpo e da mente. Dormir de 7 a 9 horas por noite ajuda na memória, no sistema imunológico e na concentração.</p>
                </div>
            </div>
    
            <div class="section">
                <img src="img/emocional.png" alt="Saúde Mental e Emocional">
                <div>
                    <h2>Saúde Mental e Emocional</h2>
                    <p>Manter o equilíbrio emocional é essencial. Práticas como meditação, lazer e apoio psicológico são fundamentais para o bem-estar.</p>
                </div>
            </div>
    
            <div class="section">
                <img src="img/habitos.jpeg" alt="Equilíbrio e Moderação">
                <div>
                    <h2>Equilíbrio e Moderação</h2>
                    <p>Encontrar equilíbrio entre hábitos saudáveis e momentos de lazer promove uma vida mais leve e prazerosa. A moderação é a chave.</p>
                </div>
            </div>
    
            <div class="section">
                <img src="img/autocuidar.jpeg" alt="Higiene e Autocuidado">
                <div>
                    <h2>Higiene e Autocuidado</h2>
                    <p>Práticas de higiene, como lavar as mãos e escovar os dentes, previnem doenças e contribuem para o bem-estar geral.</p>
                </div>
            </div>
    
            <div class="section">
                <img src="img/obj.png" alt="Estabelecimento de Objetivos e Propósito">
                <div>
                    <h2>Estabelecimento de Objetivos e Propósito</h2>
                    <p>Ter um propósito na vida aumenta a motivação e o bem-estar. Definir metas e buscar crescimento contínuo dá sentido à vida.</p>
                </div>
            </div>
        </div>
                   
    </main>

    <!-- Rodapé -->
    <br>
    <footer>
        <p>Copyright Notice © [2024] Pharma FIND. All rights reserved.</p>
        <img src="img/logo.png" alt="logotipo">
        
    </footer>

</body>
</html>
