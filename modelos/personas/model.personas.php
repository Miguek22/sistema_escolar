<?php 

   
    function buscar_persona($arg_textoabuscar){

        $personas = array();  // creo un array que va a almacenar la informacion de los paises

        $db = new ConexionDB;
        $conexion = $db->retornar_conexion();

        $sql = "SELECT * FROM personas"; // busca todas las personas

        if (!empty($arg_textoabuscar)) {
        
            $sql = $sql." WHERE capellidos_persona LIKE :arg_textoabuscar OR cnombres_persona LIKE :arg_textoabuscar";   // filtra busqueda 

            $arg_textoabuscar="%".TRIM($arg_textoabuscar)."%";
        }

        $sql = $sql." ORDER BY cnombres_persona";  
        
        $statement = $conexion->prepare($sql);
        
        $statement->bindParam(':arg_textoabuscar' , $arg_textoabuscar);  // reemplazo los parametros enlazados 
        
        
        if(!$statement){
            echo "Error al crear el registro";
        }else{
            $statement->execute();
        }

        if (!$statement) {
            // no se encontraron paises
        }
        else {
        
            // reviso el retorno

            while($resultado = $statement->fetch(PDO::FETCH_ASSOC)){

                $personas[] = $resultado;

            }
        }

        // cierro la conexion
        $statement = $db->cerrar_conexion($conexion);

        return $personas;

    }

    function buscar_una_persona($arg_persona_id){

        $persona = array();  // creo un array que va a almacenar la informacion del pais

        $db = new ConexionDB;
        $conexion = $db->retornar_conexion();

        $sql = "SELECT * FROM personas WHERE persona_id =:arg_persona_id";  // busca un solo registor
        
        $statement = $conexion->prepare($sql);
        
        $statement->bindParam(':arg_persona_id' , $arg_persona_id);  // reemplazo los parametros enlazados 
        
        
        if(!$statement){
            echo "Error al crear el registro";
        }else{
            $statement->execute();
        }

        if (!$statement) {
            // no se encontraron paises
        }
        else {
        
            $persona = $statement->fetch(PDO::FETCH_ASSOC);   // porque es un solo resultado

        }

        // cierro la conexion
        $statement = $db->cerrar_conexion($conexion);

        return $persona;

    }


    function insertar_persona($arg_capellidos_persona, $arg_cnombres_persona, $arg_ndni_persona, $arg_ncuil_persona, $arg_cemail_persona, $arg_dfechanac_persona){

        $ultimo_id=0;
        $db = new ConexionDB;
        $conexion = $db->retornar_conexion();
        $sql = "INSERT INTO personas (capellidos_persona, cnombres_persona, ndni_persona, ncuil_persona, cemail_persona, dfechanac_persona) VALUES (:arg_capellidos_persona, :arg_cnombres_persona, :arg_ndni_persona, :arg_ncuil_persona, :arg_cemail_persona, :arg_dfechanac_persona)";
        
        // preparo el sql para enviar   se puede usar prepare   y bindparam 
        $statement = $conexion->prepare($sql);
        
        $statement->bindParam(':arg_capellidos_persona' , $arg_capellidos_persona);  // reemplazo los parametros enlazados
        $statement->bindParam(':arg_cnombres_persona' , $arg_cnombres_persona); 
        $statement->bindParam(':arg_ndni_persona' , $arg_ndni_persona); 
        $statement->bindParam(':arg_ncuil_persona' , $arg_ncuil_persona); 
        $statement->bindParam(':arg_cemail_persona' , $arg_cemail_persona); 
        $statement->bindParam(':arg_dfechanac_persona' , $arg_dfechanac_persona); 
        
        
        if(!$statement){
            echo "Error al crear el registro";
        }else{
            $statement->execute();
        }
        
        $ultimo_id = $conexion->lastinsertid();
       
        // cierro la conexion
        $statement = $db->cerrar_conexion($conexion);

        return $ultimo_id;
    }


    function actualizar_persona($arg_persona_id,$arg_capellidos_persona, $arg_cnombres_persona, $arg_ndni_persona, $arg_ncuil_persona, $arg_cemail_persona, $arg_dfechanac_persona){

       
        $db = new ConexionDB;
        $conexion = $db->retornar_conexion();

        $sql = "UPDATE personas set capellidos_persona = :arg_capellidos_persona, cnombres_persona = :arg_cnombres_persona, ndni_persona = :arg_ndni_persona, ncuil_persona = :arg_ncuil_persona, cemail_persona = :arg_cemail_persona, dfechanac_persona = :arg_dfechanac_persona WHERE persona_id = :arg_persona_id";
        
        
        // preparo el sql para enviar   se puede usar prepare   y bindparam 
        $statement = $conexion->prepare($sql); 

        $statement->bindParam(':arg_persona_id' , $arg_persona_id);  // reemplazo los parametros enlazados
        $statement->bindParam(':arg_capellidos_persona' , $arg_capellidos_persona); 
        $statement->bindParam(':arg_cnombres_persona' , $arg_cnombres_persona); 
        $statement->bindParam(':arg_ndni_persona' , $arg_ndni_persona); 
        $statement->bindParam(':arg_ncuil_persona' , $arg_ncuil_persona); 
        $statement->bindParam(':arg_cemail_persona' , $arg_cemail_persona);
        $statement->bindParam(':arg_dfechanac_persona' , $arg_dfechanac_persona);

        if(!$statement){
            echo "Error al crear el registro";
        }else{
            $statement->execute();
        }

        // cierro la conexion
        $statement = $db->cerrar_conexion($conexion);

    }
    
    
    function eliminar_persona($arg_persona_id){

       
        $db = new ConexionDB;
        $conexion = $db->retornar_conexion();

        $sql = "DELETE FROM personas WHERE persona_id = :arg_persona_id";
        
        // preparo el sql para enviar   se puede usar prepare   y bindparam 
        $statement = $conexion->prepare($sql);
        
        $statement->bindParam(':arg_persona_id' , $arg_persona_id);  // reemplazo los parametros enlazados   
        
        if(!$statement){
            echo "Error al crear el registro";
        }else{
            $statement->execute();
        }

        // cierro la conexion
        $statement = $db->cerrar_conexion($conexion);

    }


?> 