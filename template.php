<?php

declare(strict_types=1);

/** @noinspection PhpFullyQualifiedNameUsageInspection */
/** @var \League\Plates\Template\Template $this */
/** @var \PhpCfdi\CfdiToPdf\CfdiData $cfdiData */
$comprobante = $cfdiData->comprobante();
$emisor = $cfdiData->emisor();
$receptor = $cfdiData->receptor();
$tfd = $cfdiData->timbreFiscalDigital();
$relacionados = $comprobante->searchNode('cfdi:CfdiRelacionados');
$impuestos = $comprobante->searchNode('cfdi:Impuestos');
$totalImpuestosTrasladados = $comprobante->searchAttribute('cfdi:Impuestos', 'TotalImpuestosTrasladados');
$totalImpuestosRetenidos = $comprobante->searchAttribute('cfdi:Impuestos', 'TotalImpuestosRetenidos');
$conceptos = $comprobante->searchNodes('cfdi:Conceptos', 'cfdi:Concepto');
$pagos = $comprobante->searchNodes('cfdi:Complemento', 'pago10:Pagos', 'pago10:Pago');
$conceptoCounter = 0;
$conceptoCount = $conceptos->count();
$pagoCounter = 0;
$pagoCount = $pagos->count();
$complemento = $comprobante -> searchNode('cfdi:Complemento');

?>
<style>
    * {
        font-size: 7pt;
        padding: 0;
        margin: 0;
    }

    table th, table td {
        vertical-align: top;
        text-align: center;
    }

    table th {
        font-weight: bold;
    }

    table.tabular th {
        text-align: right;
    }

    table.tabular td {
        text-align: left;
    }

    div.panel {
        border: 0.2mm solid #0000;
        margin-bottom: 1mm;
    }

    div.panel div.title {
        background-color: #333333;
        color: #ffffff;
        font-weight: bold;
        padding: 1mm 2mm;
    }

    
    div.panel div.content {
        padding: 1mm 2mm;
    }

    .panelcp .contentcp {
        border: 0.2mm solid #0000;
        margin-bottom: 1mm;
    }

    .titlecp {
        background-color: #333333;
        color: #ffffff;
        font-weight: bold;
        padding: 1mm 2mm;
    }

    .subtitle {
        text-align: center;
        font-weight: bold;
        font-style: italic;
    }
    .subtable {
        width: 100%;
        max-width: 100%
    }
    .border-subtable {
        border-bottom: 0.2mm solid black !important;
    }

    .pa{
        display: block;
        max-width: 10%;
        white-space: nowrap;
        overflow: hidden;
    }
</style>
<!--suppress HtmlUnknownTag -->
<page backbottom="10mm">
    <page_footer>
        <p style="text-align: center">
            Este documento es una representación impresa de un Comprobante Fiscal Digital a través de Internet
            versión <?=$this->e($comprobante['Version'])?>
            <br/>UUID: <?=$this->e($tfd['UUID'])?> - Página [[page_cu]] de [[page_nb]]
        </p>
    </page_footer>
    <div class="panel">
        <div class="title" style="text-align: center">UUID <?=$this->e($tfd['UUID'])?></div>
        <table class="tabular">
            <tr>
                <td rowspan="20" style="padding-right: 4mm;">
                    <!--suppress CheckEmptyScriptTag, HtmlUnknownTag -->
                    <qrcode style="width: 30mm;" ec="M" value="<?=$this->e($cfdiData->qrUrl())?>"/>
                </td>
                <th>Tipo:</th>
                <td><?=$this->e($comprobante['TipoDeComprobante'])?></td>
            </tr>
            <tr>
                <th class="">Serie:</th>
                <td><?=$this->e($comprobante['Serie'])?></td>
            </tr>
            <tr>
                <th class="">Folio:</th>
                <td><?=$this->e($comprobante['Folio'])?></td>
            </tr>
            <tr>
                <th class="">Lugar de expedición:</th>
                <td><?=$this->e($comprobante['LugarExpedicion'])?></td>
            </tr>
            <tr>
                <th class="">Fecha:</th>
                <td><?=$this->e($comprobante['Fecha'])?></td>
            </tr>
            <tr>
                <th>Forma de pago:</th>
                <td><?=$this->e($comprobante['FormaPago'])?></td>
            </tr>
            <tr>
                <th>Método de pago:</th>
                <td><?=$this->e($comprobante['MetodoPago'])?></td>
            </tr>
            <?php if ('' !== $comprobante['CondicionesDePago']) : ?>
                <tr>
                    <th>Condiciones de pago:</th>
                    <td><?=$this->e($comprobante['CondicionesDePago'])?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>Certificado emisor:</th>
                <td><?=$this->e($comprobante['NoCertificado'])?></td>
            </tr>
            <tr>
                <th>Certificado SAT:</th>
                <td><?=$this->e($tfd['NoCertificadoSAT'])?></td>
            </tr>
            <tr>
                <th>RFC Proveedor:</th>
                <td><?=$this->e($tfd['RfcProvCertif'])?></td>
            </tr>
            <tr>
                <th>Fecha de certificación:</th>
                <td><?=$this->e($tfd['FechaTimbrado'])?></td>
            </tr>
        </table>
    </div>
    <div class="panel">
        <div class="title">Emisor</div>
        <div class="content">
            <p>
                <?=$this->e($emisor['Nombre'] ? : 'No se especificó el nombre del emisor')?>
                <br/>RFC: <?=$this->e($emisor['Rfc'])?>
                <br/>Régimen Fiscal: <?=$this->e($emisor['RegimenFiscal'])?>
            </p>
        </div>
    </div>
    <div class="panel">
        <div class="title">Receptor</div>
        <div class="content">
            <p>
                <?=$this->e($receptor['Nombre'] ? : '(No se especificó el nombre del receptor)')?>
                <br/>RFC: <?=$this->e($receptor['Rfc'])?>
                <br/>Uso del CFDI: <?=$this->e($receptor['UsoCFDI'])?>
                <?php if ('' !== $receptor['ResidenciaFiscal']) : ?>
                    <br/>Residencia fiscal: <?=$this->e($receptor['ResidenciaFiscal'])?>
                <?php endif; ?>
                <?php if ('' !== $receptor['NumRegIdTrib']) : ?>
                    <br/>Residencia fiscal: <?=$this->e($receptor['NumRegIdTrib'])?>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <?php if (null !== $relacionados) : ?>
        <div class="panel">
            <div class="title">CFDI Relacionados (Tipo de relación: <?=$this->e($relacionados['TipoRelacion'])?>)</div>
            <div class="content">
                <?php foreach ($relacionados->searchNodes('cfdi:CfdiRelacionado') as $relacionado) : ?>
                    <span>UUID: <?=$relacionado['UUID']?></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <?php foreach ($conceptos as $concepto) : ?>
        <?php
        $conceptoCounter = $conceptoCounter + 1;
        $conceptoTraslados = $concepto->searchNodes('cfdi:Impuestos', 'cfdi:Traslados', 'cfdi:Traslado');
        $conceptoRetenciones = $concepto->searchNodes('cfdi:Impuestos', 'cfdi:Retenciones', 'cfdi:Retencion');
        ?>
        <div class="panel">
            <div class="title">Concepto: <?=$this->e($conceptoCounter)?> de <?=$this->e($conceptoCount)?></div>
            <div class="content">
                <p><strong>Descripcion: <?=$this->e($concepto['Descripcion'])?></strong></p>
                <p>
                    <span>No identificación: <?=$this->e($concepto['NoIdentificacion'] ? : '(ninguno)')?>,</span>
                    <span>Clave SAT: <?=$this->e($concepto['ClaveProdServ'])?>,</span>
                    <span>Clave Unidad: <?=$this->e($concepto['ClaveUnidad'])?>,</span>
                    <span>Unidad: <?=$this->e($concepto['Unidad'] ? : '(ninguna)')?></span>
                </p>
                <p>
                    <strong>Cantidad: <?=$this->e($concepto['Cantidad'])?></strong>,
                    <strong>Valor unitario: <?=$this->e($concepto['ValorUnitario'])?></strong>,
                    Descuento: <?=$this->e($concepto['Descuento'] ? : '(ninguno)')?>,
                    <strong>Importe: <?=$this->e($concepto['Importe'])?></strong>
                </p>
                <?php foreach ($concepto->searchNodes('cfdi:InformacionAduanera') as $informacionAduanera) : ?>
                    <p>Pedimento: <?=$this->e($informacionAduanera['NumeroPedimento'])?></p>
                <?php endforeach; ?>
                <?php foreach ($concepto->searchNodes('cfdi:CuentaPedial') as $cuentaPredial) : ?>
                    <p>Cuenta predial: <?=$this->e($cuentaPredial['Numero'])?></p>
                <?php endforeach; ?>
                <?php foreach ($concepto->searchNodes('cfdi:Parte') as $parte) : ?>
                    <p style="padding-left: 5mm">
                        <strong>Parte: <?=$this->e($parte['Descripcion'])?></strong>,
                        <br/>
                        <span>Clave SAT: <?=$this->e($parte['ClaveProdServ'])?>,</span>
                        <span>No identificación: <?=$this->e($parte['NoIdentificacion'] ? : '(ninguno)')?>,</span>
                        <span>Cantidad: <?=$this->e($parte['Cantidad'])?>,</span>
                        <span>Unidad: <?=$this->e($parte['Unidad'] ? : '(ninguna)')?>,</span>
                        <span>Valor unitario: <?=$this->e($parte['ValorUnitario'] ? : '0')?></span>,
                        <span>Importe: <?=$this->e($parte['Importe'] ? : '0')?></span>
                        <?php foreach ($parte->searchNodes('cfdi:InformacionAduanera') as $informacionAduanera) : ?>
                            <br/>Pedimento: <?=$this->e($informacionAduanera['NumeroPedimento'])?>
                        <?php endforeach; ?>
                    </p>
                <?php endforeach; ?>
                <?php foreach ($conceptoTraslados as $impuesto) : ?>
                    <p>
                        <strong>Traslado</strong>
                        Impuesto: <?=$this->e($impuesto['Impuesto'])?>,
                        Base: <?=$this->e($impuesto['Base'])?>,
                        Tipo factor: <?=$this->e($impuesto['TipoFactor'])?>,
                        Tasa o cuota: <?=$this->e($impuesto['TasaOCuota'])?>,
                        <strong>Importe: <?=$this->e($impuesto['Importe'])?></strong>
                    </p>
                <?php endforeach; ?>
                <?php foreach ($conceptoRetenciones as $impuesto) : ?>
                    <p>
                        <strong>Retención</strong>
                        Impuesto: <?=$this->e($impuesto['Impuesto'])?>,
                        Base: <?=$this->e($impuesto['Base'])?>,
                        Tipo factor: <?=$this->e($impuesto['TipoFactor'])?>,
                        Tasa o cuota: <?=$this->e($impuesto['TasaOCuota'])?>,
                        <strong>Importe: <?=$this->e($impuesto['Importe'])?></strong>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php foreach ($pagos as $pago) : ?>
        <?php
        $pagoCounter = $pagoCounter + 1;
        ?>
      <div class="panel">
        <div class="title">Pago: <?=$this->e($pagoCounter)?> de <?=$this->e($pagoCount)?></div>
        <div class="content">
          <p>
            <span><strong>Fecha de pago:</strong> <?=$this->e($pago['FechaPago'])?>,</span>
            <span><strong>Forma de pago:</strong> <?=$this->e($pago['FormaDePagoP'])?>,</span>
            <span><strong>Moneda:</strong> <?=$this->e($pago['MonedaP'])?>,</span>
            <span><strong>Monto:</strong> <?=$this->e($pago['Monto'])?></span>
            <?php if ('' !== $pago['TipoCambioP']) : ?>
              <span><strong>Tipo Cambio:</strong> <?=$this->e($pago['TipoCambioP'])?></span>
            <?php endif; ?>
            <?php if ('' !== $pago['NumOperacion']) : ?>
              <span><strong>Número operación:</strong> <?=$this->e($pago['NumOperacion'])?></span>
            <?php endif; ?>
            <?php if ('' !== $pago['RfcEmisorCtaOrd']) : ?>
              <span><strong>RFC Emisor Cta Ord:</strong> <?=$this->e($pago['RfcEmisorCtaOrd'])?></span>
            <?php endif; ?>
            <?php if ('' !== $pago['NomBancoOrdExt']) : ?>
              <span><strong>Nombre Banco Ord Extranjero:</strong> <?=$this->e($pago['NomBancoOrdExt'])?></span>
            <?php endif; ?>
            <?php if ('' !== $pago['CtaOrdenante']) : ?>
              <span><strong>Cuenta Ord:</strong> <?=$this->e($pago['CtaOrdenante'])?></span>
            <?php endif; ?>
            <?php if ('' !== $pago['RfcEmisorCtaBen']) : ?>
              <span><strong>RFC Emisor Cta Ben:</strong> <?=$this->e($pago['RfcEmisorCtaBen'])?></span>
            <?php endif; ?>
            <?php if ('' !== $pago['CtaBeneficiario']) : ?>
              <span><strong>Cuenta Ben:</strong> <?=$this->e($pago['CtaBeneficiario'])?></span>
            <?php endif; ?>
            <?php if ('' !== $pago['TipoCadPago']) : ?>
              <span><strong>Tipo cadena de pago:</strong> <?=$this->e($pago['TipoCadPago'])?></span>
            <?php endif; ?>
          </p>
            <?php if ('' !== $pago['CertPago']) : ?>
              <p>
                <strong>Certificado de pago:</strong>
                <span><?=$this->e($pago['CertPago'])?></span>
              </p>
            <?php endif; ?>
            <?php if ('' !== $pago['CadPago']) : ?>
              <p>
                <strong>Cadena de pago:</strong>
                <span><?=$this->e($pago['CadPago'])?></span>
              </p>
            <?php endif; ?>
            <?php if ('' !== $pago['SelloPago']) : ?>
              <p>
                <strong>Sello de pago:</strong>
                <span><?=$this->e($pago['SelloPago'])?></span>
              </p>
            <?php endif; ?>
            <?php
            $doctoRelacionados = $pago->searchNodes('pago10:DoctoRelacionado');
            if ($doctoRelacionados->count() > 0) :
                ?>
              <p style="margin: 10px 0 10px 0;">
                <strong>Documentos relacionados</strong>
              </p>
                <?php foreach ($doctoRelacionados as $doctoRelacionado) : ?>
                  <p style="margin-bottom: 10px;">
                    <strong>Id Documento: </strong><span><?=$this->e($doctoRelacionado['IdDocumento'])?></span>
                    <strong>Serie: </strong><span><?=$this->e($doctoRelacionado['Serie'])?></span>
                    <strong>Folio: </strong><span><?=$this->e($doctoRelacionado['Folio'])?></span>
                    <strong>Moneda DR: </strong><span><?=$this->e($doctoRelacionado['MonedaDR'])?></span>
                    <strong>Tipo de cambio DR: </strong><span><?=$this->e($doctoRelacionado['TipoCambioDR'])?></span>
                    <strong>Método de pago DR: </strong><span><?=$this->e($doctoRelacionado['MetodoDePagoDR'])?></span>
                    <strong>Número parcialidad: </strong><span><?=$this->e($doctoRelacionado['NumParcialidad'])?></span>
                    <strong>Imp pagado: </strong><span><?=$this->e($doctoRelacionado['ImpPagado'])?></span>
                    <strong>Imp saldo insoluto: </strong>
                    <span><?=$this->e($doctoRelacionado['ImpSaldoInsoluto'])?></span>
                    <strong>Imp saldo anterior: </strong><span><?=$this->e($doctoRelacionado['ImpSaldoAnt'])?></span>
                  </p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (null !== $impuestos) : ?>
        <?php
        $traslados = $impuestos->searchNodes('cfdi:Traslados', 'cfdi:Traslado');
        $retenciones = $impuestos->searchNodes('cfdi:Retenciones', 'cfdi:Retencion');
        ?>
        <div class="panel">
            <div class="title">Impuestos</div>
            <div class="content">
                <table style="width: 94%">
                    <tr>
                        <th style="width: 20%">Tipo</th>
                        <th style="width: 20%">Impuesto</th>
                        <th style="width: 20%">Tipo factor</th>
                        <th style="width: 20%">Tasa o cuota</th>
                        <th style="width: 20%">Importe</th>
                    </tr>
                    <?php foreach ($traslados as $impuesto) : ?>
                        <tr>
                            <th>Traslado</th>
                            <td><?=$this->e($impuesto['Impuesto'])?></td>
                            <td><?=$this->e($impuesto['TipoFactor'])?></td>
                            <td><?=$this->e($impuesto['TasaOCuota'])?></td>
                            <td><?=$this->e($impuesto['Importe'])?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php foreach ($retenciones as $impuesto) : ?>
                        <tr>
                            <th>Retención</th>
                            <td><?=$this->e($impuesto['Impuesto'])?></td>
                            <td><?=$this->e($impuesto['TipoFactor'])?></td>
                            <td><?=$this->e($impuesto['TasaOCuota'])?></td>
                            <td><?=$this->e($impuesto['Importe'])?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    <?php endif ?>
    
    <!-- Complemento - Carta Porte -->
    <?php if (null !== $complemento) : ?>
        <!-- Variables de los primeros nodos hijos en la carta porte -->
        <?php $cartaPorte = $complemento -> searchNode('cartaporte20:CartaPorte'); ?>
            <?php if (null !== $cartaPorte) : ?>
            <?php 
                $cartaPorte = $complemento -> searchNode('cartaporte20:CartaPorte');
                $ubicaciones = $cartaPorte -> searchNodes('cartaporte20:Ubicaciones','cartaporte20:Ubicacion');
                $mercancias = $cartaPorte -> searchNodes('cartaporte20:Mercancias','cartaporte20:Mercancia');
                $_mercancias = $cartaPorte -> searchNode('cartaporte20:Mercancias');
                $autotransporte = $_mercancias -> searchNode('cartaporte20:Autotransporte');
                $figuraTransporte = $cartaPorte -> searchNode('cartaporte20:FiguraTransporte');
            ?>
            <div class="panelcp">
                <div class="titlecp">Carta Porte</div>
                <div class="contentcp">
                    
                    <!-- Ubicaciones -->
                    <p class="subtitle">Ubicaciones</p>
                    <table  class="subtable border-subtable">
                        <tr>
                            <th style="width: 10%">Tipo de Ubicacion</th>
                            <th style="width: 10%">RFC</th>
                            <th style="width: 80%">Dirección</th>
                        </tr>
                        <?php foreach ($ubicaciones as $ubicacion) : ?>
                            <tr>
                                <td><?=$this->e($ubicacion['TipoUbicacion'])?></td>
                                <td><?=$this->e($ubicacion['RFCRemitenteDestinatario'])?></td>
                                <?php 
                                    $domicilios = $ubicacion -> searchNodes('cartaporte20:Domicilio');
                                ?>
                                <?php foreach ($domicilios as $domicilio) :?>
                                    <td style="white-space:pre-wrap; word-wrap:break-word">
                                                <?php 
                                                    $longString = $domicilio['Calle'] . " " . $domicilio['NumeroExterior'] . " " . $domicilio['Colonia'] . " " . $domicilio['Municipio'] . " " .$domicilio['Estado'] . " " . $domicilio['CodigoPostal'];
                                                    echo(wordwrap($longString, 70, "<br>", true))
                                                ?>
                                                <!-- <?=$this->e($domicilio['NumeroExterior'])?>
                                                <br />
                                                <?=$this->e($domicilio['Colonia'])?>
                                                <br />
                                                <?=$this->e($domicilio['Municipio'])?>
                                                <br />
                                                <?=$this->e($domicilio['Estado'])?>
                                                <br />
                                                <?=$this->e($domicilio['CodigoPostal'])?> -->
                                                <!-- <?php echo(wordwrap("Avenida del panteon Mz 450 Lt 8 Avenida del panteon Mz 450 Lt 8 Avenida del panteon Mz 450 Lt 8 Avenida del panteon Mz 450 Lt 8", 80, '<br>', true))?> -->
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <!-- Producto / Mercancias -->
                    <p class="subtitle">Mercancias</p>
                    <table class="subtable border-subtable">
                        <tr>
                            <th style="width: 20%">Bienes transportados</th>
                            <th style="width: 30%">Descripcion</th>
                            <th style="width: 10%">Cantidad</th>
                            <th style="width: 10%">Clave de Unidad</th>
                            <th style="width: 10%">Peso en Kg</th>
                            <th style="width: 10%">Material Peligroso</th>
                            <th style="width: 10%">Clave MP</th>
                        </tr>
                        <?php foreach ($mercancias as $mercancia) : ?>
                            <tr>
                                <td><?=$this->e($mercancia['BienesTransp'])?></td>
                                <td><?=$this->e($mercancia['Descripcion'])?></td>
                                <td><?=$this->e($mercancia['Cantidad'])?></td>
                                <td><?=$this->e($mercancia['PesoEnKg'])?></td>
                                <td><?=$this->e($mercancia['PesoEnKg'])?></td>
                                <td><?=$this->e($mercancia['MaterialPeligroso'])?></td>
                                <td><?=$this->e($mercancia['CveMaterialPeligroso'])?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <!-- Seguros  -->
                    <p class="subtitle">Seguro</p>
                    <table class="subtable border-subtable">
                        <tr>
                            <th style="width: 20%">Aseguradora</th>
                            <th style="width: 20%">Poliza</th>
                            <th style="width: 20%">Carga Asegurada</th>
                            <th style="width: 20%">Aseguradora Med. Amb.</th>
                            <th style="width: 20%">Poliza Med. Amb</th>
                        </tr>
                    
                        <tr>
                            <?php 
                                $seguros = $autotransporte -> searchNode('cartaporte20:Seguros');
                            ?>
                            <td><?=$this->e($seguros['AseguraRespCivil'])?></td>
                            <td><?=$this->e($seguros['PolizaRespCivil'])?></td>
                            <td><?=$this->e($seguros['AseguraCarga'])?></td>
                            <td><?=$this->e($seguros['AseguraMedAmbiente'])?></td>
                            <td><?=$this->e($seguros['PolizaMedAmbiente'])?></td>
                        </tr>
                        
                    </table>
                    
                    <!-- Vehiculos  -->
                    <p class="subtitle">Autotransporte</p>
                    <table class="subtable border-subtable">
                        <tr>
                            <th style="width: 20%">Permiso SCT</th>
                            <th style="width: 20%">Numero Permiso</th>
                            <th style="width: 20%">Configuración Vehicular</th>
                            <th style="width: 20%">Placa</th>
                            <th style="width: 20%">Año del modelo</th>
                        </tr>
                    
                        <tr>
                            <td><?=$this->e($autotransporte['PermSCT'])?></td>
                            <td><?=$this->e($autotransporte['NumPermisoSCT'])?></td>
                            <?php 
                                $identificacionVehicular = $autotransporte -> searchNode('cartaporte20:IdentificacionVehicular');
                            ?>
                            <td><?=$this->e($identificacionVehicular['ConfigVehicular'])?></td>
                            <td><?=$this->e($identificacionVehicular['PlacaVM'])?></td>
                            <td><?=$this->e($identificacionVehicular['AnioModeloVM'])?></td>
                        </tr>
                        
                    </table>

                    <!-- Remolques -->
                    <p class="subtitle">Remolques</p>
                    <table class="subtable border-subtable">
                        <tr>
                            <th style="width: 50%">Subtipo</th>
                            <th style="width: 50%">Placas</th>
                        </tr>
                    
                        <tr>
                            <?php 
                                $remolques = $autotransporte -> searchNodes('cartaporte20:Remolques', 'cartaporte20:Remolque');
                            ?>
                            <?php foreach ($remolques as $remolque) : ?>
                                <td><?=$this->e($remolque['SubTipoRem'])?></td>
                                <td><?=$this->e($remolque['Placa'])?></td>
                            <?php endforeach; ?>
                        </tr>
                    </table>

                    <!--  Conductor  -->
                    <p class="subtitle">Conductor</p>
                    <table class="subtable">
                        <tr>
                            <th style="width: 50%">RFC</th>
                            <th style="width: 50%">Número licencia</th>
                        </tr>
                    
                        <tr>
                            <?php 
                                $tiposFigura = $figuraTransporte -> searchNode('cartaporte20:TiposFigura');
                            ?>
                            <td><?=$this->e($tiposFigura['RFCFigura'])?></td>
                            <td><?=$this->e($tiposFigura['NumLicencia'])?></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="panel">
        <div class="title">Totales</div>
        <div class="content">
            <table style="width: 97%">
                <tr>
                    <th style="width: 10%">Moneda</th>
                    <th style="width: 15%">Tipo de cambio</th>
                    <th style="width: 15%">Subtotal</th>
                    <th style="width: 15%">Descuentos</th>
                    <th style="width: 15%">Impuestos trasladados</th>
                    <th style="width: 15%">Impuestos retenidos</th>
                    <th style="width: 15%">Total</th>
                </tr>
                <tr>
                    <td><?=$this->e($comprobante['Moneda'])?></td>
                    <td><?=$this->e($comprobante['TipoCambio'])?></td>
                    <td><?=$this->e($comprobante['SubTotal'])?></td>
                    <td><?=$this->e($comprobante['Descuento'])?></td>
                    <td><?=$this->e($totalImpuestosTrasladados)?></td>
                    <td><?=$this->e($totalImpuestosRetenidos)?></td>
                    <td><?=$this->e($comprobante['Total'])?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="panel">
        <div class="title">Datos fiscales</div>
        <div class="content">
            <table class="tabular">
                <tr>
                    <th>Sello CFDI:</th>
                    <td><?=$this->e(chunk_split($tfd['SelloCFD'], 100))?></td>
                </tr>
                <tr>
                    <th>Sello SAT:</th>
                    <td><?=$this->e(chunk_split($tfd['SelloSAT'], 100))?></td>
                </tr>
                <tr>
                    <th>Cadena TFD:</th>
                    <td><?=$this->e(chunk_split($cfdiData->tfdSourceString(), 100))?></td>
                </tr>
                <tr>
                    <th>Verificación:</th>
                    <td>
                        <a href="<?=$this->e($cfdiData->qrUrl())?>">
                            <?=$this->e(str_replace('?', "\n?", $cfdiData->qrUrl()))?>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</page>
