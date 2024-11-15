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
    CONCAT(UPPER(SUBSTRING(mg.tipo, 1, 1)), LPAD(mg.id, 6, '0'),'P') AS Rubrica
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

// Calcular o total de registros (incluindo filtros)
$total_sql = "
    SELECT COUNT(DISTINCT mg.id) AS total
    FROM medicamentos_gerais mg
    LEFT JOIN medicamentos m ON mg.designacao = m.nome
    WHERE 1=1
";
$total_params = [];
$total_types = '';

// Reaplicando os filtros na consulta de contagem
if (!empty($tipoSelecionado)) {
    $total_sql .= " AND mg.tipo = ?";
    $total_params[] = $tipoSelecionado;
    $total_types .= 's';
}

if (!empty($medicamento_filtro)) {
    $total_sql .= " AND mg.designacao LIKE ?";
    $total_params[] = $medicamento_filtro;
    $total_types .= 's';
}

if (!empty($id_filtro)) {
    $total_sql .= " AND mg.id = ?";
    $total_params[] = $id_filtro;
    $total_types .= 'i';
}

if (!empty($formaSelecionada)) {
    $total_sql .= " AND mg.forma = ?";
    $total_params[] = $formaSelecionada;
    $total_types .= 's';
}

// Filtro de disponibilidade na contagem (usando HAVING)
if (!empty($disponibilidadeSelecionada)) {
    $total_sql .= " GROUP BY mg.id HAVING ";
    if ($disponibilidadeSelecionada == 'Disponível') {
        $total_sql .= "SUM(m.quant) > 0";
    } elseif ($disponibilidadeSelecionada == 'Indisponível') {
        $total_sql .= "SUM(m.quant) = 0";
    }
}

// Executando a consulta de contagem
$total_stmt = mysqli_prepare($con, $total_sql);
if ($total_types) {
    mysqli_stmt_bind_param($total_stmt, $total_types, ...$total_params);
}
mysqli_stmt_execute($total_stmt);
$total_result = mysqli_stmt_get_result($total_stmt);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row ? $total_row['total'] : 0;
$total_pages = ceil($total_records / $rows_per_page);

// Fechando a conexão
$stmt->close();
$total_stmt->close();
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
    <input type="text" name="medicamento" value="<?php echo isset($_GET['medicamento']) ? $_GET['medicamento'] : ''; ?>" placeholder="Designação" class="input-pesquisa">

    <label class="label-name">Rubrica:</label>
    <input type="text" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>" placeholder="ID" style="width: 90px;" class="input-pesquisa">

    <select name="tipo" id="tipo" class="input-pesquisa">
        <option value="">Selecione um tipo</option>
        <option value="ANTI-MALARICO (ANTIBIOTICOS)" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'ANTI-MALARICO (ANTIBIOTICOS)' ? 'selected' : ''; ?>>Anti-Malarico (Antibióticos)</option>
        <option value="ANTI MICÓTICO" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'ANTI MICÓTICO' ? 'selected' : ''; ?>>Anti Micótico</option>
        <option value="ANTI-VIRAL" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'ANTI-VIRAL' ? 'selected' : ''; ?>>Anti-Viral</option>
        <option value="CARDIOVASULAR" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'CARDIOVASULAR' ? 'selected' : ''; ?>>Cardiovascular</option>
        <option value="CUIDADOS COM A PELE" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'CUIDADOS COM A PELE' ? 'selected' : ''; ?>>Cuidados com a Pele</option>
        <option value="DERMATOLOGIA" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'DERMATOLOGIA' ? 'selected' : ''; ?>>Dermatologia</option>
        <option value="DIGESTIVO" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'DIGESTIVO' ? 'selected' : ''; ?>>Digestivo</option>
        <option value="ENDOCRINOLOGIA E METABOLISMO" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'ENDOCRINOLOGIA E METABOLISMO' ? 'selected' : ''; ?>>Endocrinologia e Metabolismo</option>
        <option value="E.H.E E ÁCIDO BASE" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'EQUILÍBRIO HIDRO-ELÉTRICO E ÁCIDO BASE' ? 'selected' : ''; ?>>Equilíbrio Hidro-Elétrico e Ácido Base</option>
        <option value="FÁRMACOS USADOS NAS AFECÇÕES MUSCULOS-ESQUELÉTICOS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'FÁRMACOS USADOS NAS AFECÇÕES MUSCULOS-ESQUELÉTICOS' ? 'selected' : ''; ?>>Fármacos Usados nas Afecções Musculoesqueléticas</option>
        <option value="FÁRMACOS USADOS NOS TRANSTORNOS ALÉRGICOS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'FÁRMACOS USADOS NOS TRANSTORNOS ALÉRGICOS' ? 'selected' : ''; ?>>Fármacos Usados nos Transtornos Alérgicos</option>
        <option value="GENITO-URINÁRIO E HORMONAS SEXUAIS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'GENITO-URINÁRIO E HORMONAS SEXUAIS' ? 'selected' : ''; ?>>Genito-Urinário e Hormonas Sexuais</option>
        <option value="HIGIENE PESSOAL" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'HIGIENE PESSOAL' ? 'selected' : ''; ?>>Higiene Pessoal</option>
        <option value="INFANTIL" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'INFANTIL' ? 'selected' : ''; ?>>Infantil</option>
        <option value="NUTRIÇÃO, SAIS MINERAIS E VITAMINAS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'NUTRIÇÃO, SAIS MINERAIS E VITAMINAS' ? 'selected' : ''; ?>>Nutrição, Sais Minerais e Vitaminas</option>
        <option value="Outros" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'outros' ? 'selected' : ''; ?>>outros</option>

        <option value="OTORRINOLARINGOLOGIA" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'OTORRINOLARINGOLOGIA' ? 'selected' : ''; ?>>Otorrinolaringologia</option>
        <option value="OFTALMOLOGIA" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'OFTALMOLOGIA' ? 'selected' : ''; ?>>Oftalmologia</option>
        <option value="PRODUTOS ÍNTIMOS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'PRODUTOS ÍNTIMOS' ? 'selected' : ''; ?>>Produtos Íntimos</option>
        <option value="RESPIRATÓRIO" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'RESPIRATÓRIO' ? 'selected' : ''; ?>>Respiratório</option>
        <option value="SANGUE" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'SANGUE' ? 'selected' : ''; ?>>Sangue</option>
        <option value="SISTEMA NERVOSO" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'SISTEMA NERVOSO' ? 'selected' : ''; ?>>Sistema Nervoso</option>
        <option value="SUPLEMENTOS" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'SUPLEMENTOS' ? 'selected' : ''; ?>>Suplementos</option>
        <option value="VETERINÁRIO" <?php echo isset($_GET['tipo']) && $_GET['tipo'] == 'VETERINÁRIO' ? 'selected' : ''; ?>>Veterinário</option>
    </select>



    <select name="forma" id="forma" class="input-pesquisa">
        <option value="">Selecione uma forma</option>
        <option value="Adesivo" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Adesivo' ? 'selected' : ''; ?>>Adesivo</option>
        <option value="Aerosol" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Aerosol' ? 'selected' : ''; ?>>Aerosol</option>
        <option value="Comprimido" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Comprimido' ? 'selected' : ''; ?>>Comprimido</option>
        <option value="Creme" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Creme' ? 'selected' : ''; ?>>Creme</option>
        <option value="Gel" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Gel' ? 'selected' : ''; ?>>Gel</option>
        <option value="Injetável" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Injetável' ? 'selected' : ''; ?>>Injetável</option>
        <option value="Loção" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Loção' ? 'selected' : ''; ?>>Loção</option>
        <option value="Pastilha" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Pastilha' ? 'selected' : ''; ?>>Pastilha</option>
        <option value="Pomada" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Pomada' ? 'selected' : ''; ?>>Pomada</option>
        <option value="Pó" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Pó' ? 'selected' : ''; ?>>Pó</option>
        <option value="Suspensão" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Suspensão' ? 'selected' : ''; ?>>Suspensão</option>
        <option value="Suplemento" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Suplemento' ? 'selected' : ''; ?>>Suplemento</option>
        <option value="Xarope" <?php echo isset($_GET['forma']) && $_GET['forma'] == 'Xarope' ? 'selected' : ''; ?>>Xarope</option>
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
            <td><?php echo $row['Rubrica']; ?></td>
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

<!-- Código HTML para paginação com filtros na URL -->
<div class="pagination">
    <?php
    // Definindo quantas páginas ao redor da página atual queremos exibir
    $pages_to_show = 5;
    $start_page = max(1, $page - 2); // Página inicial a exibir
    $end_page = min($total_pages, $page + 2); // Página final a exibir

    // Ajustando as páginas exibidas quando a página atual está perto do início ou do fim
    if ($page <= 3) {
        $start_page = 1;
        $end_page = min($total_pages, $pages_to_show);
    } elseif ($page >= $total_pages - 2) {
        $start_page = max(1, $total_pages - $pages_to_show + 1);
        $end_page = $total_pages;
    }

    // Link para a página anterior
    if ($page > 1) {
        echo '<a href="?page=' . ($page - 1) .'&rows_per_page=' . urlencode($rows_per_page). '&medicamento=' . urlencode($medicamento_filtro) . '&tipo=' . urlencode($tipoSelecionado) . '&forma=' . urlencode($formaSelecionada) . '&disponibilidade=' . urlencode($disponibilidadeSelecionada) . '">Anterior</a> ';
    }

    // Exibindo as páginas no intervalo definido
    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $page) {
            echo '<span class="active">' . $i . '</span> ';
        } else {
            echo '<a href="?page=' . $i .'&rows_per_page=' . urlencode($rows_per_page). '&medicamento=' . urlencode($medicamento_filtro) . '&tipo=' . urlencode($tipoSelecionado) . '&forma=' . urlencode($formaSelecionada) . '&disponibilidade=' . urlencode($disponibilidadeSelecionada) . '">' . $i . '</a> ';
        }
    }

    // Exibir reticências "..." se houver mais páginas após as exibidas
    if ($end_page < $total_pages) {
        echo '... ';
    }

    // Link para a próxima página
    if ($page < $total_pages) {
        echo '<a href="?page=' . ($page + 1) .'&rows_per_page=' . urlencode($rows_per_page). '&medicamento=' . urlencode($medicamento_filtro) . '&tipo=' . urlencode($tipoSelecionado) . '&forma=' . urlencode($formaSelecionada) . '&disponibilidade=' . urlencode($disponibilidadeSelecionada) . '">Próxima</a>';
    }
    ?>
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

