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


if (isset($_GET['medicamento']) && isset($_GET['preco'])) {
    $medicamento = $_GET['medicamento'];
    $preco = $_GET['preco'];

    $sql = "SELECT farmacias.nome AS farmacia_nome 
            FROM medicamentos 
            INNER JOIN farmacias ON medicamentos.farmacia_id = farmacias.id 
            WHERE medicamentos.nome = ?";

    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $medicamento);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $farmaciaNome = $row['farmacia_nome'];
    }
} else {
    
    header("Location: Index.php");
    exit;
}
?>

<?php
if (isset($_GET['medicamento']) && isset($_GET['preco'])) {
    $medicamento = $_GET['medicamento'];
    $preco = $_GET['preco'];

    $sql = "SELECT farmacias.nome AS farmacia_nome, medicamentos.quant AS quantidade 
            FROM medicamentos 
            INNER JOIN farmacias ON medicamentos.farmacia_id = farmacias.id 
            WHERE medicamentos.nome = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $medicamento); 
    $stmt->execute();

    $result = $stmt->get_result();

    $farmacias = [];

    if ($result->num_rows > 0) {
        $encontrou = false; 
       
        while ($row = $result->fetch_assoc()) {
            if ($row['quantidade'] > 0) {
                $farmacias[] = htmlspecialchars($row['farmacia_nome'] . " com " . $row['quantidade'] . ' Unid.');
                $encontrou = true; 
            }
        }
    
        if (!$encontrou) {
            echo "
            <script>
                alert('Nenhuma farmácia tem " . addslashes($medicamento) . "');
                window.location.href = 'Index.php';
            </script>";
        
        } else {
            $farmaciasList = "<br>" . implode("<br>", $farmacias);
        }
    }
    $farmaciasList = "<br>" . implode("<br>", $farmacias);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaFind - Pagamento</title>
    <style>
       * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            align-items: center;
            justify-content: center;
        }

        body {
            padding-top: 81px;
            font-family: Roboto condensed;
            justify-content: center;
            display: flex;
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

        .container {
            background-color: #ffffff;
            width: 100%;
            max-width: 500px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
            justify-content: center;
        }

        .title {
            font-size: 26px;
            color: #2D4D76;
            margin-bottom: 20px;
        }

        .payment-method,
        .delivery-option {
            margin: 20px 0;
            text-align: left;
        }

        .payment-method label,
        .delivery-option label {
            font-size: 18px;
            color: #2D4D76;
            cursor: pointer;
            display: block;
            padding-left: 30px;
            position: relative;
        }

        .payment-method input,
        .delivery-option input {
            position: absolute;
            left: 0;
            top: 0;
            cursor: pointer;
        }

        .total-cost {
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            color: #2D4D76;
        }

        .submit-button {
            background-color: #003366;
            color: #ffffff;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }

        .submit-button:hover {
            background-color: #09233e;
        }

        .submit-button:active {
            background-color: #003366;
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

        .product-info{
            font-size: 18px;
        }

        .farmacias{
           padding-left: 55px;
            text-align: justify;
            padding-bottom: 10px;
        }

        .product-info a{
            color: black;
            font-size: 18px;
            text-decoration:none;
        }

        .product-info a:hover{
            color: blue;
            text-decoration:underline;
        }

        .location-field label {
            color: #2D4D76;
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
            text-align: left;
        }

        .location-field input[type="text"],input[type="number"]{
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #2D4D76;
            border-radius: 5px;
            font-size: 16px;
            font-family: Roboto condensed;
            box-sizing: border-box;
            transition: border 0.3s ease;
        }

        .location-field input[type="text"]:focus,input[type="number"]:focus{
            border-color: #F37126;
        }
        .farmacias{
            color: #003366;
            text-decoration: underline;
        }

        /* Estilo do rótulo */
        .combo-label {
            color: #2D4D76;
            font-size: 18px;
            margin-bottom: 5px;
            display: inline-block;
        }

        /* Estilo do combobox */
        .combo-select {
            width: 100px;
            padding: 10px;
            font-size: 14px;
            font-family: Roboto Condensed;
            border: 2px solid #2D4D76;
            border-radius: 5px;
            appearance: none;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        /* Seta personalizada para o combobox */
        .combo-select:focus {
            border-color: #F37126;
            outline: none;
        }

        /* Estilo para a seta personalizada */
        .select-wrapper {
            text-align: left;
            position: relative;
            padding-left: 29px;
            display: inline-block;
            width: 100%;
        }

        .select-wrapper::after {
            font-size: 12px;
            color: #333;
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            pointer-events: none;
        }

    </style>
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

    <div class="container">
        <h1 class="title">Selecione o Método de Pagamento</h1>

        <!-- Exibição do medicamento e preço -->
        <div class="product-info">
            <strong>
                <!--<p> Medicamento:
                    <?php //echo htmlspecialchars($medicamento); ?>
                </p>-->
                Farmacias com <?php echo htmlspecialchars($medicamento); ?>
                <p class="farmacias">  
                    <?php echo $farmaciasList ?>
                </p>
                <p>Preço: <?php echo htmlspecialchars($preco); ?> MT</p>
                <br>
                <a href="buy.php?medicamento=<?php echo htmlspecialchars($medicamento);?>">Detalhes</a>
            </strong>
        </div>

        <!-- Métodos de Pagamento -->
        <div class="payment-method">
            <label>
                <input type="radio" name="payment" value="mpesa"> M-Pesa
            </label>
        </div>
        <div class="payment-method">
            <label>
                <input type="radio" name="payment" value="emola"> E-Mola
            </label>
        </div>
        <div class="payment-method">
            <label>
                <input type="radio" name="payment" value="cartao"> Cartão de Crédito/Débito
            </label>
        </div>

        <div class="select-wrapper">
            <label for="numberSelect" class="combo-label">Quantidade:</label>
            <select id="numberSelect" name="numberSelect" class="combo-select" onchange="updateTotalCost()" required>
                <option value=""  disabled selected>Quantidade</option>
                <?php
                for ($i = 1; $i <= 10; $i++) {
                    echo "<option value='$i'>$i</option>";
                }
                ?>
            </select>
        </div>


        <!-- Opção de Entrega -->
        <div class="delivery-option">
            <label>
                <input type="checkbox" id="deliveryCheckbox" onchange="toggleDelivery()"> Desejo Entrega (100 MT adicional)
            </label>
        </div>

        <!-- Campo para Localização -->
        <div class="location-field" id="locationField" style="display: none;">
            <label for="locationInput">Localizacao:</label>
            <input type="text" id="locationInput" placeholder="Insira a sua Avenida e o numero da casa" required>
            <label for="locationInput">Contacto:</label>
            <input type="text" id="contactInput" placeholder="Numero de celular" maxlength="9" required>
        </div>

        <!-- Custo Total -->
        <div class="total-cost" id="totalCost">
            Total: <span id="costAmount">0 MT</span>
        </div>

        <!-- Botão de Confirmação -->
        <button class="submit-button" onclick="confirmPayment()">Continuar</button>

        <script>
            let basePrice = <?php echo $preco; ?>;  // Preço base (passado via PHP)
            let deliveryFee = 100;  // Taxa de entrega

            // Função para atualizar o custo total
            function updateTotalCost() {
                const quantidadeField = document.getElementById('numberSelect').value;  
                const deliveryCheckbox = document.getElementById('deliveryCheckbox');
                const costAmount = document.getElementById('totalCost');

                if (quantidadeField > 0) {
                    const totalPrice = basePrice * quantidadeField;

                    //adiciona a taxa de entrega
                    const finalPrice = deliveryCheckbox.checked ? totalPrice + deliveryFee : totalPrice;

                    costAmount.textContent = `${finalPrice} MT`;
                } else {
                
                    costAmount.textContent = `0 MT`;
                }

                locationField.style.display = deliveryCheckbox.checked ? 'block' : 'none';
            }
            function toggleDelivery() {
                updateTotalCost();
            }
            function confirmPayment() {
                const selectedPayment = document.querySelector('input[name="payment"]:checked');
                const deliveryCheckbox = document.getElementById('deliveryCheckbox');
                const locationInput = document.getElementById('locationInput');
                const contactInput = document.getElementById('contactInput');
                const quantidadeSelecionada = document.getElementById('numberSelect') ? document.getElementById('numberSelect').value : 1;
                const entrega = document.getElementById('deliveryCheckbox').checked ? 1: 0;

                if (!selectedPayment) {
                    alert("Selecione um método de pagamento.");
                    return;
                }

                if (deliveryCheckbox.checked && locationInput.value.trim() === "") {
                    alert("Por favor, insira a sua localização.");
                    return;
                }

                if (deliveryCheckbox.checked && contactInput.value.trim() === "") {
                    alert("Por favor, insira o ser contaacto.");
                    return;
                }

                if(!quantidadeSelecionada)
                {
                    alert("Por favor, selecione a quantidade.");
                    return;
                }

                // Direcionar para a página de pagamento com base no método selecionado
                let url = ''; 
                let medicamentoId = "<?php echo htmlspecialchars($medicamento); ?>";

                switch (selectedPayment.value) {
                    case 'mpesa':
                        url = 'mpesa_payment.php?medicamento=' + encodeURIComponent( medicamentoId) + '&quantidade=' + quantidadeSelecionada + '&entrega=' + entrega;
                        break;
                    case 'emola':
                        url = 'emola_payment.php?medicamento=' + encodeURIComponent( medicamentoId) + '&quantidade=' + quantidadeSelecionada + '&entrega=' + entrega;
                        break;
                    case 'cartao':
                        url = 'card_payment.php?medicamento=' + encodeURIComponent( medicamentoId) + '&quantidade=' + quantidadeSelecionada + '&entrega=' + entrega;
                        break;
                }

                window.location.href = url;

            }
        </script>


        

        
</body>
</html>
