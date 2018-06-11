<?php

require_once $_SERVER['DOCUMENT_ROOT']."/graficas/Database.php";

class  GraficasController
{
    public function acumuladoAnual()
    {
        $datos = new Database;
        return $datos->acumuladoAnual(  );
    }


    public function acumuladoFamilia( $condicicion)
    {
        $arrayOtros = array();
        $itemOtros = new stdClass;
        $datos = new Database;
        $datos = $datos->acumuladoFamilia( $condicicion);
        foreach ($datos as $index => $elemento) {
            if ( isset($elemento->CAMPO) ) { 
                $datos[$index]->CAMPO =  utf8_encode( $elemento->CAMPO );
                if ( $index >= 13) {
                    //Creando un arreglo de los elementos que no formarán parte visual de las gráficas
                    $itemOtros->CAMPO = $datos[$index]->CAMPO;
            
                    $itemOtros->TOTAL = $datos[$index]->TOTAL;
                   array_push( $arrayOtros,   $itemOtros );                    
                    if ($index == 13) {
                        $datos[$index]->CAMPO = "OTROS";
                        $datos[$index]->TOTAL = $elemento->TOTAL;
                        
                    } else {
                        $datos[13]->TOTAL += $elemento->TOTAL;
                        unset( $datos[$index]);
                    }
                }
                
            }
        }
        return $datos;
    }


    function creaCondicion( $arrayParametros ){
        $condicicion = "";
        $selectFecha = "";
        extract( $arrayParametros);
        if ( $anio  > 0) {
            $condicicion .=" AND extract(year from fecha) = $anio";
        }
        if ( $mes > 0 ) {
            $condicicion .=" AND extract(month from fecha) = $mes";
        }
        if ( $familia != "") {
            $condicicion .=" AND familia ='$familia' ";
        }
        if ( $subFamilia != "") {
            $condicicion .=" AND subfamilia ='$subFamilia' ";
        }
        if ( $marca != "") {
            $condicicion .=" AND marca ='$marca' ";
        }
        if ( $almacen != "") {
            $condicicion .=" AND almacen ='$almacen' ";
        }

        //AGREGANDO EL GROUP BY
        if( $date != ""){
             $condicicion .="  GROUP BY extract($date FROM fecha) , campo ";

             if ( $date == "month") {
                 $selectFecha .=",extract($date FROM fecha) as mes, $campoAgrupar as campo";
             } else {
                 $selectFecha .=",extract($date FROM fecha) anio, $campoAgrupar as campo";
             }
             
             
        }
        
        return array($condicicion,$selectFecha);
    }

    public function condicionBarraAnual( $arrayParametros )
    {
        $condicicion = "";
        $selectFecha = "";
        extract( $arrayParametros);
        if ( $anio  > 0) {
            $condicicion .=" AND extract(year from fecha) = $anio";
        }
        if ( $mes > 0 ) {
            $condicicion .=" AND extract(month from fecha) = $mes";
        }
        if ( $familia != "") {
            $condicicion .=" AND familia ='$familia' ";
        }
        if ( $subFamilia != "") {
            $condicicion .=" AND subfamilia ='$subFamilia' ";
        }
        if ( $marca != "") {
            $condicicion .=" AND marca ='$marca' ";
        }
        if ( $almacen != "") {
            $condicicion .=" AND almacen ='$almacen' ";
        }

        //AGREGANDO EL GROUP BY
        if( $date != ""){
             $condicicion .="  GROUP BY extract($date FROM fecha) ";

             if ( $date == "month") {
                 $selectFecha .=",extract($date FROM fecha) as mes";
             } else {
                 $selectFecha .=",extract($date FROM fecha) anio";
             }
             
             
        }        
        return array($condicicion,$selectFecha);
    }
}

$grafica = new GraficasController;


$opcion = $_GET['opc'];
$campos = array('marca','familia','subfamilia','almacen');
switch ($opcion) {
    case 'anual':{
        echo json_encode( $grafica->acumuladoAnual() );
        break;
    }
    case 'barra':{
        $parametros = array(
            'anio' => $_GET['anio'],
            'mes' => $_GET['mes'],
            'familia' => $_GET['familia'],
            'subFamilia' => $_GET['subFamilia'],
            'marca' => $_GET['marca'],
            'almacen' => $_GET['sucursal'],
            'date' => $_GET['groupDate'],
            'campoAgrupar' => $_GET['groupField']
        );
        $condicicion = $grafica->condicionBarraAnual( $parametros );
        echo json_encode( $grafica->acumuladoFamilia( $condicicion ) );
        break;
    }
    case 'familia':{
        $parametros = array(
            'anio' => $_GET['anio'],
            'mes' => $_GET['mes'],
            'familia' => $_GET['familia'],
            'subFamilia' => $_GET['subFamilia'],
            'marca' => $_GET['marca'],
            'almacen' => $_GET['sucursal'],
            'date' => $_GET['groupDate'],
            'campoAgrupar' => $_GET['groupField']
        );
        $condicicion = $grafica->creaCondicion( $parametros );
        echo json_encode( $grafica->acumuladoFamilia( $condicicion ) );
        break;
    }         
    default:
       echo $_GET['opc'];
        break;
}