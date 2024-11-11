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
    <link rel="stylesheet" href="css/petshop.css">
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

        <div class="container">
           
            <div class="section2">
                <h2>CUIDADOS COM O SEU PET</h2>
                
                <p><strong>Alimentação Adequada:</strong> Fornecemos ração balanceada e saudável para cães, gatos, aves e outros animais de estimação, com ingredientes que ajudam na longevidade e vitalidade dos pets.</p>
                <br>
                <p><strong>Higiene e Saúde:</strong> Temos produtos de higiene, como shampoos e desinfetantes, e serviços de vacinação e check-ups regulares que garantem a saúde e a segurança dos seus animais.</p>
                <br>
                <p><strong>Atividades Físicas e Brinquedos:</strong> Sabemos que o exercício é essencial para a saúde física e mental do seu pet, então oferecemos uma seleção de brinquedos interativos e resistentes.</p>
                <br>
                <p><strong>Afeto e Bem-Estar:</strong> Animais são membros da família e precisam de carinho. Oferecemos orientação para garantir que o seu pet seja bem ajustado e feliz, com todo o amor e atenção que merecem.</p>
                
            </div>

            
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
    
        </div>

        <section class="produtos-info">
            <h2>Produtos para o Seu Pet</h2>
            <div class="grid-container">
                <div class="card">
                    <a href="Whey.php">
                        <img src="img/whey.png" alt="Whey Protein">
                    </a>
                    <h3 class="nam">Whey Protein Isolado </h3>
                    <p class="desc">
                        <ul>
                            <li>Proteína de rápida absorção para ganho muscular</li>
                            <li>Ideal para recuperação pós-treino</li>
                            <li>Adequado para intolerantes à lactose</li>
                            <li>Sabores variados e baixo teor de gordura</li>
                        </ul>
                    </p>
                </div>
                <div class="card">
                    <a class="produt" href="creatina.php">
                        <img src="img/creatina.png" alt="Creatina">
                    </a>    
                    <h3 class="nam">Creatina Monohidratada</h3>
                    <p class="desc">
                        <ul>
                            <li>Aumenta força e resistência muscular</li>
                            <li>Auxilia no ganho de massa muscular</li>
                            <li>Ótima para treinos de alta intensidade</li>
                            <li>Uso diário para melhores resultados</li>
                        </ul>
                    </p>
                
                </div>
                <div class="card">
                    <a class="produt" href="bcaa.php">
                        <img src="img/bcca.png" alt="BCAA">
                    </a>
                    <h3 class="nam">BCAA 2:1:1 - 60 Cápsulas</h3>
                    <p class="desc">
                        <ul>
                            <li>Previne fadiga muscular em treinos intensos</li>
                            <li>Melhora a recuperação pós-exercício</li>
                            <li>Aminoácidos essenciais para ganho de massa</li>
                            <li>Ideal para uso pré e pós-treino</li>
                        </ul>
                    </p>
                </div>
                <div class="card">
                    <a href="">
                        <img src="img/dogfood.jpg" alt="Ração para Cachorro">
                    </a>
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
         </div>
     </section>
        

        <footer>
            <p>Copyright Notice © [2024] Pet Shop. All rights reserved.</p>
            <img src="img/logo.png" alt="Pet Shop Logo">
        </footer>
    </body>
</html>
