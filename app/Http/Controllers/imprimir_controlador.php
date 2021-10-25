<?php

namespace App\Http\Controllers;

use Debugbar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;
use App\carrito_compra;
use App\nota_de_credito;
use App\nota_de_credito_detalle;
use App\boleta;
use App\factura;
use App\cliente_modelo;
use App\cotizacion;
use App\cotizacion_detalle;
use Mpdf\Mpdf;
use App\servicios_sii\Dte;
use App\servicios_sii\DtePDF;
use App\servicios_sii\EnvioDte;
use App\servicios_sii\File;
use Storage;
use BigFish\PDF417\PDF417; // https://github.com/ihabunek/pdf417-php
use BigFish\PDF417\Renderers\ImageRenderer;
//Revisar también "tecnickcom/tcpdf": "6.2.26" usa libredte
use TCPDF; //Viene como dependencia (require) con sasco/libreDTE
use DateTime;

/*
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
*/

class ClsCar{
    public $descripcion;
    public $cantidad;
    public $pu;
    public $total_item;
}


class imprimir_controlador extends Controller
{
    //INSTALAR LA IMPRESORA POS CON NOMBRE RPT010
    //Y COMPARTIRLA PARA QUE FIGURE EN LA RED

    private function dametotalcarrito()
    {
        //$total=carrito_compra::where('usuarios_id',Session::get('usuario_id'))->sum('total_item');
        $total=(new carrito_compra)->dame_total(Session::get('usuario_id'));
        return $total;
    }


    private function configurarPDF()
    {
            /* NOTA: Por ahora no uso
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            */

            $mpdf = new Mpdf([
                'mode'=>'utf-8',
                'format'=>[80,3000],  //en mm ancho x alto //Configurar la impresora tambien
                'margin_header'=>0,
                'margin_top'=>0,
                'margin_footer'=>0,
                'margin_bottom'=>0,
                'margin_left'=>0,
                'margin_right'=>0,
                'orientation'=>'P',
                ]);

            /* OJO: NO FUNCIONA
            $mpdf->AddFontDirectory(resource_path('mis_letras'));

            $mpdf->fontdata['alfredito']=[
                'R'=>'alfredito.ttf'
            ];
            */

            $mpdf->SetAuthor("Ing. Jesús Tejerina Rivera");
            $mpdf->SetDisplayMode('fullwidth'); //OJO: https://mpdf.github.io/reference/mpdf-functions/setdisplaymode.html
            return $mpdf;

    }

    private function dame_timbre($tipo_doc,$doc_num)
    {
        $donde=base_path('timbre_electronico');
        if($tipo_doc=='33')
        {
            $archivo=base_path().'/xml/generados/facturas/'.$tipo_doc.'_'.$doc_num.'.xml';
            $imagen_ruta=$donde."\\"."factura_".$doc_num.".jpg";
        }
        if($tipo_doc=='39')
        {
            $archivo=base_path().'/xml/generados/boletas/'.$tipo_doc.'_'.$doc_num.'.xml';
            $imagen_ruta=$donde."\\"."boleta_".$doc_num.".jpg";
        }

        if($tipo_doc=='61')
        {
            $archivo=base_path().'/xml/generados/notas_de_credito/'.$tipo_doc.'_'.$doc_num.'.xml';
            $imagen_ruta=$donde."\\"."notacredito_".$doc_num.".jpg";
        }

        if($tipo_doc=='56')
        {
            $archivo=base_path().'/xml/generados/notas_de_debito/'.$tipo_doc.'_'.$doc_num.'.xml';
            $imagen_ruta=$donde."\\"."notadebito_".$doc_num.".jpg";
        }

        try {
            //Cargar el xml enviado
            $doc_xml=file_get_contents($archivo);
            $DTE=new Dte($doc_xml);
            $timbre_texto=$DTE->getTED();
            //$timbre_texto=htmlentities($timbre_texto); //este muestra el codigo xml purito
            //return $timbre_texto;

            //$h='<TED version="1.0"><DD><RE>5483206-0</RE><TD>33</TD><F>27</F><FE>2020-07-23</FE><RR>60803000-K</RR><RSR>---Servicio de Impuestos Internos</RSR><MNT>126999</MNT><IT1>AMORTIGUADOR TRASERO IZQ DER GAS</IT1><CAF version="1.0"><DA><RE>5483206-0</RE><RS>JUANA EUSEBIA TRONCOSO SANCHEZ</RS><TD>33</TD><RNG><D>1</D><H>50</H></RNG><FA>2020-06-17</FA><RSAPK><M>6YpciGcurx9/v98OIrgAdy062Hk+W8LpNXCO0AHGDVTRQ1tayz9wOrw0mFkIwEMYzUe2t1bIArJNqmj7jRMS9Q==</M><E>Aw==</E></RSAPK><IDK>100</IDK></DA><FRMA algoritmo="SHA1withRSA">wXSrFVHqiC/Ieo/EXOFX3yYTgqAh3lCZKbwFMg4qlYPh0N64HUxJMnisSGN41fGWtllw3imP2qPoi8VtSEO5AA==</FRMA></CAF><TSTED>2020-07-23T19:57:34</TSTED></DD><FRMT algoritmo="SHA1withRSA">4/5y5CalqNvUYZvbSybIPZag9B0HP7A24HiFPCNRDC+KLn4YpUDXDJccif4lDJA6kQ+CPVBVkZopLRrVNrzzUA==</FRMT></TED>';
            //Generar el timbre electrónico

            $pdf417=new PDF417();
            //https://github.com/ihabunek/pdf417-php/blob/master/src/PDF417.php
            $pdf417->setColumns(10); //Por defecto el núm de columnas es de 6 lo que abarca un total de 573 caracteres del texto a codificar
            //Entonces le puse 10 (máximo 30) para que cupiera el texto del TED.
            $datos=$pdf417->encode($timbre_texto);
            $render_img = new ImageRenderer([
                'format' => 'jpg'
            ]);
            $imagen = $render_img->render($datos);
            $imagen->save($imagen_ruta);
            return $imagen_ruta;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function damehtml($vista,$tipo_dte,$doc_num)
    {
        if($tipo_dte=='co'){ //cotizaciones
            $cab_cotizacion=cotizacion::where('num_cotizacion',$doc_num)->first();
            if(!is_null($cab_cotizacion)){

                $det_cotizacion=cotizacion_detalle::where('cotizaciones_detalle.id_cotizacion',$cab_cotizacion->id)
                                ->join('repuestos','cotizaciones_detalle.id_repuestos','repuestos.id')
                                ->get();
                if($det_cotizacion->count()>0){
                    $id_cliente=$cab_cotizacion->id_cliente;
                    $cliente=cliente_modelo::where('id',$id_cliente)->first();
                    $html_cotizacion=view($vista,compact('cab_cotizacion','det_cotizacion','cliente'))->render();
                    return $html_cotizacion;
                }else{
                    return false;
                }

            }else{
                return false;
            }

        }


        $d=Session::get('xml');
        switch($tipo_dte){
            case '33':
                $doc=base_path().'/xml/generados/facturas/'.$d;
            break;
            case '39':
                $doc=base_path().'/xml/generados/boletas/'.$d;
            break;
            case '61':
                $doc=base_path().'/xml/generados/notas_de_credito/'.$d;
            break;
            case '56':
                $doc=base_path().'/xml/generados/notas_de_debito/'.$d;
            break;
        }
        //sacamos el detalle del xml
        $dat=file_get_contents($doc);
        $xml=new \SimpleXMLElement($dat, LIBXML_COMPACT);
        $carrito=collect();
        $total=0;
        $total_item=0;
        $neto=0;
        $iva=0;
        $hay_referencia=0;
        $modifica_texto=false;

        if($tipo_dte=='33')
        {
            $id_cliente=factura::where('num_factura',$doc_num)->value('id_cliente');
            $neto=intval($xml->SetDTE->DTE->Documento->Encabezado->Totales->MntNeto);
            $iva=intval($xml->SetDTE->DTE->Documento->Encabezado->Totales->IVA);
            $total=intval($xml->SetDTE->DTE->Documento->Encabezado->Totales->MntTotal);
        }
        if($tipo_dte=='39')
        {
            $id_cliente=boleta::where('num_boleta',$doc_num)->value('id_cliente');
            $neto=intval($xml->SetDTE->DTE->Documento->Encabezado->Totales->MntTotal);
            $iva=round($neto*Session::get('PARAM_IVA'),0);
            $total=round($neto*(1+Session::get('PARAM_IVA')),0);
        }
        if($tipo_dte=='61')
        {

            $id_cliente=nota_de_credito::where('num_nota_credito',$doc_num)->value('id_cliente');
            $hay_referencia=1;
            if(strval($xml->SetDTE->DTE->Documento->Referencia->CodRef)!='2'){
                $neto=intval($xml->SetDTE->DTE->Documento->Encabezado->Totales->MntNeto);
                $iva=intval($xml->SetDTE->DTE->Documento->Encabezado->Totales->IVA);
                $total=intval($xml->SetDTE->DTE->Documento->Encabezado->Totales->MntTotal);
            }else{
                $modifica_texto=true;
                $neto=0;
                $iva=0;
                $total=0;
            }
            $referencia_fecha=strval($xml->SetDTE->DTE->Documento->Referencia->FchRef);
            $referencia_folio=strval($xml->SetDTE->DTE->Documento->Referencia->FolioRef);
            $fecha_emision=strval($xml->SetDTE->DTE->Documento->Encabezado->IdDoc->FchEmis);
            $referencia="Boleta";
            $doku_dte=strval($xml->SetDTE->DTE->Documento->Referencia->TpoDocRef);
            if($doku_dte=='33') $referencia="Factura";
            //2*Boleta N° 5 Anular todo[111*222*333*444*555] //separar ese string para imprimirlo adecuadamente
            $referencia_motivo=strval($xml->SetDTE->DTE->Documento->Referencia->RazonRef);
            $codigo_motivo=strval($xml->SetDTE->DTE->Documento->Referencia->CodRef);

            $texto_modificado=strval($xml->SetDTE->DTE->Documento->Encabezado->Receptor->RznSocRecep);
            $texto_modificado.="*".strval($xml->SetDTE->DTE->Documento->Encabezado->Receptor->GiroRecep);
            $texto_modificado.="*".strval($xml->SetDTE->DTE->Documento->Encabezado->Receptor->DirRecep);
            $texto_modificado.="*".strval($xml->SetDTE->DTE->Documento->Encabezado->Receptor->CmnaRecep);
            $texto_modificado.="*".strval($xml->SetDTE->DTE->Documento->Encabezado->Receptor->CiudadRecep);

        }
        if($tipo_dte=='56')
        {

            $hay_referencia=1; //del xml sacar
        }

        //FALTA: Guia de despacho



        //Construir el detalle del carrito

        if($modifica_texto==true){
            $oCar=new ClsCar();
            $oCar->descripcion=trim(strval($xml->SetDTE->DTE->Documento->Detalle[0]->NmbItem));
            $oCar->cantidad=0;
            $oCar->total_item=0;
            $oCar->pu=0;
            $carrito->push($oCar);
        }else{
            foreach($xml->SetDTE->DTE->Documento->Detalle as $Det){
                $oCar=new ClsCar();
                $oCar->descripcion=trim(strval($Det->NmbItem));
                $oCar->cantidad=intval($Det->QtyItem);
                $oCar->total_item=round(intval($Det->MontoItem)*(1+Session::get('PARAM_IVA')),0);
                $oCar->pu=round($oCar->total_item/$oCar->cantidad,0);
                $carrito->push($oCar);
            }
        }


        $fecha_emision=(string)$xml->SetDTE->DTE->Documento->Encabezado->IdDoc->FchEmis;
        $cliente=cliente_modelo::where('id',$id_cliente)->first();

        $timbre_url=$this->dame_timbre($tipo_dte,$doc_num);
        //FALTA: Poner un IF para saber si se generó correctamente el timbre...
        //este es para boletas y facturas
        if($tipo_dte=='33' || $tipo_dte=='39'){
            $html=view($vista,compact('carrito','total','neto','iva','doc_num','cliente','fecha_emision','hay_referencia','timbre_url'))->render();
        }

        if($tipo_dte=='61'){
            $html=view($vista,compact('carrito','total','neto','iva','doc_num','cliente','fecha_emision','hay_referencia','referencia','referencia_folio','referencia_fecha','referencia_motivo','codigo_motivo','texto_modificado','timbre_url'))->render();
        }
        return $html;
    }

    private function imprimirXML($xml){
        try {
            if($xml!=0){
                $xml_dte=$xml; //desde estadodte.blade
            }else{
                $xml_dte=Session::get('xml'); //desde ventas(bol,fac),nc, nd, gdespa
            }


            if($xml_dte==0){ //
                $estado=['estado'=>'ERROR','mensaje'=>'XML no definido para imprimir o ya fue impreso.'];
                return json_encode($estado);
            }

            $tipo_dte=substr($xml_dte,0,2);
            $num_dte=str_replace(".xml","",substr($xml_dte,3));
            $ruta=$this->donde($tipo_dte,$num_dte);

            $carpeta_xml=$ruta['carpeta_xml'];

            if($carpeta_xml=="00"){
                $estado=['estado'=>'Error Archivo XML','mensaje'=>'No se encontró XML para la impresión ('.$xml_dte.')'];
                return json_encode($estado);
            }


            $xml = base_path().'/xml/generados/'.$carpeta_xml.$xml_dte;
            // Cargar EnvioDTE y extraer arreglo con datos de carátula y DTEs
            $EnvioDte = new EnvioDte();
            $EnvioDte->loadXML(file_get_contents($xml));

            // procesar cada DTEs e ir agregándolo al PDF
            $Caratula = $EnvioDte->getCaratula();
            $Documentos = $EnvioDte->getDocumentos();

            foreach ($Documentos as $DTE) {
                if (!$DTE->getDatos())
                    die('No se pudieron obtener los datos del DTE');

                $pdf = new DtePDF(true); // =false hoja carta, =true papel contínuo (false por defecto si no se pasa)
                $pdf->setFooterText();

                $pdf->setResolucion(['FchResol'=>$Caratula['FchResol'], 'NroResol'=>$Caratula['NroResol']]);
                $pdf->setCedible(false);
                $pdf->agregar($DTE->getDatos(), $DTE->getTED());
                $guardar_pdf=$ruta['donde'].$ruta['archivo'];
                $pdf->Output($guardar_pdf, 'F');
                $estado=['estado'=>'OK','mensaje'=>$ruta['pdf']];

                return json_encode($estado);
            }
        } catch (\Exception $e) {
            $estado=['estado'=>'Error al imprimir XML','mensaje'=>$e->getMessage()];
            return json_encode($estado);
        }



/*
        // directorio temporal para guardar los PDF
        $dir = sys_get_temp_dir().'/dte_'.$Caratula['RutEmisor'].'_'.$Caratula['RutReceptor'].'_'.str_replace(['-', ':', 'T'], '', $Caratula['TmstFirmaEnv']);
        if (is_dir($dir))
            File::rmdir($dir);
        if (!mkdir($dir))
            die('No fue posible crear directorio temporal para DTEs');
*/


        // entregar archivo comprimido que incluirá cada uno de los DTEs
        //File::compress($dir, ['format'=>'zip', 'delete'=>true, 'download'=>false]);



    }

    public function imprimir($xml){
        return $this->imprimirXML($xml);
    }

    public function imprimir_cotizacion($num_cotizacion)
    {
        $ruta=$this->donde('co',$num_cotizacion);

        try{
            $mpdf=$this->configurarPDF();
            $mpdf->SetSubject($ruta['archivo']);
            $html=$this->damehtml($ruta['vista'],'co',$num_cotizacion);
            if($html===false){
                $estado=['estado'=>'ERROR','mensaje'=>'No se pudo generar el PDF'];
                return json_encode($estado);
            }
            $guardar_pdf=$ruta['donde'].$ruta['archivo'];
            $mpdf->WriteHTML($html);
            $mpdf->Output($guardar_pdf,"F"); // OJO: OUTPUT  F: Guardar el PDF  D: Descargar el PDF    I:  InLine Browser
            $estado=['estado'=>'OK','mensaje'=>$ruta['pdf']];
        }catch (\Exception $error){
            $e=$error->getMessage();
            $estado=['estado'=>'ERROR_IMPRESION','mensaje'=>$e];

        }
        return json_encode($estado);
    }

    private function donde($tipo_dte,$doc_num){
        //funciona para imprimirXML, FALTA: Probar para imprimir solo...
        $carpeta_xml="00";
        //$base_pdf=public_path("storage/pdf")."/"; //original
        $base_pdf=base_path('storage/app/public/pdf')."/";
        switch ($tipo_dte)
        {
            case "co" :
                $donde=$base_pdf."cotizaciones/";
                $archivo="cotizacion_".$doc_num.".pdf";
                $vista="impresion.cotizacion";
                $carpeta_xml="---";
                $pdf=asset('storage/pdf/cotizaciones')."/".$archivo;
            break;
            case "39" :
                $donde=$base_pdf."boletas/";
                $archivo="boleta_".$doc_num.".pdf";
                $vista="impresion.boleta";
                $carpeta_xml="boletas/";
                $pdf=asset('storage/pdf/boletas')."/".$archivo;
            break;
            case "33" :
                $donde=$base_pdf."facturas/";
                $archivo="factura_".$doc_num.".pdf";
                $vista="impresion.factura";
                $carpeta_xml="facturas/";
                $pdf=asset('storage/pdf/facturas')."/".$archivo;
            break;
            case "61" :
                $donde=$base_pdf."notas_credito/";
                $archivo="nota_credito_".$doc_num.".pdf";
                $vista="impresion.nota_credito";
                $carpeta_xml="notas_de_credito/";
                $pdf=asset('storage/pdf/notas_credito')."/".$archivo;
            break;
            case "56" :
                $donde=$base_pdf."notas_debito/";
                $archivo="nota_debito_".$doc_num.".pdf";
                $vista="impresion.nota_debito";
                $carpeta_xml="notas_de_debito/";
                $pdf=asset('storage/pdf/notas_debito')."/".$archivo;
            break;
            case "52" :
                $donde=$base_pdf."guias_despacho/";
                $archivo="guia_despacho_".$doc_num.".pdf";
                $vista="impresion.guia_despacho";
                $carpeta_xml="guias_de_despacho/";
                $pdf=asset('storage/pdf/guias_despacho')."/".$archivo;
            break;
            default:
        }

        $rpta=['donde'=>$donde,'archivo'=>$archivo,'vista'=>$vista,'carpeta_xml'=>$carpeta_xml,'pdf'=>$pdf];
        return $rpta;

    }

    private function mike42($d)
    {
        try {
            $nombreImpresora = "RPT010";
            $conector1 = new WindowsPrintConnector($nombreImpresora);
            //$conector2 = new FilePrintConnector("php://stdout");
            $impresora=new Printer($conector1);

            $fila="Imprimiendo ".$d."\n";
            $impresora->text($fila);
            $impresora->setJustification(Printer::JUSTIFY_CENTER);
            //$impresora->setJustification(Printer::JUSTIFY_LEFT);
            //$impresora->setJustification(Printer::JUSTIFY_RIGHT);

            //RECTANGULO
            $imagen1=public_path().'/storage/imagenes/rectangulo.png';
            $rectangulo = EscposImage::load($imagen1, false);
            $impresora->bitImage($rectangulo);
            $impresora->feedReverse(5);
            $impresora->setEmphasis(true);
            //$impresora->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $impresora->setFont(Printer::FONT_A);
            $impresora->text("R.U.T.: 76.881.221-7\n");
            $impresora->text("FACTURA ELECTRÓNICA\n");
            $numfac="N° "."1234567890\n";
            $impresora->text($numfac);
            $impresora->setEmphasis(false);
            //$impresora->selectPrintMode();
            $impresora->setFont();

            //LOGO
            //$imagen=public_path().'/storage/imagenes/logo_pos.png';
            //$logo = EscposImage::load($imagen, false);
            //$impresora->bitImage($logo);



            $impresora->text("Laravel\n");
            $impresora->setTextSize(2, 2);
            $impresora->text("Pancho App\n");
            $impresora->feed(5);
            $impresora -> cut();

        } finally {
            $impresora -> close();
        }
    }


}
