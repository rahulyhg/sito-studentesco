<?php
require_once 'templates/barcode/barcode.php';
if (isset($download)) {
    
    $fontSize = 10; // GD1 in px ; GD2 in point
    $marge = 10; // between barcode and hri in pixel
    $x = 125; // barcode center
    $y = 125; // barcode center
    $height = 50; // barcode height in 1D ; module size in 2D
    $width = 2; // barcode height in 1D ; not use in 2D
    $angle = 0; // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation
    
    $code = '123456789012'; // barcode, of course ;)
    $type = 'ean13';
    
    // -------------------------------------------------- //
    // USEFUL
    // -------------------------------------------------- //
    function drawCross($im, $color, $x, $y) {
        imageline($im, $x - 10, $y, $x + 10, $y, $color);
        imageline($im, $x, $y - 10, $x, $y + 10, $color);
    }
    
    // -------------------------------------------------- //
    // ALLOCATE GD RESSOURCE
    // -------------------------------------------------- //
    $im = imagecreatetruecolor(300, 300);
    $black = ImageColorAllocate($im, 0x00, 0x00, 0x00);
    $white = ImageColorAllocate($im, 0xff, 0xff, 0xff);
    $red = ImageColorAllocate($im, 0xff, 0x00, 0x00);
    $blue = ImageColorAllocate($im, 0x00, 0x00, 0xff);
    imagefilledrectangle($im, 0, 0, 300, 300, $white);
    
    $text = fopen("barcode.txt", "w+");
    fclose($text);
    file_put_contents("barcode.txt", "", LOCK_EX);
    $eans = $options["database"]->select("ean", "*", array(
        "ORDER" => "persona"
    ));
    $classi = $options["database"]->select("classi", "*", array(
        "ORDER" => "id"
    ));
    $studenti = $options["database"]->select("studenti", "*", array(
        "id" => $options["database"]->max("studenti", "id"),
        "ORDER" => "persona"
    ));
    $results = $options["database"]->select("persone", "*");
    if ($results != null) {
        foreach ($results as $result) {
            $studente = ricerca($studenti, $result["id"], "persona");
            if ($studente != - 1) {
                $classe = "";
                $class = ricerca($classi, $studenti[$studente]["classe"]);
                if ($class != - 1) {
                    $classe = $classi[$class]["nome"];
                }
                $tot = explode(" ", $result["nome"]);
                $nome = "";
                for ($i = 0; $i < count($tot); $i ++) {
                    $nome .= $tot[$i];
                    if ($i == 0) $nome .= ";";
                    else if ($i != count($tot) - 1) $nome .= " ";
                }
                $data = Barcode::gd($im, $black, $x, $y, $angle, $type, array(
                    'code' => $eans[ricerca($eans, $result["id"], "persona")]["ean"]
                ), $width, $height);
                file_put_contents("barcode.txt", $data['hri'] . ";" . $nome . ";" . $classe . "\n", FILE_APPEND | LOCK_EX);
            }
        }
    }
}
else {
    require_once 'templates/pdf/fpdf.php';
    
    // -------------------------------------------------- //
    // USEFUL
    // -------------------------------------------------- //
    class eFPDF extends FPDF {

        function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle = 0) {
            $font_angle += 90 + $txt_angle;
            $txt_angle *= M_PI / 180;
            $font_angle *= M_PI / 180;
            
            $txt_dx = cos($txt_angle);
            $txt_dy = sin($txt_angle);
            $font_dx = cos($font_angle);
            $font_dy = sin($font_angle);
            
            $s = sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET', $txt_dx, $txt_dy, $font_dx, $font_dy, $x * $this->k, ($this->h - $y) * $this->k, $this->_escape($txt));
            if ($this->ColorFlag) $s = 'q ' . $this->TextColor . ' ' . $s . ' Q';
            $this->_out($s);
        }
    }
    
    // -------------------------------------------------- //
    // PROPERTIES
    // -------------------------------------------------- //
    
    $fontSize = 10;
    $marge = 10; // between barcode and hri in pixel
    $x = 150; // barcode center
    $y = 75; // barcode center
    $height = 50; // barcode height in 1D ; module size in 2D
    $width = 2; // barcode height in 1D ; not use in 2D
    $angle = 0; // rotation in degrees
    
    $type = 'ean13';
    $black = '000000'; // color in hexa
                       
    // -------------------------------------------------- //
                       // ALLOCATE FPDF RESSOURCE
                       // -------------------------------------------------- //
    
    $pdf = new eFPDF('P', 'pt');
    $pdf->AddPage();
    
    $pdf->SetFont('Arial', 'B', $fontSize);
    $pdf->SetTextColor(0, 0, 0);
    // -------------------------------------------------- //
    // BARCODE
    // -------------------------------------------------- //
    $eans = $options["database"]->select("ean", "*", array(
        "ORDER" => "persona"
    ));
    $persone = $options["database"]->select("persone", array(
        "id",
        "nome",
        "username",
        "password",
        "stato"
    ), array(
        "ORDER" => "id"
    ));
    $studenti = $options["database"]->select("studenti", "*", array(
        "id" => $options["database"]->max("studenti", "id"),
        "ORDER" => "persona"
    ));
    $datas = $options["database"]->select("classi", "*");
    if ($datas != null) {
        foreach ($datas as $data) {
            $pdf->SetFillColor(200, 220, 255);
            $pdf->Cell(0, 12, $data["nome"], 0, 1, 'L', true);
            $y = 75;
            $x = 150;
            foreach ($studenti as $key => $studente) {
                if ($studente["classe"] == $data["id"]) {
                    $persona = ricerca($persone, $studente["persona"]);
                    if ($persona != - 1) {
                        $ean = ricerca($eans, $studente["persona"], "persona");
                        if ($ean != - 1) {
                            $code = $eans[ricerca($eans, $studente["persona"], "persona")]["ean"]; // barcode, of course ;)
                            $result = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array(
                                'code' => $code
                            ), $width, $height);
                            
                            $len = $pdf->GetStringWidth($result['hri']);
                            Barcode::rotate(- $len / 2, ($result['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
                            $nome = $persone[$persona]["nome"];
                            $pdf->TextWithRotation($x + $xt - (strlen($nome) + (strlen($nome) / 2)), $y + $yt, $nome, $angle);
                            if ($pdf->h > $y + 100) $y += 100;
                            else {
                                $y = 75;
                                if ($pdf->w > $x + 270) $x += 270;
                                else {
                                    $pdf->AddPage();
                                    $x = 150;
                                }
                            }
                        }
                    }
                    unset($studenti[$key]);
                }
            }
            $pdf->AddPage();
        }
    }
    /*
     * $eans = $options["database"]->select("ean", "*", array ("ORDER" => "persona"));
     * $classi = $options["database"]->select("classi", "*", array ("ORDER" => "id"));
     * $studenti = $options["database"]->select("studenti", "*", array ("id" => $options["database"]->max("studenti", "id"), "ORDER" => "persona"));
     * $results = $options["database"]->select("persone", "*");
     * if ($results != null) {
     * foreach ($results as $result) {
     * $studente = ricerca($studenti, $result["id"], "persona");
     * if ($studente != -1) {
     * $classe = "";
     * $class = ricerca($classi, $studenti[$studente]["classe"]);
     * if ($class != -1) {
     * $classe = $classi[$class]["nome"];
     * }
     * $code = $eans[ricerca($eans, $result["id"], "persona")]["ean"]; // barcode, of course ;)
     * $nome = $result["nome"];
     * $data = Barcode::fpdf($pdf, $black, $x, $y, $angle, $type, array ('code' => $code), $width, $height);
     *
     * $len = $pdf->GetStringWidth($data['hri']);
     * Barcode::rotate(-$len / 2, ($data['height'] / 2) + $fontSize + $marge, $angle, $xt, $yt);
     * $pdf->TextWithRotation($x + $xt, $y + $yt, $nome . ", " . $classe, $angle);
     * if ($pdf->h > $y + 100) $y += 100;
     * else {
     * $y = 75;
     * if ($pdf->w > $x + 270) $x += 270;
     * else {
     * $pdf->AddPage();
     * $x = 150;
     * }
     * }
     * }
     * }
     * }
     */
    // -------------------------------------------------- //
    // HRI
    // -------------------------------------------------- //
    
    $pdf->Output();
}
?>