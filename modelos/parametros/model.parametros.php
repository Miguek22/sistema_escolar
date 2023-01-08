<?php 

 // Funcion para obtener los parametros de la inasistencia
function obtener_valores_parametros(){

	$resultado_parametros =  "";

	$db = new ConexionDB;

	$conexion = $db->retornar_conexion();

	$sql_parametros = "SELECT * FROM parametros";

	$statement = $conexion->prepare($sql_parametros);

	$statement->execute();
	
	$resultado = $statement->fetch(PDO::FETCH_ASSOC);

	$resultado_parametros = $resultado;

	return $resultado_parametros;
}