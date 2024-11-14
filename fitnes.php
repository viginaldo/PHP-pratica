
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
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vida Fitness</title>
   
    <link rel="stylesheet" href="css/fitnes.css">
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
                <li><a href="geral.php">Lista Geral</a></li>
                <li><a href="about.php">Sobre nos</a></li>
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
        <h2>VIDA FITNESS</h2>
        <strong>
            Transforme sua saúde e bem-estar com consistência e determinação!
        </strong>    

        <div class="call">
            <a href="contact.php">
                <img src="img/call.png" alt="call-center">
            </a>
        </div>
    </section>

    <div class="container">
        <!-- Seção sobre Vida Fitness -->
        <div class="section">
            <h2>O que é a Vida Fitness?</h2>
            <p>A vida fitness é um estilo de vida focado na saúde e na boa forma física, englobando exercícios regulares, uma alimentação balanceada e atenção ao bem-estar físico e mental. Ela envolve práticas que fortalecem o corpo, aumentam a resistência e promovem uma vida ativa e equilibrada.</p>
        </div>

        <!-- Seção sobre os aspectos da Vida Fitness -->
        <div class="section">
            <h2>Aspectos da Vida Fitness</h2>
            <p><strong>Treinamento Físico Regular:</strong> Exercícios aeróbicos, treinamento de força e alongamentos são fundamentais para o desenvolvimento e a manutenção da saúde física.</p>
            <p><strong>Alimentação Balanceada e Planejada:</strong> Comer de forma saudável, com proteínas, carboidratos e gorduras saudáveis, auxilia na recuperação muscular e mantém a energia.</p>
            <p><strong>Descanso e Recuperação:</strong> Dormir adequadamente e permitir a recuperação muscular são essenciais para evitar lesões e melhorar o desempenho.</p>
            <p><strong>Estilo de Vida Ativo:</strong> Incorporar atividades físicas no dia a dia, como caminhar e usar escadas, é parte de um estilo de vida ativo e fitness.</p>
            <p><strong>Saúde Mental:</strong> Exercícios físicos também promovem o bem-estar mental, aliviando o estresse e melhorando o humor.</p>
            <p><strong>Disciplina e Consistência:</strong> O sucesso na vida fitness requer uma rotina disciplinada e a prática constante de hábitos saudáveis.</p>
        </div>

        <!-- Recomendações para Ganho de Massa Muscular -->
        <div class="section">
            <h2 class="recomend">Recomendações para Ganho de Massa Muscular</h2>
            <div class="recommendations">
                <div class="recommendation">
                    <img src="img/resisencia.png" alt="Treinamento de Resistência">
                    <h3>Treinamento de Resistência</h3>
                    <p>Exercícios com pesos ajudam a aumentar a massa muscular e melhorar a força, sendo essenciais para quem deseja hipertrofia.</p>
                </div>
                <div class="recommendation">
                    <img src="img/proteina.png" alt="Alimentação Proteica">
                    <h3>Alimentação Rica em Proteínas</h3>
                    <p>Consumir proteínas de alta qualidade, como carnes magras, ovos e leguminosas, auxilia na reparação e crescimento muscular.</p>
                </div>
                <div class="recommendation">
                    <img src="img/descanso.png" alt="Descanso e Recuperação">
                    <h3>Descanso e Recuperação</h3>
                    <p>O descanso entre os treinos permite a recuperação muscular, fundamental para o ganho de massa e prevenção de lesões.</p>
                </div>                
                <div class="recommendation">
                    <img src="img/treino.png" alt="Planejamento de Treino">
                    <h3>Planejamento de Treino</h3>
                    <p>Organizar um plano de treino que progride em intensidade ajuda a alcançar ganhos consistentes e minimizar estagnações.</p>
                </div>
                <div class="recommendation">
                    <img src="img/hidratacao.png" alt="Hidratação Adequada">
                    <h3>Hidratação Adequada</h3>
                    <p>Manter-se hidratado é vital para o desempenho muscular e para otimizar o transporte de nutrientes pelo corpo.</p>
                </div>
            </div>
        </div>
         
        <section class="produtos-info">
        <h2>Suplementos e Produtos Recomendados</h2>
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
                <a href="Glutamina.php">
                    <img src="img/glutamini.png" alt="Glutamina">
                </a>
                <h3 class="nam">Glutamina Pura - 300g</h3>
                <p class="desc">
                    <ul>
                        <li>Auxilia na recuperação muscular</li>
                        <li>Fortalece o sistema imunológico</li> 
                        <li>Ótimo para treinos intensos</li>
                        <li>Uso recomendado após o treino e antes de dormir</li>
                    </ul>
                </p>
            </div>
        </div>
    </section>
    </div>
    

    <br>
    <footer>
        <p>Copyright Notice © [2024] Pharma FIND. All rights reserved.</p>
        <img src="img/logo.png" alt="logotipo">
        
    </footer>
</body>
</html>
