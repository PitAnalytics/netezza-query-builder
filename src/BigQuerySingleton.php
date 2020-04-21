<?php

namespace App\Google;

use Exception;
use GuzzleHttp\HTTP\Client;
use Google\Cloud\BigQuery\BigQueryClient as BigQueryClient;
class BigQuerySingleton{

    //instancia estatica de bigquery para singleton
    private static $instance=null;

    //clase de bigquery cliente
    private $bigQuery;

    //constructor estatico de singleton
    public static function instanciate($config){

        //de no existir instancia la creamos
        if (!self::$instance instanceof self){

            self::$instance = new self($config);
   
        }

        //de existir la regresamos
       return self::$instance;

    }

    //constructor con clase anidada
    private function __construct($config){
        $this->bigQuery = new BigQueryClient($config);
    }

    //query general con arreglo como resultado
    public function query($query){

        //variable de resultado
        $result=[];

        try{

            //creamos query con string de sql
            $jobConfig = $this->bigQuery->query($query);
            //nuevo ejecutamos trabajo
            $job = $this->bigQuery->startQuery($jobConfig);
            //tomamos resultados
            $queryResults = $job->queryResults();
            //anulamos metadatos del trabajo
            unset($job);

            //iteramos resultados de query y los almacenamos en la respuesta
            foreach ($queryResults as $row) {
                $result[]=$row;
                unset($row);
            }

            //anulamos resultados
            unset($queryResults);

        }

        //tomamos excepcion
        catch(Exception $e){
            die($e);
        }

        //regresamos resultado como estructura de datos
        return $result;
    }

}