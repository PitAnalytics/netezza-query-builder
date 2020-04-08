<?php

ini_set('memory_limit', '6000M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(120);

require_once 'vendor/autoload.php';

use App\Google\BigQuerySingleton as BigQuerySingleton;

if(!isset($_GET['sociedad'])||!isset($_GET['mes'])||!isset($_GET['fraccion'])){

  die('por favor agregar todos los campos');

}
else{
////
$sociedad = $_GET['sociedad'];
$mes = $_GET['mes'];
$fraccion =$_GET['fraccion'];
////
$bigQuery = BigQuerySingleton::instanciate(['projectId'=>'estado-de-resultados-266105']);
////

$diaMinimo = "1";
$diaMaximo = "5";

switch ($fraccion) {
  case '0':
    $diaMinimo = "1";
    $diaMaximo = "5";
    break;
  case '1':
    $diaMinimo = "6";
    $diaMaximo = "10";
    break;
  case '2':
    $diaMinimo = "11";
    $diaMaximo = "15";
    break;
  case '3':
    $diaMinimo = "16";
    $diaMaximo = "20";
    break;
  case '4':
    $diaMinimo = "21";
    $diaMaximo = "25";
    break;
  case '5':
    $diaMinimo = "26";
    $diaMaximo = "31";
    break;
  default:
    $diaMinimo = "1";
    $diaMaximo = "5";
    break;
}

$query ="SELECT BUKRS,KOSTL,BLDAT,BUDAT,SGTXT,HKONT,BLART,DMBTR,WRBTR,PSWSL,PSWBT,PRCTR,LIFNR,KUNNR,PROJK,DBBLG,ZUONR,SHKZG,BELNR 
FROM 
`estado-de-resultados-266105.bseg_2020.bseg_2020_aio` 
WHERE BUKRS = '".$sociedad."' 
AND CAST(SUBSTR(BUDAT,5,2) AS INT64) = ".$mes." 
AND CAST(SUBSTR(BUDAT,7,2) AS INT64) BETWEEN  ".$diaMinimo." AND ".$diaMaximo." 
ORDER BY CAST(BUDAT AS INT64); ";
////
  $bseg = $bigQuery->query($query);
    $size = count($bseg);

    echo("--".$size."<br/>"." filas");

    for($i=0; $i<$size; $i++){

      $line = $bseg[$i];
      $curedLine=[];

      foreach ($line as $key => $value) {

        if($value===''||!isset($value)){
          $curedLine[$key]="null";
        }
        else{
          $curedLine[$key]="'".$value."'";
        }

      }

      unset($line);

      $insertable =  "(".
      $curedLine["BUKRS"].",".  //sociedad
      $curedLine["KOSTL"].",".  //ceco
      $curedLine["BLDAT"].",".  //fecha_documento
      $curedLine["BUDAT"].",".  //fecha_base
      $curedLine["SGTXT"].",".  //texto
      $curedLine["HKONT"].",".  //cuenta
      $curedLine["BLART"].",".  //tipo_documento
      $curedLine["DMBTR"].",".  //monto_base
      $curedLine["WRBTR"].",".  //monto_documento
      $curedLine["PSWSL"].",".  //moneda_documento
      $curedLine["PSWBT"].",".  //tipo_cambio
      $curedLine["PRCTR"].",".  //cebe
      $curedLine["LIFNR"].",".  //numero_proveedor
      $curedLine["KUNNR"].",".  //numero_documento
      $curedLine["PROJK"].",".  //pep
      $curedLine["DBBLG"].",".  //referencia
      $curedLine["ZUONR"].",".  //asignacion
      $curedLine["SHKZG"].",".  //tipo_saldo
      $curedLine["BELNR"].");"; //numero_documento

      unset($line);
      unset($bseg[$i]);
      echo("INSERT INTO bseg(BUKRS,KOSTL,BLDAT,BUDAT,SGTXT,HKONT,BLART,DMBTR,WRBTR,PSWSL,PSWBT,PRCTR,LIFNR,KUNNR,PROJK,DBBLG,ZUONR,SHKZG,BELNR) ");
      echo("VALUES ");
      echo($insertable);
      echo('<br/>');
    
    }

}
