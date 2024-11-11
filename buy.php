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

$medicamentos = [
   
    ' ' => 'Index.php',
    'Vitamina C' => 'vitaminaC.php',
    'Ibuprofen' => 'fitnes.php',
    'Paracetamol' => 'buy.php',
    'Creatina' => 'creatina.php',
    'Perservativo' => 'Condom.php',
    'Latex Condoms' => 'Condom.php',
    'Composto Sexual' => 'composto_sexual.php',
    'Whey Protein' => 'Whey.php',
    'BCAA' => 'bcaa.php',
    'Saude Sexual' => 'Saude_sexual.php',
    'Diposicao Sexual Feminina'=> 'Feminina_Sexual.php',
    'Oleo Johnson' => 'Oleo_johnson.php',
    'ATL Baby' => 'ATL_Babu.php',
    'Corine de Farme' => 'Corine_Farme.php',
    'Creme Johnson' => 'Creme_Johnson.php',
    'Kit Eudora' => 'Kit_Eudora.php',
    'Gel De Banho' => 'Gel.php',
    'Bioderma' => 'Bioderma.php',
    'Anti Manchas' => 'Anti_manchas.php',
    'Fralda Pampers' => 'Fralda.php',
    'Glutamina' => 'Glutamina.php'
];

// Verificar se o parâmetro 'medicamento' está presente na URL
if (isset($_GET['medicamento'])) {
    $medicamento = $_GET['medicamento'];

    // Verificar se o medicamento está no array
    if (array_key_exists($medicamento, $medicamentos)) {
        // Redirecionar para a página correspondente
        header('Location: ' . $medicamentos[$medicamento]);
        exit;
    } else {
        // Se o medicamento não for encontrado, redireciona para a página inicial
        header('Location: Index.php');
        exit;
    }
}

?>

<?php

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
    <title>Shopping</title>
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

    <section>
        <div class="container">
            <section class="banner3">
                <img src="img/shopP.png" alt="Imagem ao lado do formulário">
            </section>
            <div class="container-txt">
                <h2 class="tit">PARACETAL 500g</h2>
                
                <p>
                    Tomar 1 comprimido a cada 6-8 horas, conforme necessidade, para alívio de dores leves a moderadas e febre. <strong>Não exceder</strong> 4 g (8 comprimidos) em 24 horas. Ingerir com água, sem mastigar.
                </p>    
                <br>
                <p class="sec">
                    Uso adulto e pediátrico acima de 12 anos. Consulte um médico se os sintomas persistirem por mais de 3 dias.
                </p>
                <div class="container-card">
                <div class="container-card">
                    <div class="acction">
                        <!-- Redireciona para buy.php com o nome do medicamento e preço -->
                        <a href="payment.php?medicamento=<?php echo urlencode("Paracetamol"); ?>&preco=<?php echo urlencode(300); ?>">
                            <img src="img/buy.png" alt="Imagem ao lado do formulário">
                            <span>Comprar</span>
                        </a>
                    </div>
           

                  <!--  <div class="acction">
                        <img src="img/deliver.png" alt="Imagem ao lado do formulário">
                        <span>Entrega</span>
                    </div> -->
                    <div class="acction">
                        <img src="img/bloc.png" alt="Imagem ao lado do formulário">
                        <span>Localizacao</span>
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
                <img src="img/pcrianca.png" alt="Produto 1">
                <h3 class="nam">Paracetamol para  Criança </h3>
                <p class="desc">
                   indicado para: 
                   <br>
                   <br>
                   <ul>
                        
                        <li>Dor de cabeça</li>
                        <li>Resfriados e gripe</li>
                        <li>Dor nas costas</li>
                        <li>dor de dente</li>
                    </ul>
                </p>
            </div>
            <div class="card">
                <img src="img/sabor.png" alt="Produto 2">
                <h3 class="nam">Com sabor a frutas</h3>
                <p class="desc">
                    <ul>
                        <li>
                            A dose pediátrica de paracetamol varia de 10 a 15 mg/kg/dose, com intervalos de 4-6 horas entre cada administração
                        </li>
                    </ul>
                </p>
            </div>
            <div class="card">
                <img src="img/infantil.png" alt="Produto 2">
                <h3 class="nam">Antibiótico Infantil</h3>
                <p class="desc">
                    
                    Paracetamol ben-u-ron contém paracetamol como substância ativa, que atua aliviando a dor (analgésico) de intensidade ligeira a moderada e diminuindo a febre (antipirético).Paracetamol ben-u-ron está indicado para:<br>
                    <br><ul>
                               
                        <li>Sintomatologia associada a estados gripais e constipações;</li>
                    </ul>
                </p>
            </div>
            <div class="card">
                <img src="img/azevedo.png" alt="Produto 2">
                <h3 class="nam">Vibral</h3>
                <p class="desc">
                    Indicado para o tratamento dos sintomas da tosse irritante e seca (sem secreção). 
                    <br>
                    <br>
                    <ul>
                       <li> Conteúdo: Xarope 120ml</li>
                        <li>Uso oral</li> 
                        <li>Uso adulto e pediátrico acima de 2 anos</li>
                        <li>Fabricante: Abbott</li>
                    </ul>
                </p>
            </div>
        </div>
    </section>
        

</body>
</html>