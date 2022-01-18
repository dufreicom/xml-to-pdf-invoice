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

//Call template
$htmlTranslator = new \PhpCfdi\CfdiToPdf\Builders\HtmlTranslators\PlatesHtmlTranslator(
    getcwd(),
    'template'
);

//Create the converter
$converter = new PhpCfdi\CfdiToPdf\Converter(
    new PhpCfdi\CfdiToPdf\Builders\Html2PdfBuilder($htmlTranslator)
);

//Create the invoice as output.pdf
$converter->createPdfAs($cfdiData, 'output.pdf');

//Variable for returns any message
$ret = [];

//Check if pdf generated exist
$pdf_result = '/tmp/output.pdf';
if(file_exists($pdf_result)) {
    /**Response PDF as base64 */
    //Open file, convert to Base64, Response it & finish script
    $b64pdf = base64_encode(file_get_contents($pdf_result));

    $ret = [
        "fileContent" => $b64pdf
    ];
    print json_encode($ret);
    

    /** Send Binaries */
    // $fp = fopen($pdf_result, 'rb');

    //Send the correct headers
    // header("Content-Type: application/pdf");
    // header("Content-Length: " . filesize($pdf_result));

    //dump the pdf file and stop the script
    // fpassthru($fp);
    exit;
} else {
    //Response with JSON
    $ret = [
        "error" => "Something is wrong, the file is not generated."
    ];
    print json_encode($ret);
}

?>
