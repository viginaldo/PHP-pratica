<?php
session_start();
include 'conexao.php'; // Conexão com o banco de dados

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

$data_filtro = isset($_GET['data_venda']) ? $_GET['data_venda'] : '';
$medicamento_filtro = isset($_GET['medicamento']) ? $_GET['medicamento'] : '';
$venda_id_filtro = isset($_GET['venda_id']) ? $_GET['venda_id'] : '';

// Inicia a parte básica da consulta SQL
$sql = "
    SELECT 
        v.id AS venda_id,
        v.data_venda AS data,
        f.nome AS farmacia,
        m.nome AS produto,
        v.quantidade,
        v.preco AS preco,
        v.total AS total,
        v.m_pagamento,
        v.entrega,
        v.estado,
        CONCAT(UPPER(SUBSTRING(m.nome, 1, 1)), LPAD(v.id, 6, '0'),'P') AS Rubrica
    FROM 
        vendas v
    JOIN 
        farmacias f ON v.farmacia_id = f.id
    JOIN 
        medicamentos m ON v.medicamento_id = m.id
    WHERE 
        v.us_id = '$user_id' AND v.estado = 0 AND v.entrega = 1
";

if (isset($_POST['confirmar_entrega'])) {
    $venda_id = $_POST['venda_id']; // Corrigido para obter o ID da venda correto
    $novo_estado = 1;

    $query = "UPDATE vendas SET estado = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $novo_estado, $venda_id);
    $stmt->execute();
    
    // Redireciona para evitar o reenvio do formulário ao recarregar a página
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Aplica o filtro de data, se fornecido
if ($data_filtro !== '') {
    // Verifica se a data está no formato correto 'YYYY-MM-DD'
    $sql .= " AND DATE(v.data_venda) = '$data_filtro'";
}

// Aplica o filtro de medicamento, se fornecido
if ($medicamento_filtro !== '') {
    $sql .= " AND m.nome LIKE '%$medicamento_filtro%'";
}

// Aplica o filtro de ID da venda, se fornecido
if ($venda_id_filtro !== '') {
    $sql .= " AND v.id = '$venda_id_filtro'";
}

if (isset($_POST['confirmar_entrega'])) {
    $venda_id = $_POST['v.id'];
    $novo_estado = 1;

    $query = "UPDATE vendas SET estado = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $novo_estado, $venda_id);
    $stmt->execute();
}

// Ordena os resultados pela data de venda
$sql .= " ORDER BY v.data_venda DESC";

// Executa a consulta
$result = mysqli_query($con, $sql);

// Verifica se houve erro na execução da consulta
if (!$result) {
    die("Erro na consulta: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregas</title>
    <link rel="stylesheet" href="css/historic.css">

    <style>
        
        .form-pesquisa {
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content:right;
            gap: 10px;
            font-family: Roboto condensed;
            font-weight: normal; 

        }

        .label-name{
            padding-top: 10px;
        }

        
        .input-pesquisa {
            padding: 8px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ddd;
            width: 200px; 
        }

        
        .btn-pesquisa {
            padding: 10px 20px;
            background-color: #003366;
            color: white;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .btn-pesquisa:hover {
            background-color: #002244;
        }
    </style>
</head>
<body>
    <!-- Header -->
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
            <a href="#"><img src="img/user.png" height="50px" alt="User Icon"></a>
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

    <h1 style="text-align: left; font-size: 17px; padding-left:30px;padding-top: 20px; padding-bottom: 20px; color:#003366; text-decoration: underline">ENTREGAS PENDENTES > > ></h1>
    <b><b>
    <form action="entrega.php" method="GET" class="form-pesquisa">
        <label class="label-name">Data da Compra:</label>
        <input type="date" name="data_venda" id="data_venda" value="<?php echo isset($_GET['data_venda']) ? $_GET['data_venda'] : ''; ?>" class="input-pesquisa">

        <label class="label-name">Codigo </label>
         <input type="text" name="venda_id" id="venda_id" value="<?php echo isset($_GET['venda_id']) ? $_GET['venda_id'] : ''; ?>" class="input-pesquisa" placeholder="Nº Compra" style="width: 90px">

        
        <label class="label-name">Produto:</label>
        <input type="text" name="medicamento" id="medicamento" value="<?php echo isset($_GET['medicamento']) ? $_GET['medicamento'] : ''; ?>" placeholder="Nome do medicamento" class="input-pesquisa" style="width: 170px;">

        <button type="submit" class="btn-pesquisa">Pesquisar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Rubrica</th>
                <th>Data da Compra</th>
                <th>Farmácia</th>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço</th>
                <th>Total</th>
                <th>Entrega</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['Rubrica']; ?></td>
                        <td><?php echo $row['data']; ?></td>
                        <td><?php echo $row['farmacia']; ?></td>
                        <td><?php echo $row['produto']; ?></td>
                        <td><?php echo $row['quantidade']; ?></td>
                        <td><?php echo number_format($row['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo number_format($row['total'], 2, ',', '.'); ?></td>
                        <td><?php echo $row['entrega'] == 1 ? 'Sim' : 'Nao'; ?></td>
                        <td><?php echo $row['estado'] == 1 ? 'Recebido' : 'Pendente'; ?></td>
                        <td>
                            <?php if ($row['estado'] == 0): // Mostra o botão apenas se o estado for "Pendente" ?>
                                <form method="post" action="">
                                    <input type="hidden" name="venda_id" value="<?php echo $row['venda_id']; ?>">
                                    <button type="submit" class="btn-pesquisa" name="confirmar_entrega">Confirmar<br> Entrega</button>
                                </form>
                            <?php else: ?>
                                <span>Confirmado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">Nenhuma Entrega Pendente.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <footer>
        <p class="copyright">Copyright Notice © [2024] Pharma FIND. All rights reserved.</p>
        <img src="img/logo.png" alt="logotipo">
    </footer>
</body>

</html>

<?php
mysqli_close($con);
?>
   