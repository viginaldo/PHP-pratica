<?php
session_start();
include 'conexao.php'; // Conexão com o banco de dados

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Obter filtros e sanitizar inputs
$data_filtro = isset($_GET['data_venda']) ? mysqli_real_escape_string($con, $_GET['data_venda']) : '';
$medicamento_filtro = isset($_GET['medicamento']) ? mysqli_real_escape_string($con, $_GET['medicamento']) : '';
$venda_id_filtro = isset($_GET['venda_id']) ? (int)$_GET['venda_id'] : '';
$estado_filtro = isset($_GET['estado']) ? mysqli_real_escape_string($con, $_GET['estado']) : '';
$entrega_filtro = isset($_GET['entrega']) ? mysqli_real_escape_string($con, $_GET['entrega']) : '';

// Paginacao
$rows_per_page = isset($_GET['rows_per_page']) ? (int)$_GET['rows_per_page'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $rows_per_page;

// Construir consulta SQL inicial
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
        v.us_id = ? 
";

// Adicionar filtros dinamicamente
if ($estado_filtro === 'Recebido') {
    $sql .= " AND v.estado = 1";
} elseif ($estado_filtro === 'Pendente') {
    $sql .= " AND v.estado = 0";
}

if ($entrega_filtro === 'Sim') {
    $sql .= " AND v.entrega = 1";
} elseif ($entrega_filtro === 'Nao') {
    $sql .= " AND v.entrega = 0";
}

if (!empty($data_filtro)) {
    $sql .= " AND DATE(v.data_venda) = ?";
}

if (!empty($medicamento_filtro)) {
    $sql .= " AND m.nome LIKE ?";
}

if (!empty($venda_id_filtro)) {
    $sql .= " AND v.id = ?";
}

// Paginacao: Limitar resultados
$sql .= " ORDER BY v.data_venda DESC LIMIT ?, ?";

// Preparar consulta
$stmt = $con->prepare($sql);

// Adicionar parâmetros dinamicamente
$param_types = "i";
$param_values = [$user_id];

if (!empty($data_filtro)) {
    $param_types .= "s";
    $param_values[] = $data_filtro;
}

if (!empty($medicamento_filtro)) {
    $param_types .= "s";
    $param_values[] = "%$medicamento_filtro%";
}

if (!empty($venda_id_filtro)) {
    $param_types .= "i";
    $param_values[] = $venda_id_filtro;
}

// Adicionar parâmetros para paginação
$param_types .= "ii";
$param_values[] = $start_from;
$param_values[] = $rows_per_page;

// Associar parâmetros
$stmt->bind_param($param_types, ...$param_values);

// Executar consulta
$stmt->execute();
$result = $stmt->get_result();

// Contar o número total de registros para a paginação
$total_sql = "SELECT COUNT(*) FROM vendas v WHERE v.us_id = ?";
$total_stmt = $con->prepare($total_sql);
$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $rows_per_page);

// Verificar erros
if (!$result) {
    die("Erro na consulta: " . $stmt->error);
}
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaFind</title>
    <link rel="stylesheet" href="css/historic.css">

    <style>
        
        .form-pesquisa {
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content:right;
            gap: 20px;
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
        .pagination {
            text-align: center;
            margin-top: 20px;
            padding-bottom: 40px;
        }

        .pagination a {
            padding: 8px 16px;
            margin: 0 5px;
            text-decoration: none;
            color: #ffffff;
            background-color: #003366;
            border: 1px solid #f4f4ff;
            border-radius: 4px;
            font-size: 14px;
            font-weight: normal;
        }

        .pagination a:hover {
            background-color: #002244;
            color: white;
        }

        .pagination .disabled {
            color: #ccc;
            cursor: not-allowed;
            border: 1px solid #ccc;
        }

        .pagination span {
            font-size: 14px;
            margin: 0 10px;
            color: #555;
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
                <li><a href="entrega.php">Entregas</a></li>
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

    <h1>Histórico de Compras</h1>
    <b><b>
    <form action="Historico.php" method="GET" class="form-pesquisa">

        <select name="rows_per_page" id="rows_per_page" class="input-pesquisa" style="width: 70px;">
            <option value="10" <?php echo isset($_GET['rows_per_page']) && $_GET['rows_per_page'] == 10 ? 'selected' : ''; ?>>10</option>
            <option value="20" <?php echo isset($_GET['rows_per_page']) && $_GET['rows_per_page'] == 20 ? 'selected' : ''; ?>>20</option>
            <option value="50" <?php echo isset($_GET['rows_per_page']) && $_GET['rows_per_page'] == 50 ? 'selected' : ''; ?>>50</option>
            <option value="100" <?php echo isset($_GET['rows_per_page']) && $_GET['rows_per_page'] == 100 ? 'selected' : ''; ?>>100</option>
            <option value="150" <?php echo isset($_GET['rows_per_page']) && $_GET['rows_per_page'] == 150 ? 'selected' : ''; ?>>150</option>
        </select>

        <label class="label-name">Data da Compra:</label>
        <input type="date" name="data_venda" id="data_venda" value="<?php echo isset($_GET['data_venda']) ? $_GET['data_venda'] : ''; ?>" class="input-pesquisa" style="width: 130px;">

        <label class="label-name">Codigo </label>
         <input type="text" name="venda_id" id="venda_id" value="<?php echo isset($_GET['venda_id']) ? $_GET['venda_id'] : ''; ?>" class="input-pesquisa" placeholder="Nº Compra" style="width: 90px">

        
        <label class="label-name">Produto:</label>
        <input type="text" name="medicamento" id="medicamento" value="<?php echo isset($_GET['medicamento']) ? $_GET['medicamento'] : ''; ?>" placeholder="Nome do medicamento" class="input-pesquisa" style="width: 170px;">

        <select name="entrega" class="input-pesquisa" style="width: 100px;">

            <option value="">Entrega</option>

            <option value="Sim" <?php echo isset($_GET['entrega']) && $_GET['entrega'] == 'Sim' ? 'selected' : ''; ?>>Sim</option>

            <option value="Nao" <?php echo isset($_GET['entrega']) && $_GET['entrega'] == 'Nao' ? 'selected' : ''; ?>>Nao</option>

        </select>

        <select name="estado" class="input-pesquisa" style="width: 100px;">

            <option value="">Estado</option>

            <option value="Recebido" <?php echo isset($_GET['estado']) && $_GET['estado'] == 'Recebido' ? 'selected' : ''; ?>>Recebido</option>

            <option value="Pendente" <?php echo isset($_GET['estado']) && $_GET['estado'] == 'Pendente' ? 'selected' : ''; ?>>Pendente</option>

        </select>

        <button type="submit" class="btn-pesquisa">Pesquisar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Data da Compra</th>
                <th>Farmácia</th>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço</th>
                <th>Total</th>
                <th>Método de Pagamento</th>
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
                        <td><?php echo $row['m_pagamento']; ?></td>
                        <td><?php echo $row['entrega'] == 1 ? 'Sim' : 'Nao'; ?></td>
                        <td>
                        <?php if ($row['estado'] == 1): ?>
                            Recebido
                        <?php else: ?>
                            <a href="entrega.php?venda_id=<?php echo $row['venda_id']; ?>" style="color: red; text-decoration: underline;">
                                Pendente
                            </a>
                        <?php endif; ?>
                    </td>
                        <td>
                        <a href="Comprovativo.php?venda_id=<?php echo $row['venda_id']; ?>">
                            <button>Baixar</button>
                        </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">Nenhuma compra encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <div class="btn-container" style="text-align: right; padding-right: 10px; padding-bottom: 15px">
        <a href="Extrato.php" style="text-decoration: none;">
            <button 
                style="background-color: #003366; color: white; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer; border-radius: 5px; transition: background-color 0.3s;"
                onmouseover="this.style.backgroundColor='#002244';"
                onmouseout="this.style.backgroundColor='#003366';">
                Extrato
            </button>
        </a>

    </div>

    <div class="pagination">
        <?php
        $pages_to_show = 5;
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);

        if ($page <= 3) {
            $start_page = 1;
            $end_page = min($total_pages, $pages_to_show);
        } elseif ($page >= $total_pages - 2) {
            $start_page = max(1, $total_pages - $pages_to_show + 1);
            $end_page = $total_pages;
        }

        // Link para a página anterior
        if ($page > 1) {
            echo '<a href="?page=' . ($page - 1) .'&rows_per_page=' . urlencode($rows_per_page). '&medicamento=' . urlencode($medicamento_filtro) . '&estado=' . urlencode($estado_filtro) . '&entrega=' . urlencode($entrega_filtro) . '">Anterior</a> ';
        }

        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $page) {
                echo '<span class="active">' . $i . '</span> ';
            } else {
                echo '<a href="?page=' . $i .'&rows_per_page=' . urlencode($rows_per_page). '&medicamento=' . urlencode($medicamento_filtro) . '&estado=' . urlencode($estado_filtro) . '&entrega=' . urlencode($entrega_filtro) . '">' . $i . '</a> ';
            }
        }

        if ($end_page < $total_pages) {
            echo '... ';
        }

        // Link para a próxima página
        if ($page < $total_pages) {
            echo '<a href="?page=' . ($page + 1) .'&rows_per_page=' . urlencode($rows_per_page). '&medicamento=' . urlencode($medicamento_filtro) . '&estado=' . urlencode($estado_filtro) . '&entrega=' . urlencode($entrega_filtro) . '">Próxima</a>';
        }
        ?>
    </div>

    <!-- Footer -->
    <footer>
        <p>Copyright Notice © [2024] Pharma FIND. All rights reserved.</p>
        <img src="img/logo.png" alt="logotipo">
    </footer>
</body>
</html>

<?php
mysqli_close($con);
?>
   