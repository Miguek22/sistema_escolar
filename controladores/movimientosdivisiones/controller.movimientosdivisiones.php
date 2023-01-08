<?php 


// seccion que permite resolver problemas de inclusion de archivos
$carpeta_trabajo="";
$seccion_trabajo="/controladores";

if (strpos($_SERVER["PHP_SELF"] , $seccion_trabajo) >1 ) {
   $carpeta_trabajo=substr($_SERVER["PHP_SELF"],1, strpos($_SERVER["PHP_SELF"] , $seccion_trabajo)-1);  // saca la carpeta de trabajo del sistema
 }


  $absolute_include = str_repeat("../",substr_count($_SERVER["PHP_SELF"] , "/")-1).$carpeta_trabajo; //resuelve problemas de profundidad de carpetas

  if (!empty($carpeta_trabajo)) {
  	$absolute_include = $absolute_include."/";
  	$carpeta_trabajo = "/".$carpeta_trabajo;      
  }
  // fin seccion 


  require $absolute_include.'public/vendor/autoload.php';  // carga el archivo de librerias pdf

  use Spipu\Html2Pdf\Html2Pdf;   // carga la clase y la usa para generar pdf


  include ($absolute_include."config/global.php");   // variables de configuracion
  
  include ($absolute_include."clases/class.conexion.php");   // clase para conexion de base de datos

  include ($absolute_include."administracion/sesion.php") ;

  include ($absolute_include."modelos/log/model.log.php");   // para manejar los log

  include ($absolute_include."modelos/anoLectivos/model.anoslectivos.php");   // se incluye el modelo de ano lectivos
  
  include ($absolute_include."modelos/divisiones/model.divisiones.php");   // se incluye el modelo de alumnos
  
  include ($absolute_include."modelos/cursos/model.curso.php");   // se incluye el modelo de alumnos
  
  include ($absolute_include."modelos/alumnos/model.alumnos.php");   // se incluye el modelo de alumnos
  
  include ($absolute_include."modelos/divisionesAlumnos/model.divisionesalumnos.php");   // se incluye el modelo de alumnos
  
  include ($absolute_include."modelos/movimientosDivisiones/model.movimientosdivisiones.php");   // se incluye el modelo de alumnos


  //verifica si se llamo a una accion determinada en el controlador
  $accion="";
  if (isset($_REQUEST['accion'])) {   // si existe una variable tipo REQUEST que llama a una accion / funcion

  	$accion=$_REQUEST['accion'];
  }

// Se valida si hay alguna accion enviada desde el front-end 
// en caso de que haya enviado la accion de tipo index
// o la accion este vacia
// se mostrara el listado de los alumnos para la asistencia 

  if ( $accion == "" OR $accion=="index" )  {

    // Se llama a la funcion para mostrar una lista de los anos lectivos
    movimientos_divisiones_index();
  }elseif ($accion == "detalle") {

    $token = $_POST['token'];

    if ($token == $_SESSION['token']) {
      movimientos_divisiones_detalle($_POST);
    }else{
     movimientos_divisiones_index();
   }

 }elseif ($accion == "nuevo") {

  $token = $_POST['token'];

  if ($token == $_SESSION['token']) {
    movimientos_divisiones_nuevo();
  }else{
   movimientos_divisiones_index();
 }

}elseif ($accion == "buscar_alumnos_movimientos_division") {

  $datosFormularioPasajeMovimientosDivisiones = $_POST['datosFormularioPasajeMovimientosDivisiones'];

  buscar_alumnos_movimientos_division($datosFormularioPasajeMovimientosDivisiones);
}elseif ($accion == "verificarDivisionPasaje") {

  $datosVerificarDivision =$_POST['datosVerificarDivision'];  
  
  verificarDivisionPasaje($datosVerificarDivision);
}elseif ($accion == "guardarAlumnosDivisionPasaje") {
  $datosAlumnosDivisionPasaje = $_POST['datosAlumnosDivisionPasaje'];

  guardarAlumnosDivisionPasaje($datosAlumnosDivisionPasaje);
}elseif ($accion == "editar") {

  $token = $_POST['token'];

  if ($token == $_SESSION['token']) {
    movimientos_divisiones_editar($_POST);
  }else{
   movimientos_divisiones_index();
 }

}elseif ($accion == "quitarAlumnosDivisionPasaje") {

  quitarAlumnosDivisionPasaje($_POST['datosAlumnosQuitarDivision'], $_POST['datosIdCursoAlumnosQuitarDivision']);
}elseif ($accion == "eliminarAlumnosDivisionPasaje") {

  eliminarAlumnosDivisionPasaje($_POST['datosIdCursoAlumnosEliminarDivision']);
}




// ===============================================================================
// FUNCION QUE INTERACTUAN CON EL MODELO
// ===============================================================================


// Funcion para que el controlador liste los alumnos para la asistencia
function movimientos_divisiones_index(){

  $absolute_include = $GLOBALS['absolute_include'];
  $carpeta_trabajo = $GLOBALS['carpeta_trabajo'];

  $resultAnosLectivos = buscar_Ano_Lectivo_Activo();


  $resultado_divisiones_ano_lectivo = obtener_divisiones_ano_lectivo($resultAnosLectivos['anolectivo_id']);

  include $absolute_include."vistas/movimientosDivisiones/index.php";

}

// Funcion para que el controlador muestre el detalle de la division

function movimientos_divisiones_detalle($arg_POST){
  $absolute_include = $GLOBALS['absolute_include'];
  $carpeta_trabajo = $GLOBALS['carpeta_trabajo'];

  $resultAnosLectivos = buscar_Ano_Lectivo_Activo();

  $curso_id = $arg_POST['curso_id'];

  $resultado_detalle_divisiones = buscar_division_alumnos($resultAnosLectivos['anolectivo_id'] ,$curso_id);

  $resultado_descripcion_curso = buscarCurso($curso_id);
  include $absolute_include."vistas/movimientosDivisiones/detalleMoviemientosDivisionesAlumnos.php";

}

function movimientos_divisiones_nuevo(){

  $absolute_include = $GLOBALS['absolute_include'];
  $carpeta_trabajo = $GLOBALS['carpeta_trabajo'];

  $anio_anterior = date('Y', strtotime('-1 year')) ;

  $resultAnosLectivosAnterior = buscar_Descripcion_Ano_Lectivo($anio_anterior);

  $resultCursos = obtenerCursos();
  include $absolute_include."vistas/movimientosDivisiones/nuevoMovimientosDivisionesAlumnos.php";

}

function buscar_alumnos_movimientos_division($argDatosFormularioPasajeMovimientosDivisiones){

  $argDatosFormularioPasajeMovimientosDivisiones = json_decode($argDatosFormularioPasajeMovimientosDivisiones);
  $idPasajeCurso = $argDatosFormularioPasajeMovimientosDivisiones->idPasajeCurso;
  $idPasajeAnoLectivo = $argDatosFormularioPasajeMovimientosDivisiones->idPasajeAnoLectivo;

  // var_dump($argDatosFormularioPasajeMovimientosDivisiones);
  $result_division_alumnos = buscar_division_alumnos($idPasajeAnoLectivo, $idPasajeCurso);
  if (!empty($result_division_alumnos)) {

    // // obtener ano lectivo siguiente
    // $anio_siguiente = date('Y', strtotime('+1 year')) ;

    $result_ano_lectivo_actual = buscar_Ano_Lectivo_Activo();

    if (!empty($result_ano_lectivo_actual)) {

      // obtener cursos
      $result_cursos = obtenerCursos();

      if (!empty($result_cursos)) {

      // armar tabla

        $resulta_tabla_pasar_division = armar_tabla_movimientos_division($result_division_alumnos, $result_ano_lectivo_actual, $result_cursos);

        $respuesta_obtener_division = array('estado' => true,'mensaje' => $resulta_tabla_pasar_division);

      }else{
        $respuesta_obtener_division = array('estado' => false,'mensaje' => 'ERROR AL CARGAR LOS CURSOS, POR FAVOR CONTACTE CON EL ADMINISTRADOR');

      }
    }else{

      $respuesta_obtener_division = array('estado' => false,'mensaje' => 'EL AÑO LECTIVO SIGUIENTE NO EXISTE, POR FAVOR CONTACTE CON EL ADMINISTRADOR');

    }

  }else{
    $respuesta_obtener_division = array('estado' => false,'mensaje' => 'ESTA DIVISION NO EXISTE, POR FAVOR VERIFIQUE');
  }
  echo json_encode($respuesta_obtener_division);
}


function verificarDivisionPasaje($argDatosVerificarDivision){

  $argDatosVerificarDivision = json_decode($argDatosVerificarDivision);

  $idCursoSiguiente = $argDatosVerificarDivision->idCursoSiguiente;
  $idAnoLectivoSiguiente = $argDatosVerificarDivision->idAnoLectivoSiguiente;

  $result_verificar_division = obtener_divisiones_ano_lectivo_curso($idAnoLectivoSiguiente, $idCursoSiguiente);

  if (!empty($result_verificar_division['mensaje'])) {
    $respuesta_verificar_division = array('estado' => false,'mensaje' => 'ESTA DIVISION YA ESTA CARGADA, POR FAVOR VERIFIQUE');
    
  }else{
    $respuesta_verificar_division = array('estado' => true);

  }

  echo json_encode($respuesta_verificar_division);

}


function guardarAlumnosDivisionPasaje($argDatosAlumnosDivisionPasaje){
  $argDatosAlumnosDivisionPasaje = json_decode($argDatosAlumnosDivisionPasaje);

  $idCursoSiguiente = $argDatosAlumnosDivisionPasaje->idCursoSiguiente;
  $idAnoLectivoSiguiente = $argDatosAlumnosDivisionPasaje->idAnoLectivoSiguiente;
  $idAlumnosCheck = $argDatosAlumnosDivisionPasaje->idAlumnosCheck; 

  // Primero: guardar division

  $result_guardado_division = insertar_division($idAnoLectivoSiguiente, $idCursoSiguiente);

  // var_dump($result_guardado_division);
  if (!$result_guardado_division['estado']) {

    $respuesta_guardado_pasaje_division = array('estado' => false,'mensaje' => $result_guardado_division['mensaje']);

  }else{

  // Segundo: guardar division alumnos

    foreach ($idAlumnosCheck as $key => $id_alumnos) {

      $result_guardado_alumnos_division = insertar_alumnos_division($id_alumnos, $idAnoLectivoSiguiente, $idCursoSiguiente);

      if (!$result_guardado_alumnos_division['estado']) {
        echo json_encode(array('estado' => false,'mensaje' => $result_guardado_alumnos_division['mensaje']));
      }

    }

    $respuesta_guardado_pasaje_division = array('estado' => true,'mensaje' => 'PASAJE DE ALUMNOS REALIZADO CON EXITO!');

  }

  echo json_encode($respuesta_guardado_pasaje_division);

}

function movimientos_divisiones_editar($arg_POST){

  $absolute_include = $GLOBALS['absolute_include'];
  $carpeta_trabajo = $GLOBALS['carpeta_trabajo'];

  $curso_id = $arg_POST['curso_id'];

  $result_ano_lectivo_actual = buscar_Ano_Lectivo_Activo();

  $result_division_alumnos = buscar_division_alumnos($result_ano_lectivo_actual['anolectivo_id'], $curso_id);

  $result_cursos = buscarCurso($curso_id);
  include $absolute_include."vistas/movimientosDivisiones/editarMovimientosDivisionesAlumnos.php";


}


function quitarAlumnosDivisionPasaje($arg_DatosAlumnosQuitarDivision, $arg_IdCursoAlumnosQuitarDivision){


  $datosAlumnosQuitarDivision = json_decode($arg_DatosAlumnosQuitarDivision);
  $idCursoAlumnosQuitarDivision = json_decode($arg_IdCursoAlumnosQuitarDivision);

  $result_ano_lectivo_actual = buscar_Ano_Lectivo_Activo();

  //Borrar a los alumnos 
  foreach ($datosAlumnosQuitarDivision as $key => $id_alumno) {

    $result_eliminacion_alumno_division = eliminar_alumno_division($id_alumno, $result_ano_lectivo_actual['anolectivo_id']);

    if (!$result_eliminacion_alumno_division['estado']) {
      echo json_encode(array('estado' => false,'mensaje' => $result_eliminacion_alumno_division['mensaje']));
      die;
    }

    $respuesta_modificacion_pasaje_division = array('estado' => true,'mensaje' => $result_eliminacion_alumno_division['mensaje']);

  }

// verifico que no haya ningun alumno en la division

  $result_division_alumnos = buscar_division_alumnos($result_ano_lectivo_actual['anolectivo_id'], $idCursoAlumnosQuitarDivision);

  if (empty($result_division_alumnos)) {
  //caso verdadero eliminar registro de divisiones
    $result_eliminacion_division = eliminar_division($result_ano_lectivo_actual['anolectivo_id'], $idCursoAlumnosQuitarDivision);
    if (!$result_eliminacion_division['estado']) {

      $respuesta_modificacion_pasaje_division = array('estado' => true,'mensaje' => $result_eliminacion_division['mensaje']);
      
    }
  }

  echo json_encode($respuesta_modificacion_pasaje_division);


}


function eliminarAlumnosDivisionPasaje($argDatosIdCursoAlumnosEliminarDivision){


  $idCursoAlumnosEliminarDivision = json_decode($argDatosIdCursoAlumnosEliminarDivision);

  $result_ano_lectivo_actual = buscar_Ano_Lectivo_Activo();

  $result_ano_lectivo_actual = $result_ano_lectivo_actual['anolectivo_id'];

// elimino registro tabla de division alumnos
  $result_eliminar_division_alumno = eliminar_curso_division_alumno($idCursoAlumnosEliminarDivision, $result_ano_lectivo_actual);

  if (!$result_eliminar_division_alumno['estado']) {
    
    $respuesta_eliminacion_pasaje_division = array('estado' => false,'mensaje' => $result_eliminar_division_alumno['mensaje']);
  }else{

    // elimino registro de tabla divisiones
    $result_eliminacion_division = eliminar_division($result_ano_lectivo_actual, $idCursoAlumnosEliminarDivision);

    if (!$result_eliminacion_division['estado']) {
      $respuesta_eliminacion_pasaje_division = array('estado' => false,'mensaje' => $result_eliminacion_division['mensaje']);
    }

  }

  $respuesta_eliminacion_pasaje_division = array('estado' => true,'mensaje' => 'DIVISION ELIMINADA CORRECTAMENTE!');

  echo json_encode($respuesta_eliminacion_pasaje_division);
}