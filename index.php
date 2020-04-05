<?php

ini_set('memory_limit', '4000M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

use App\Google\BigQuerySingleton as BigQuerySingleton;

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
WHERE BUKRS = '5200' 
ORDER BY CAST(BUDAT AS INT64);");

foreach($bseg as $line){

  $insertable =  "('".$line["BUKRS"]."','".$line["BLDAT"]."','".$line["BUDAT"]."','".$line["SGTXT"]."','".$line["HKONT"]."','".$line["BLART"]."','".$line["DMBTR"]."','".$line["WRBTR"]."','".$line["PSWSL"]."','".$line["PSWBT"]."','".$line["PROJK"]."','".$line["ZUONR"]."),";

  unset($line);

  echo($insertable);

}

