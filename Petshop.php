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

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Shop</title>
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
                    <li><a href="Historico.php">Historic</a></li>
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

        <section class="banner">
            <h2>Pet Shop - Cuide Bem do Seu Amigo!</h2>
            <strong>Produtos e cuidados para o bem-estar do seu pet.</strong>    
        </section>

        <div class="info-background">
            <!-- Seção sobre Pet Shop com background -->
            <div class="info-section">
                <h2>Sobre o Pet Shop</h2>
                <p>Nosso pet shop é especializado em oferecer o que há de melhor para o cuidado e conforto dos animais de estimação. Desde alimentos de alta qualidade até acessórios modernos e seguros, oferecemos uma vasta gama de produtos que atendem às necessidades diárias dos pets. Estamos comprometidos com a saúde, felicidade e bem-estar dos animais, ajudando os donos a proporcionar uma vida plena aos seus companheiros.</p>
            </div>

            <!-- Seção sobre cuidados com os pets com background -->
            <div class="info-section">
                <h2>Cuidados com o Seu Pet</h2>
                <ul>
                    <li><strong>Alimentação Adequada:</strong> Fornecemos ração balanceada e saudável para cães, gatos, aves e outros animais de estimação, com ingredientes que ajudam na longevidade e vitalidade dos pets.</li>
                    <li><strong>Higiene e Saúde:</strong> Temos produtos de higiene, como shampoos e desinfetantes, e serviços de vacinação e check-ups regulares que garantem a saúde e a segurança dos seus animais.</li>
                    <li><strong>Atividades Físicas e Brinquedos:</strong> Sabemos que o exercício é essencial para a saúde física e mental do seu pet, então oferecemos uma seleção de brinquedos interativos e resistentes.</li>
                    <li><strong>Afeto e Bem-Estar:</strong> Animais são membros da família e precisam de carinho. Oferecemos orientação para garantir que o seu pet seja bem ajustado e feliz, com todo o amor e atenção que merecem.</li>
                </ul>
            </div>
        </div>

        <!-- Produtos Recomendados (sem background) -->
        <div class="container">
            <section class="produtos-info">
                <h2>Produtos para o Seu Pet</h2>
                <div class="grid-container">
                    <div class="card">
                        <img src="./dogfood.jpg" alt="Ração para Cachorro">
                        <h3 class="nam">Ração para Cachorro - 15kg</h3>
                        <div class="desc">
                            <ul>
                                <li>Composição balanceada para o seu cachorro</li>
                                <li>Nutrição completa para cães de todas as idades</li>
                                <li>Fortalece o sistema imunológico</li>
                                <li>Ideal para cachorros ativos</li>
                            </ul>
                        </div>
                    </div>
                    <!-- Additional product cards can be added here following the same format -->
                </div>
            </section>
        </div>

        <footer>
            <p>Copyright Notice © [2024] Pet Shop. All rights reserved.</p>
            <img src="img/logo.png" alt="Pet Shop Logo">
        </footer>
    </body>
</html>
