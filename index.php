<?php

ini_set('memory_limit', '4000M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

use App\Google\BigQuerySingleton as BigQuerySingleton;

if(!isset($_GET['sociedad'])||!isset($_GET['mes'])){

  die('por favor agregar todos los campos');

}
else{

//
$sociedad = $_GET['sociedad'];
$mes = $_GET['mes'];

//
$bigQuery = BigQuerySingleton::instanciate(['projectId'=>'estado-de-resultados-266105']);

  $bseg = $bigQuery->query(
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
    PSWBT, 
    PRCTR, 
    LIFNR, 
    KUNNR, 
    PROJK, 
    DBBLG, 
    ZUONR 
    FROM
    `estado-de-resultados-266105.bseg_2020.bseg_2020_aio`
    WHERE BUKRS = '".$sociedad."' 
    AND CAST(SUBSTR(BUDAT,5,2) AS INT64) = $mes 
    ORDER BY CAST(BUDAT AS INT64);");

    $size = count($bseg);

    echo('INSERT INTO bseg(BUKRS,KOSTL,BLDAT,BUDAT,SGTXT,HKONT,BLART,DMBTR,WRBTR,PSWSL,PSWBT,PROJK,ZUONR)');
    echo('<br>');
    echo('VALUES');


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

      $insertable =  "('".
      $curedLine["BUKRS"].",".
      $curedLine["KOSTL"].",".
      $curedLine["BLDAT"].",".
      $curedLine["BUDAT"].",".
      $curedLine["SGTXT"].",".
      $curedLine["HKONT"].",".
      $curedLine["BLART"].",".
      $curedLine["DMBTR"].",".
      $curedLine["WRBTR"].",".
      $curedLine["PSWSL"].",".
      $curedLine["PSWBT"].",".
      $curedLine["PRCTR"].",".
      $curedLine["LIFNR"].",".
      $curedLine["KUNNR"].",".
      $curedLine["PROJK"].",".
      $curedLine["DBBLG"].",".
      $curedLine["ZUONR"].")";

      if($i!==($size-1)){

        $insertable.=",";

      }
      else{

        $insertable.=";";        

      }

      unset($line);
      unset($bseg[$i]);
      echo($insertable);
      echo('<br/>');
    
    }

}
