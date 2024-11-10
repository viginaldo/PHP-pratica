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

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saúde e beleza - Pharma Find</title>
    <link rel="stylesheet" href="css/beleza.css">
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
            <input type="text" placeholder="Pesquise o seu medicamento aqui...">
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

    <!-- Seção Principal -->
    <main>
        <section class="intro">
            <h1></h1>
            <p></p>
        </section>

        <section class="banner">
            <h2>SAÚDE E BELEZA</h2>
            <strong>
                Cuide da sua beleza e da sua familia : ).
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
                    <a href="Kit_Eudora.php">
                        <img src="img/siage.png" alt="Produto 1">
                    </a>
                    <h3>Kit Eudora Siàge Nutri Óleos Poderosos Completo (5 Produtos)</h3>
                    <p class="desc">
                        <ul>
                            <li>Fragrância exclusiva e marcante</li>
                            <li>deixa os cabelos suavemente perfumados o dia todo</li>
                            <li>Contém: Eudora Siàge Nutri Óleos Poderosos - Shampoo 2</li>
                        </ul>
                    </p>
                </div>
                <div class="card">
                    <a href="Gel.php">
                        <img src="img/gel.png" alt="Produto 2">
                    </a>
                    <h3 class="nam">Gel de Banho</h3>
                    <p class="desc">
                        <ul>
                            <li>
                                propriedades cicatrizantes
                            </li>
                            <li>
                                regeneradoras
                            </li>
                            <li>
                                A base surfactante é macia e pode-se dizer que tem uma compatibilidade muito boa com a pele
                            </li>
                        </ul>
                    </p>
                </div>
                <div class="card">
                    <a href="Bioderma.php">
                        <img src="img/bioderma.webp" alt="Produto 3">
                    </a>
                    <h3 class="nam">Bioderma</h3>
                    <p class="desc">
                        <ul>
                            <li>Controle da oleosidade por 8h¹</li>
                            <li>Sensação de pele fresca & limpa aprovada por 90% dos usuários.</li>
                            
                        </ul>
                        
                    </p>
                </div>
                <div class="card">
                    <a href="Anti_manchas.php">
                        <img src="img/cicatriz.webp" alt="Produto 4">
                    </a>
                    <h3 class="nam">Anti-Mnchas</h3>
                    <p class="desc"> 
                        <ul>
                            <li>Ajuda a esfoliar a pele</li>
                            <li>Remove células mortas</li> 
                            <li>Estimulando a renovação celular</li>
                            <li>Estimula a síntese de colágeno</li>
                        </ul>
                    </p>
                </div>
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
