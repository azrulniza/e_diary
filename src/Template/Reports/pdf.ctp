<?php 
header('Content-Disposition: attachment; filename="downloaded.pdf"'); 
echo $content_for_layout;  

$judul = "Halo apa kabar";
        $header = array(
            array("label" => "NIM", "length" => 30, "align" => "L"),
array("label" => "NAMA", "length" => 30, "align" => "L"),
            array("label" => "ALAMAT", "length" => 30, "align" => "L"),
            array("label" => "ALAMAT", "length" => 30, "align" => "L"),
            array("label" => "ALAMAT", "length" => 30, "align" => "L"),
            array("label" => "TGL LAHIR", "length" => 30, "align" => "L")
        );
$pdf = new FPDF();
        $fpdf->AddPage();
        #tampilkan judul
        $fpdf->SetFont('Arial', 'B', 16);
        $fpdf->Cell(10, 10, $judul);
        #buat header tabel
        $fpdf->SetFont('Arial', '', '10');
        $fpdf->SetFillColor(255, 0, 0);
        $fpdf->SetTextColor(255);
        $fpdf->SetDrawColor(128, 0, 0);
        foreach ($header as $kolom) {
//            debug($kolom);exit;
            $fpdf->Cell($kolom['length'], 5, $kolom['label'], 1, '0', $kolom['align'], true);
        }
        $fpdf->Ln();
        #tampilkan data tabelnya
        $fpdf->SetFillColor(224, 235, 255);
        $fpdf->SetTextColor(0);
        $fpdf->SetFont('');
        $fill = false;
        foreach ($data as $baris) {
            $i = 0;
            foreach ($baris as $cell) {
//                debug($header);exit;
//                $fpdf->Cell($header[$i]['length'], 5, $cell, 1, '0', $kolom['align'], $fill);
                $fpdf->Cell(30, 5, $cell, 1, '0', $kolom['align'], $fill);
                $i++;
            }
            $fill = !$fill;
            $fpdf->Ln();
        }
        
        
        $fpdf->Output();
        ?>