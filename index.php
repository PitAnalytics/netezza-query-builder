<?php

ini_set('memory_limit', '6000M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(120);

require_once 'vendor/autoload.php';

use App\Google\BigQuerySingleton as BigQuerySingleton;

if(!isset($_GET['sociedad'])||!isset($_GET['mes'])||!isset($_GET['dia'])){

  die('por favor agregar todos los campos');

}
else{
////
$sociedad = $_GET['sociedad'];
$anualidad = $_GET['anualidad'];
$mes = $_GET['mes'];
$dia =$_GET['dia'];
$seccion = intval($_GET['seccion']);
////
$bigQuery = BigQuerySingleton::instanciate(['projectId'=>'estado-de-resultados-266105']);
////
$query =
"SELECT 
  BUKRS, 
  KOSTL, 
  BLDAT, 
  BUDAT, 
  SGTXT, 
  HKONT, 
  BLART, 
  DMBTR, 
  WRBTR, 
  PSWSL, 
  PRCTR, 
  LIFNR, 
  KUNNR, 
  PROJK, 
  DBBLG, 
  ZUONR, 
  SHKZG, 
  BELNR 
FROM 
`estado-de-resultados-266105.bseg_2020.bseg_2020_aio` 
WHERE BUKRS = '".$sociedad."' 
AND CAST(SUBSTR(BUDAT,1,4) AS INT64) = ".$anualidad." 
AND CAST(SUBSTR(BUDAT,5,2) AS INT64) = ".$mes." 
AND CAST(SUBSTR(BUDAT,7,2) AS INT64) =  ".$dia." 
ORDER BY CAST(BUDAT AS INT64); ";
////
  $bseg = $bigQuery->query($query);
  $size = count($bseg);

  echo("--"." sociedad: ".$sociedad."<br/>");
  echo("--"." filas: ".$size."<br/>");
  echo("--"." anualidad: ".$anualidad."<br/>");
  echo("--"." mes: ".$mes."<br/>");
  echo("--"." dia: ".$dia."<br/>");

    for($i=0; $i<$size; $i++){

      $o=$i+1;
      $r=$o%$seccion;
      
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

      if($r===1){

        echo("INSERT INTO input_bseg_".$sociedad."(BUKRS,KOSTL,BLDAT,BUDAT,SGTXT,HKONT,BLART,DMBTR,WRBTR,PSWSL,PRCTR,LIFNR,KUNNR,PROJK,DBBLG,ZUONR,SHKZG,BELNR) ");
        echo('<br/>');
    
      }

      $insertable =  "SELECT ".
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
      $curedLine["PRCTR"].",".  //cebe
      $curedLine["LIFNR"].",".  //numero_proveedor
      $curedLine["KUNNR"].",".  //numero_documento
      $curedLine["PROJK"].",".  //pep
      $curedLine["DBBLG"].",".  //referencia
      $curedLine["ZUONR"].",".  //asignacion
      $curedLine["SHKZG"].",".  //tipo_saldo
      $curedLine["BELNR"];      //numero_documento

      echo($insertable);

      if($r===0){

        echo(";");
        echo('<br/>');
        echo("--------------------------------");
        echo('<br/>');

      }
      if($r!==0&&$o===$size){

        echo(";");
        echo('<br/>');
        echo("------------------------------------------------------------------------------------------");
        echo('<br/>');
        echo("------------------------------------------------------------------------------------------");
        echo('<br/>');

      }
      else if($r!==0&&$o!==$size){

        echo(" UNION ALL");
        echo('<br/>');

      }

      unset($line);
      unset($bseg[$i]);
      
    }
    
}
