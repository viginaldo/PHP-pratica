<?php
session_start();
include 'conexao.php';
require('TCPDF-main/tcpdf.php'); 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Consulta para obter todas as compras do usuário
$sql = "
    SELECT 
        v.data_venda AS data,
        f.nome AS farmacia,
        m.nome AS produto,
        v.quantidade,
        v.preco AS preco,
        v.total AS total,
        v.m_pagamento,
        v.entrega,
        u.nome AS usuario_nome
    FROM 
        vendas v
    JOIN 
        farmacias f ON v.farmacia_id = f.id
    JOIN 
        medicamentos m ON v.medicamento_id = m.id
    JOIN 
        usuarios u ON u.id = v.us_id
    WHERE 
        u.id = '$user_id'
    ORDER BY 
        v.data_venda DESC
";

$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    
    // Variáveis para cálculos de total, IVA, subtotal e entrega
    $total = 0;
    $total_iva = 0;
    $total_entrega = 0;
    $subtotal = 0;
    
    // Gerando o PDF
    $pdf = new TCPDF();
    $pdf->AddPage();
    
    // Logo
    $pdf->Image('img/logo.jpg', 93, 7, 23, 23);
    
    // Cabeçalho
    $pdf->SetFont('Helvetica', 'B', 16);
    $pdf->Ln(20);
    $pdf->Cell(0, 10, 'PHARMAFIND', 0, 1, 'C');
    $pdf->SetFont('Helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Extrato de Compras', 0, 1, 'C');
    $pdf->SetFont('Helvetica', 'I', 12);
    $pdf->Cell(0, 10, 'Tel: +258 850 312 999 | Email: info@pharmafind.com', 0, 1, 'C');
    
    // Informações do Cliente
    $pdf->Ln(5);
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'CLIENTE: ' . $row['usuario_nome'], 0, 1, 'L'); 
    
    // Cabeçalho da tabela de extrato
    $pdf->Ln(5);
    $pdf->SetFillColor(14, 45, 82);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->Cell(40, 10, 'DATA', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'FARMÁCIA', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'PRODUTO', 1, 0, 'C', true);
    $pdf->Cell(15, 10, 'QNT', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'PREÇO', 1, 0, 'C', true);
    $pdf->Cell(20, 10, 'ENTREGA', 1, 1, 'C', true);

    // Cor do texto da tabela de extrato
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Helvetica', '', 10);

    // Lista de medicamentos comprados
    do {
        $pdf->Cell(40, 10, $row['data'], 1, 0, 'C');
        $pdf->Cell(40, 10, $row['farmacia'], 1, 0, 'C');
        $pdf->Cell(40, 10, $row['produto'], 1, 0, 'C');
        $pdf->Cell(15, 10, $row['quantidade'], 1, 0, 'C');
        $pdf->Cell(30, 10, number_format($row['preco'], 2, ',', '.'), 1, 0, 'C');
        $pdf->Cell(20, 10, ($row['entrega'] == 1 ? 'Sim' : 'Nao'), 1, 1, 'C');
        
        // Atualizar totais para cálculo
        $total += $row['total'];
        $total_iva += ($row['total'] * 0.16);  // IVA 16%
        $total_entrega += ($row['entrega'] == 1 ? 100 : 0);  // Supondo que a entrega custa 100 MT
        $subtotal += ($row['total'] - ($row['total'] * 0.16));  // Subtotal = Total - IVA
    } while ($row = mysqli_fetch_assoc($result));

    // Fechamento do PDF - Adicionando Totais
    $pdf->Ln(10);  // Espaço entre a tabela e os totais
    
    // Subtotal
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->Cell(60, 10, 'Subtotal:', 0, 0, 'L');
    $pdf->Cell(60, 10, number_format($subtotal, 2, ',', '.') . ' MT', 0, 1, 'C'); 
    
    // Entrega
    $pdf->Cell(60, 10, 'Entrega:', 0, 0, 'L'); 
    $pdf->Cell(60, 10, number_format($total_entrega, 2, ',', '.') . ' MT', 0, 1, 'C');

    // IVA
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->Cell(60, 10, 'IVA (16%):', 0, 0, 'L');
    $pdf->Cell(60, 10, number_format($total_iva, 2, ',', '.') . ' MT', 0, 1, 'C');
    
    // Total
    $pdf->SetFont('Helvetica', 'B', 10);
    $pdf->Cell(60, 10, 'Total:', 0, 0, 'L');
    $pdf->Cell(60, 10, number_format($total, 2, ',', '.') . ' MT', 0, 1, 'C');
    
    // Fechando o PDF
    $pdf->Output('Extrato_Compras.pdf', 'D');

} else {
    echo 'Nenhuma compra encontrada para este usuário.';
}

mysqli_close($con);
?>
