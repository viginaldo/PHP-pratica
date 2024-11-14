<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$medicamento_filtro = isset($_GET['medicamento']) ? '%' . $_GET['medicamento'] . '%' : '';
$id_filtro = isset($_GET['id']) ? $_GET['id'] : '';
$tipoSelecionado = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$formaSelecionada = isset($_GET['forma']) ? $_GET['forma'] : '';
$disponibilidadeSelecionada = isset($_GET['disponibilidade']) ? $_GET['disponibilidade'] : '';

// Filtros
$sql = "
    SELECT 
    mg.id AS medicamento_geral_id,
    mg.tipo,
    mg.designacao,
    mg.forma,
    mg.dose,
    mg.instrucao,
    m.preco,
    mg.estado,
    CASE WHEN SUM(m.quant) > 0 THEN 1 ELSE 0 END AS estado_disponibilidade,
    CONCAT(UPPER(SUBSTRING(mg.tipo, 1, 1)), LPAD(mg.id, 6, '0')) AS Rubrica
FROM 
    medicamentos_gerais mg
LEFT JOIN 
    medicamentos m ON mg.designacao = m.nome
WHERE 
    1=1
";

// Parâmetros para a consulta
$params = [];
$types = '';

// Adicionando filtros dinamicamente
if (!empty($tipoSelecionado)) {
    $sql .= " AND mg.tipo = ?";
    $params[] = $tipoSelecionado;
    $types .= 's';
}

if (!empty($medicamento_filtro)) {
    $sql .= " AND mg.designacao LIKE ?";
    $params[] = $medicamento_filtro;
    $types .= 's';
}

if (!empty($id_filtro)) {
    $sql .= " AND mg.id = ?";
    $params[] = $id_filtro;
    $types .= 'i';
}

if (!empty($formaSelecionada)) {
    $sql .= " AND mg.forma = ?";
    $params[] = $formaSelecionada;
    $types .= 's';
}

// Adicionando o GROUP BY
$sql .= " GROUP BY mg.id";

// Se o filtro de disponibilidade for definido, aplicamos a condição no HAVING
if (!empty($disponibilidadeSelecionada)) {
    if ($disponibilidadeSelecionada == 'Disponível') {
        $sql .= " HAVING SUM(m.quant) > 0"; 
    } elseif ($disponibilidadeSelecionada == 'Indisponível') {
        $sql .= " HAVING SUM(m.quant) = 0";
    }
}

// Configurando a paginação
$rows_per_page = isset($_GET['rows_per_page']) ? (int)$_GET['rows_per_page'] : 10; // Padrão de 10 resultados por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Página atual
$offset = ($page - 1) * $rows_per_page; // Calcula o OFFSET

// Adicionando o ORDER BY
$sql .= " ORDER BY mg.tipo ASC";

// Adicionando LIMIT e OFFSET à consulta (após ORDER BY)
$sql .= " LIMIT ?, ?";
$params[] = $offset;
$params[] = $rows_per_page;
$types .= 'ii'; // Bind para os parâmetros LIMIT e OFFSET

// Preparando a consulta
$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die("Erro na consulta: " . mysqli_error($con));
}

// Vincula os parâmetros se houver
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Calcular o total de registros
$total_sql = "SELECT COUNT(*) AS total FROM medicamentos_gerais mg LEFT JOIN medicamentos m ON mg.designacao = m.nome WHERE 1=1";
$total_result = mysqli_query($con, $total_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $rows_per_page); // Total de páginas necessárias

// Fechando a conexão
$stmt->close();
?>





<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Medicamentos Gerais</title>
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


<h1>Medicamentos Gerais</h1>
<b><b>
<form action="geral.php" method="GET" class="form-pesquisa">
    <select name="rows_per_page" id="rows_per_page" class="input-pesquisa" style="width: 70px;">
        <option value="10" <?php echo isset($_GET['rows_per_page']) && $_GET['rows_per_page'] == 10 ? 'selected' : ''; ?>>10</option>
        <option value="20" <?php echo isset($_GET['rows_per_page']) && $_GET['rows_per_page'] == 20 ? 'selected' : ''; ?>>20</option>
        <option value="50" <?php echo isset($_GET['rows_per_page']) && $_GET['rows_per_page'] == 50 ? 'selected' : ''; ?>>50</option>
        <option value="100" <?php echo isset($_GET['rows_per_page']) && $_GET['rows_per_page'] == 100 ? 'selected' : ''; ?>>100</option>
        <option value="150" <?php echo isset($_GET['rows_per_page']) && $_GET['rows_per_page'] == 150 ? 'selected' : ''; ?>>150</option>
    </select>



    <!-- Filtros de pesquisa -->
    <label class="label-name">Designação:</label>
    <input type="text" name="medicamento" value="<?php echo isset($_GET['medicamento']) ? $_GET['medicamento'] : ''; ?>" class="input-pesquisa">

    <label class="label-name">Rubrica:</label>
    <input type="text" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>" placeholder="ID" style="width: 60px;" class="input-pesquisa">

    <select name="tipo" id="tipo" class="input-pesquisa">
        <option value="">Selecione um tipo</option>
        <option value="ANTIBIÓTICOS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'ANTIBIÓTICOS' ? 'selected' : ''; ?>>Antibióticos</option>
        <option value="CARDIOVASULAR" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'CARDIOVASULAR' ? 'selected' : ''; ?>>Cardiovascular</option>
        <option value="DIGESTIVO" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'DIGESTIVO' ? 'selected' : ''; ?>>Digestivo</option>
        <option value="ENDOCRINOLOGIA E METABOLISMO" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'ENDOCRINOLOGIA E METABOLISMO' ? 'selected' : ''; ?>>Endocrinologia e Metabolismo</option>
        <option value="GENITO-URINÁRIO E HORMONAS SEXUAIS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'GENITO-URINÁRIO E HORMONAS SEXUAIS' ? 'selected' : ''; ?>>Genito-Urinário e Hormonas Sexuais</option>
        <option value="RESPIRATÓRIO" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'RESPIRATÓRIO' ? 'selected' : ''; ?>>Respiratório</option>
        <option value="SANGUE" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'SANGUE' ? 'selected' : ''; ?>>Sangue</option>
        <option value="SISTEMA NERVOSO" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'SISTEMA NERVOSO' ? 'selected' : ''; ?>>Sistema Nervoso</option>
        <option value="ANTI-MALARICO (ANTIBIOTICOS)" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'ANTI-MALARICO (ANTIBIOTICOS)' ? 'selected' : ''; ?>>Anti-Malarico (Antibióticos)</option>
        <option value="ANTI MICÓTICO" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'ANTI MICÓTICO' ? 'selected' : ''; ?>>Anti Micótico</option>
        <option value="ANTI-VIRAL" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'ANTI-VIRAL' ? 'selected' : ''; ?>>Anti-Viral</option>
        <option value="DIURETICOS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'DIURETICOS' ? 'selected' : ''; ?>>Diuréticos</option>
        <option value="E.H.E E ÁCIDO BASE" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'EQUILÍBRIO HIDRO-ELÉTRICO E ÁCIDO BASE' ? 'selected' : ''; ?>>Equilíbrio Hidro-Elétrico e Ácido Base</option>
        <option value="NUTRIÇÃO, SAIS MINERAIS E VITAMINAS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'NUTRIÇÃO, SAIS MINERAIS E VITAMINAS' ? 'selected' : ''; ?>>Nutrição, Sais Minerais e Vitaminas</option>
        <option value="FÁRMACOS USADOS NOS TRANSTORNOS ALÉRGICOS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'FÁRMACOS USADOS NOS TRANSTORNOS ALÉRGICOS' ? 'selected' : ''; ?>>Fármacos Usados nos Transtornos Alérgicos</option>
        <option value="FÁRMACOS USADOS NAS AFECÇÕES MUSCULOS-ESQUELÉTICOS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'FÁRMACOS USADOS NAS AFECÇÕES MUSCULOS-ESQUELÉTICOS' ? 'selected' : ''; ?>>Fármacos Usados nas Afecções Musculoesqueléticas</option>
        <option value="DERMATOLOGIA" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'DERMATOLOGIA' ? 'selected' : ''; ?>>Dermatologia</option>
        <option value="OTORRINOLARINGOLOGIA" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'OTORRINOLARINGOLOGIA' ? 'selected' : ''; ?>>Otorrinolaringologia</option>
        <option value="OFTALMOLOGIA" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'OFTALMOLOGIA' ? 'selected' : ''; ?>>Oftalmologia</option>
    </select>


    <select name="forma" id="forma" class="input-pesquisa">
        <option value="">Selecione uma forma</option>
        <option value="Comprimido" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Comprimido' ? 'selected' : ''; ?>>Comprimido</option>
        <option value="Injetável" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Injetável' ? 'selected' : ''; ?>>Injetável</option>
        <option value="Suspensão" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Suspensão' ? 'selected' : ''; ?>>Suspensão</option>
        <option value="Xarope" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Xarope' ? 'selected' : ''; ?>>Xarope</option>
        <option value="Pomada" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Pomada' ? 'selected' : ''; ?>>Pomada</option>
        <option value="Creme" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Creme' ? 'selected' : ''; ?>>Creme</option>
        <option value="Solução" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Solução' ? 'selected' : ''; ?>>Solução</option>
    </select>

    <select name="disponibilidade" class="input-pesquisa">
        <option value="">Selecione a disponibilidade</option>
        <option value="Disponível" <?php echo isset($_GET['disponibilidade']) && $_GET['disponibilidade'] == 'Disponível' ? 'selected' : ''; ?>>Disponível</option>
        <option value="Indisponível" <?php echo isset($_GET['disponibilidade']) && $_GET['disponibilidade'] == 'Indisponível' ? 'selected' : ''; ?>>Indisponível</option>
    </select>

    <button type="submit" class="btn-pesquisa">Pesquisar</button>
</form>


<table>
    <thead>
        <tr>
            <th>Rubrica</th>
            <th>Tipo</th>
            <th>Designação</th>
            <th>Forma</th>
            <th>Dose</th>
            <th>Instrução</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    
    <tbody>
    <?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['Rubrica'].'P'; ?></td>
            <td><?php echo $row['tipo']; ?></td>
            <td><?php echo $row['designacao']; ?></td>
            <td><?php echo $row['forma']; ?></td>
            <td><?php echo $row['dose']; ?></td>
            <td><?php echo $row['instrucao']; ?></td>
            <td>
                <?php echo isset($row['estado_disponibilidade']) && $row['estado_disponibilidade'] == 1 ? 'Disponível' : 'Indisponível'; ?>
            </td>
            <td>
                <a href="payment.php?medicamento=<?php echo isset($row['designacao']) ? urlencode($row['designacao']) : ''; ?>&preco=<?php echo isset($row['preco']) ? urlencode($row['preco']) : 0; ?>">
                    <button>Pesquisar</button>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="6">Nenhum medicamento encontrado.</td>
    </tr>
<?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=1&rows_per_page=<?php echo $rows_per_page; ?>">Primeira</a>
        <a href="?page=<?php echo $page - 1; ?>&rows_per_page=<?php echo $rows_per_page; ?>">Anterior</a>
    <?php else: ?>
        <span class="disabled">Primeira</span>
        <span class="disabled">Anterior</span>
    <?php endif; ?>

    Página <?php echo $page; ?> de <?php echo $total_pages; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>&rows_per_page=<?php echo $rows_per_page; ?>">Próxima</a>
        <a href="?page=<?php echo $total_pages; ?>&rows_per_page=<?php echo $rows_per_page; ?>">Última</a>
    <?php else: ?>
        <span class="disabled">Próxima</span>
        <span class="disabled">Última</span>
    <?php endif; ?>
</div>



</body>

<footer>
    <p>Copyright Notice © [2024] Pharma FIND. All rights reserved.</p>
    <img src="img/logo.png" alt="logotipo">
</footer>
</html>

<?php
mysqli_close($con);
?>

