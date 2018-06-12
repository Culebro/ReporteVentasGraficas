<?php

class Database
{

    private $conexion ;

	public function __construct()
	{
		$host = "172.16.0.70:C:\\Prediction\\BDs\\Prediction.fdb";
		$user="SYSDBA";
		$pass="masterkey";
		$this->conexion = @ibase_pconnect($host,$user,$pass) or die("Error al conectarse a la base de datos: ".ibase_errmsg());
		return $this->conexion;
    }
    
    public function select( $query)
    {
        $executeQuery = ibase_query( $this->conexion, $query);
        $resultados = array( );
        while ( $resultado = ibase_fetch_object( $executeQuery)) {
            array_push( $resultados, $resultado);
        }
        return $resultados;
    }
    public function acumuladoAnual(){
        $queryAcumulado = "SELECT sum(r.IMPORTELINEA + r.DETIVA) as total, extract(year from fecha) as anio
                                        FROM VENTAS r
                                        group by extract(year from fecha)";
        $acumuladoMes = $this->select( $queryAcumulado );

        return $acumuladoMes;
    }

    public function acumuladoFamilia( $condicion ){
                $queryAcumulado = "SELECT sum(r.IMPORTELINEA + r.DETIVA) as total  $condicion[1]
                                            FROM VENTAS r
                                            where true $condicion[0] ORDER BY total DESC";
                                            
        $acumuladoFamilia = $this->select( $queryAcumulado );

        return $acumuladoFamilia;
    }





}
