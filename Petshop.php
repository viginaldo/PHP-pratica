<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Shop</title>
    <style>
        /* Inline CSS for consistent styling */

        /* Full-width background for info sections */
        .info-background {
            background-image: url('./about.jpg'); /* Update this to your preferred background image */
            background-size: cover;
            background-position: center;
            padding: 50px 0;
            color: #fff;
        }

        .info-section {
            width: 80%;
            margin: auto;
            background: rgba(0, 0, 0, 0.6); /* Semi-transparent background for readability */
            padding: 25px;
            border-radius: 8px;
        }

        .info-section h2 {
            color: #ffdd57;
        }

        .info-section p, .info-section ul {
            color: #f2f2f2;
        }

        /* Styling for the banner and container */
        .banner {
            text-align: center;
            padding: 20px;
            background: #f5f5f5;
        }

        .container {
            width: 80%;
            margin: auto;
        }

        /* Product styling without background */
        .produtos-info {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .produtos-info h2 {
            color: #333;
        }

        .grid-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            width: calc(33.333% - 20px);
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background-color: #fafafa;
        }

        .card img {
            width: 100%;
            height: auto;
        }

        .card .nam {
            color: #333;
            font-weight: bold;
            padding: 10px;
        }

        .card .desc {
            padding: 10px;
        }

        /* Additional styles for footer */
        footer {
            text-align: center;
            padding: 15px;
            background: #333;
            color: #fff;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="img/logoname.png" height="70px" alt="PetShop Logo">
    </div>
    <nav>
        <ul>
            <li><a href="Index.php">Home</a></li>
            <li><a href="#" onclick="toggleCategories();">Category</a></li>
            <li><a href="#">Favorite</a></li>
            <li><a href="about.php">About us</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>
    <div class="icons">
        <form method="GET" action="">
            <input type="text" name="produto" placeholder="Pesquise o seu produto aqui..." required>
            <button class="but" type="submit">Pesquisar</button>
        </form>
        <a href="#"><img src="img/user.png" height="50px" alt="User Icon"></a>
        <a href="Login.php"><img src="img/logout.png" height="50px" alt="Cart Icon"></a>
    </div>
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
