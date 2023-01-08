<?php
 
  include ($absolute_include."vistas/plantillas/head.php"); 
   
  include ($absolute_include."vistas/plantillas/navbar.php"); 
 
?> 

<div class="title_left col-lg-8 col-md-8 col-sm-8 col-xs-8">
        <h4>Notas</h4>
</div>

<!-- boton  para agregar -->

<!-- text para busqueda -->


<div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
                 
            <div class="x_content">

                <center>
            
                <table id="table" class="table table-responsive table-stripped table-bordered nowrap " cellspacing="0" width="64.9%">

                    <thead>
                        <tr>

                            <th class="text-center">Nombre Y Apellido</th>
                            <th class="text-center">Curso</th>
                            <th class="text-center">Materia</th>
                            <th class="text-center">Etapa Escolar</th>
                            <th class="text-center">Año Lectivo</th>
                            <th class="text-center">Calificacion</th>
                            <th colspan = "2" class="text-center">Opciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($notas as $nota): ?>
                        <tr>
                           
                            <td class="text-center"><?php echo $nota['cnombres_persona']. " " . $nota['capellidos_persona']; ?></td>
                            <td class="text-center"><?php echo $nota['cdescripcion_curso']; ?></td>
                            <td class="text-center"><?php echo $nota['cnombre_materia']; ?></td>
                            <td class="text-center"><?php echo $nota['cdescripcion_etapa']; ?></td>
                            <td class="text-center"><?php echo $nota['ndescripcion_anolectivo']; ?></td>
                            <td class="text-center"><?php echo $nota['ncalificacion']; ?></td>

                        </tr>
                    <?php endforeach; ?>

                    </tbody> 
               
                </table>
                </center>

        </div>
</div>



<?php

    include ($absolute_include."vistas/plantillas/footer.php"); 
?>    
