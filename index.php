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
$converter->createPdfAs($cfdiData, '/tmp/output.pdf');

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Welcome!</title>
    <link href="https://fonts.googleapis.com/css?family=Dosis:300&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex h-screen">
    <div class="rounded-full mx-auto self-center relative" style="height: 400px; width: 400px; background: linear-gradient(123.19deg, #266488 3.98%, #258ECB 94.36%)">
        <h1 class="font-light absolute w-full text-center text-blue-200" style="font-family: Dosis; font-size: 45px; top: 35%">Hello XML v0.2</h1>
        <div class="w-full relative absolute" style="top: 60%; height: 50%">
            <div class="absolute inset-x-0 bg-white" style="bottom: 0; height: 55%"></div>
            <svg viewBox="0 0 1280 311" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0)"><path d="M1214 177L1110.5 215.5L943.295 108.5L807.5 168.5L666 66.5L581 116L517 49.5L288.5 184L163.5 148L-34.5 264.5V311H1317V258.5L1214 177Z" fill="white"/><path d="M1214 177L1110.5 215.5L943.295 108.5L807.5 168.5L666 66.5L581 116L517 49.5L288.5 184L163.5 148L-34.5 264.5L163.5 161L275 194L230.5 281.5L311 189L517 61L628 215.5L600 132.5L666 77L943.295 295L833 184L943.295 116L1172 275L1121 227L1214 189L1298 248L1317 258.5L1214 177Z" fill="#DCEFFA"/></g><defs><clipPath id="clip0"><rect width="1280" height="311" fill="white"/></clipPath></defs></svg>
        </div>
    </div>
</body>
</html>
