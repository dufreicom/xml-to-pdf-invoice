<?php
    //Import using composer libraries CfdiUtils & CfdiToPdf
    require "vendor/autoload.php";
    
    //Starting script server open CORS
    header('Access-Control-Allow-Origin: GET,POST');
    header('Content-Type: application/json');

    
    //Obtaining xml from JSON
    $data = json_decode(file_get_contents('php://input'), true);
    $xml = $data['external'];

    //Clean cfdi
    $xml = CfdiUtils\Cleaner\Cleaner::staticClean($xml);

    // Create the main node structure
    $comprobante = CfdiUtils\Nodes\XmlNodeUtils::nodeFromXmlString($xml);

    //Create the CfdiData object, it contains all the required information
    $cfdiData = (new PhpCfdi\CfdiToPdf\CfdiDataBuilder())
        ->build($comprobante);

    //Create the converter
    $converter = new PhpCfdi\CfdiToPdf\Converter(
        new PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder()
    );

    //Create the invoice as output.pdf
    $converter->createPdfAs($cfdiData, 'output.pdf');
    
    //Variable for returns any message
    $ret = [];

    //Check if pdf generated exist
    $pdf_result = './output.pdf';
    if(file_exists($pdf_result)) {
        //open the file in binary mode
        
        $fp = fopen($pdf_result, 'rb');
    
    
        //Send the correct headers
        header("Content-Type: application/pdf");
        header("Content-Length: " . filesize($pdf_result));
    
        //dump the pdf file and stop the script
        fpassthru($fp);
        exit;
    } else {
        //Response with JSON
        $ret = [
            "error: " => "Something is wrong, the file is not generated."
        ];
        print json_encode($ret);
    }

