<?php
session_start();
include 'conexao.php';
require('TCPDF-main/tcpdf.php'); // Certifique-se de que a biblioteca FPDF esteja disponível

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

if (isset($_GET['venda_id'])) {
    $venda_id = $_GET['venda_id'];

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
            v.id = '$venda_id' AND u.id = '$user_id'
    ";

    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Gerando o PDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        

        // Logo
        $pdf->Image('img/logo.jpg', 93, 7, 23, 23); 
        
        // Cabeçalho
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Ln(30);
        $pdf->Cell(0, 10, 'PHARMAFIND', 0, 1, 'C');
        $pdf->SetFont('Helvetica', 'I', 12);
        $pdf->Cell(0, 10, 'Tel: +258 850 312 999 | Email: info@pharmafind.com', 0, 1, 'C');
        
        // Título do Comprovante
        $pdf->Ln(1);
        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'RECIBO DE COMPRA', 0, 1, 'C');
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 10, 'Compra Nº p' . str_pad($venda_id, 6, '0', STR_PAD_LEFT).'F', 0, 1, 'C');
        
        // Informações do Cliente e Farmácia
        $pdf->Ln(10);
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Cell(95, 10, 'CLIENTE: ' . $row['usuario_nome'], 0, 0, 'L'); 
        $pdf->Cell(95, 10, 'FARMACIA: ' . $row['farmacia'], 0, 1, 'R'); 
        
        
        $pdf->SetFillColor(14, 45, 82); 
        $pdf->SetTextColor(255, 255, 255); 
        $pdf->SetFont('Helvetica', 'B', 10);

        // Cabeçalho da tabela
        $pdf->Cell(35, 10, 'DATA', 1, 0, 'C', true);
        $pdf->Cell(45, 10, 'PRODUTO', 1, 0, 'C', true); 
        $pdf->Cell(15, 10, 'QNT', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'PRECO', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'MET. PAGAMENTO', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'ENTREGA', 1, 1, 'C', true);

       
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Helvetica', '', 10);

        // Preenchendo as células com os dados
        $pdf->Cell(35, 10, $row['data'], 1, 0, 'C');
        $pdf->Cell(45, 10, $row['produto'], 1, 0, 'C'); 
        $pdf->Cell(15, 10, $row['quantidade'], 1, 0, 'C');
        $pdf->Cell(30, 10, number_format($row['preco'], 2, ',', '.'), 1, 0, 'C');
        $pdf->Cell(40, 10, $row['m_pagamento'], 1, 0, 'C');
        $pdf->Cell(25, 10, ($row['entrega'] == 1 ? 'Sim' : 'Nao'), 1, 1, 'C');
        $pdf->Ln(10);
        
        // Deliver 
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(60, 10, 'Entrega:', 0, 0, 'L'); 
        $pdf->Cell(60, 10, ($row['entrega'] == 1 ? number_format(100, 2, ',', '.').' MT' : number_format(0, 2, ',', '.').' MT'), 0, 1, 'C');

       
        // Subtotal
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(60, 10, 'Subtotal:', 0, 0, 'L');
        $pdf->Cell(60, 10, number_format($row['total'] - ($row['total'] * 0.16), 2, ',', '.').' MT', 0, 1, 'C'); 
        $pdf->SetFont('Helvetica', 'B', 10);

        // IVA
        $pdf->Cell(60, 10, 'IVA:', 0, 0, 'L');
        $pdf->Cell(60, 10, number_format(($row['total'] * 0.16), 2, ',', '.').' MT', 0, 1, 'C');
        $pdf->SetFont('Helvetica', 'B', 10);
        // Total
        $pdf->Cell(60, 10, 'Total:', 0, 0, 'L');
        $pdf->Cell(60, 10, number_format($row['total'], 2, ',', '.').' MT', 0, 1, 'C');
       
        $pdf->Output('Compra' . str_pad($venda_id, 6, '0', STR_PAD_LEFT) . '.pdf', 'D');
        
    } else {
        echo 'Venda não encontrada.';
    }
}

mysqli_close($con);
?>
