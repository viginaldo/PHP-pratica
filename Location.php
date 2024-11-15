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
    <title>Localização da Farmácia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            padding-top: 90px;
            font-family: Roboto condensed;
        }


        header {
            background-color: #003366;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            position: fixed;
            width: 100%;
            height: 81px;
            top: 0;
            left: 0;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Sombra basica*/
            z-index: 1000; /*cabeçalho sobre o conteúdo */
        }

        header .logo span {
            color: #00ffcc;
        }

        header nav ul {
            list-style: none;
            display: flex;
            gap: 15px;
            padding-top: 50px;
        }

        header nav ul li a {
            font-size: 18px;
            padding: 8px;
            color: #fff;
            text-decoration: none;
        }

        /* Menu principal */
        .menu {
            display: flex;
            list-style: none;
            background-color: #003366;
            padding: 10px;
            color: white;
        }

        .menu li {
            margin-right: 20px;
        }

        .menu-icon img {
            width: 38px;
            cursor: pointer;
            padding-top: 7px;
        }

        .menu-content {
            display: none; 
            position: absolute;
            top: 60px; /* Ajuste conforme necessário */
            right: 0;
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 5px;
        }

        .menu-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .menu-content a:hover {
            background-color: #EDAC26;
        }

        .menu-content.show {
            display: block;
        }
        /* Menu de categorias suspenso */
        .categories-menu {
            display: none; 
            position: fixed;
            top: 82px;
            left: 0;
            width: 100%;
            background-color: #003366;
            padding: 20px 0px;
            justify-content: space-around;
            z-index: 900;
        }

        .category-item, a {
            text-align: center;
            color: white;
            text-decoration: none;
        }

        .category-item img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 5px;
        }



        header .icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }


        header .icons input {
            padding: 5px;
            border-radius: 8px;
            border: 1px  #9b9595;
            width: 300px;
            height: 30px;
        }

        .but {
            padding: 10px 20px;
            background-color: #003366;
            border: none;
            color: #ffffff;
            font-weight: bold;
            cursor: pointer;
            border-radius: 30px;
            text-decoration: none;
        }

        .but:hover {
            background-color: #09233e;
            text-decoration: underline;
        }

        #map {
            height: 500px;
            width: auto;
        }

        h1{
            text-align: center;
        }
        /* Estilo para o botão */
        .back-button {
            position: absolute;
            top: 80%;
            left: 50%;
            transform: translate(-60%, -50%);
            padding: 10px 20px;
            font-size: 16px;
            background-color: #003366;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Estilo para quando o botão é pressionado */
        .back-button:hover {
            background-color: #09233e;
        }

    </style>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
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


    <h1>Localização da Farmácia</h1>
    <div id="map">
        
    </div>
    <button type="button" class="back-button" onclick="window.history.back();">Voltar</button>
    <script>
        function initMap() {
            // Coordenadas da farmácia (substitua pelas coordenadas da farmácia desejada)
            const farmaciaLocation = { lat: -25.965, lng: 32.583 }; 

            // Criação do mapa centralizado na farmácia
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: farmaciaLocation,
            });

            // Marcador para a farmácia
            const farmaciaMarker = new google.maps.Marker({
                position: farmaciaLocation,
                map: map,
                title: "Farmácia",
            });

            // Localização do usuário
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        // Marcador para o usuário
                        const userMarker = new google.maps.Marker({
                            position: userLocation,
                            map: map,
                            title: "Sua Localização",
                            icon: {
                                url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                            }
                        });

                        // Centralizar o mapa entre os dois pontos
                        map.setCenter(userLocation);

                        // Traçar a rota entre o usuário e a farmácia
                        const directionsService = new google.maps.DirectionsService();
                        const directionsRenderer = new google.maps.DirectionsRenderer();
                        directionsRenderer.setMap(map);

                        const request = {
                            origin: userLocation,
                            destination: farmaciaLocation,
                            travelMode: google.maps.TravelMode.DRIVING,
                        };

                        directionsService.route(request, (result, status) => {
                            if (status == google.maps.DirectionsStatus.OK) {
                                directionsRenderer.setDirections(result);
                            } else {
                                alert("Não foi possível calcular a rota: " + status);
                            }
                        });
                    },
                    () => {
                        alert("Erro ao obter localização do usuário.");
                    }
                );
            } else {
                alert("Geolocalização não é suportada pelo seu navegador.");
            }
        }

        // Iniciar o mapa
        window.onload = initMap;
    </script>
    
</body>
</html>
