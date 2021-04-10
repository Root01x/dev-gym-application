<?php

session_start();


if ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2) {
    # code...

    header("location: ./");
}



 include "../conection.php";

 if(!empty($_POST)) //validacion de campos vacios
 {
     $alert = '';
     if (empty($_POST['evento']) ||  $_POST['precio'] < 0 || $_POST['capacidad'] <= 0 || empty($_POST['id']) || empty($_POST['foto_actual']) || empty($_POST['foto_remove'])) 
     {
       $alert = '<p class="msg_error"> Todos los campos son obligatorios</p>';  # code...
     }
     else{
         
            $codevento      = $_POST['id'];
            $evento         = $_POST['evento'];        
            $precio         = $_POST['precio'];
            $capacidad      = $_POST['capacidad'];
            $direccion      = $_POST["direccion"];
            $id_t_semimario = $_POST['rol'];
           
            $fecha_evento   = date('Y-m-d H:m:s', strtotime($_POST['fecha_ev'])); 
            
            $imgEvento      = $_POST['foto_actual'];
            $imgRemove      = $_POST['foto_remove'];

            $foto           = $_FILES['foto'];
            $nombre_foto    = $foto['name'];
            $type           = $foto['type'];
            $url_temp       = $foto['tmp_name'];
            //print_r($fecha_evento);
           

            $upd  = '';

            if ($nombre_foto != '') 
            {
                $destino = 'img/uploads/';
                $img_nombre = 'img_'.md5(date('d-m-Y H:m:s'));
                $imgEvento = $img_nombre.'.jpg';
                $src        = $destino.$imgEvento;
                # code...
            }else {
                if ($_POST['foto_actual'] != $_POST['foto_remove']) {
                    $imgEvento = 'img_evento.png';
                    # code...
                }
            }

            $result = 0;

                $query_update = mysqli_query($conection,"UPDATE evento
                                                         SET descripcion        = '$evento', 
                                                             precio             = $precio, 
                                                             capMax             = $capacidad,
                                                             direccion          = '$direccion', 
                                                             foto               = '$imgEvento',
                                                             fecha_evento       = '$fecha_evento',
                                                             id_tipo_seminario  = '$id_t_semimario'
                                                         WHERE codevento        = $codevento   
                                                            ") ;
                
                if ($query_update) {
                    if ($nombre_foto != '' && ($_POST['foto_actual'] != 'img_evento.png') || ($_POST['foto_actual'] != $_POST['foto_remove'])) {
                        
                        unlink('img/uploads/'.$_POST['foto_actual']);
                        
                        # code...
                    }
                    if ($nombre_foto!='') {
                        move_uploaded_file($url_temp,$src);
                        # code...
                    }
                    $alert = '<p class="msg_save"> Evento Actualizado Correctamente</p>';
                    # code...
                }
                else{
                    $alert = '<p class="msg_error"> Error al Actualizar Evento</p>';
                }
            

        }
            //mysqli_close($conection);

 }
 //VALIDACIONES
 
 if (empty($_REQUEST['id'])) {
     header("location: lista_eventos.php");
     # code...
 }else {
     $id_evento = $_REQUEST['id'];
     if (!is_numeric($id_evento)) {
     header("location: lista_eventos.php"); 
     # code...
     }
     $query_evento = mysqli_query($conection,"SELECT e.codevento, e.descripcion,e.direccion, e.precio, (e.id_tipo_seminario) AS idrol,
                                                     (t.nombre) AS rol, e.capMax, e.fecha_evento, e.foto  
                                              FROM evento e
                                              INNER JOIN tip_seminario t
                                              ON  e.id_tipo_seminario = t.id_tipo_seminario 
                                              WHERE codevento = $id_evento 
                                              AND status = 1");
     $result_evento = mysqli_num_rows($query_evento);
     
     $foto = '';
     $classRemove = 'notBlock';
     
     if ($result_evento == 0) {
        header("location: lista_eventos.php");# code...
       
     }else{
       
        $data_evento = mysqli_fetch_assoc($query_evento);
        
        $option = '';
           //while ($data = mysqli_fetch_array($query_evento)) {
              
               $idrol      = $data_evento['idrol'];
               $rol        = $data_evento['rol'];
               
               if ($idrol == 1) {
                   $option = '<option value="'.$idrol.'" select>'.$rol.'</option>';
                   # code...
               }else if ($idrol == 2) {
                   $option = '<option value="'.$idrol.'" select>'.$rol.'</option>';
               }
           //}
           
           


           if ($data_evento['foto'] != 'img_evento.png') {
             $classRemove = '';
             $foto = '<img id="img" src="img/uploads/'.$data_evento['foto'].'" alt="Evento">';  # code...
           }
        # code...
       
       
     }
     # code...
 }

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>ACTUALIZAR SEMINARIO</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
		<div class="form_registre">

        <h1><i class="far fa-clock"></i> Actualizar Seminario</h1>
        <hr>
        <div class="alert"> <?php  echo isset($alert) ? $alert : '';   ?></div>

        <form action="" method="post" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?php echo $data_evento['codevento']?>">
            <input type="hidden" id="foto_actual" name="foto_actual" value="<?php echo $data_evento['foto']?>">
            <input type="hidden" id="foto_remove" name="foto_remove" value="<?php echo $data_evento['foto']?>">

            <label for="evento">Nombre Seminario</label>
            <input type="text" name="evento" id="evento" placeholder="Nombre del Evento" pattern="[A-Za-z ]{2,100}" title="Solo se permiten letras!" value="<?php echo $data_evento['descripcion'];?>">
            <label for="precio">Precio</label>
            <input type="number" name="precio" id="precio" placeholder="Precio del Evento" value="<?php echo $data_evento['precio'];?>">
            <label for="evento">Tipo de Seminario</label>

            <?php

                    $query_rol = mysqli_query($conection, "SELECT * FROM tip_seminario");
                    mysqli_close($conection);
                    $result_rol = mysqli_num_rows($query_rol);
                            
            ?>
                <select name="rol" id="rol" class="notItemOne">
                    <?php 
                    echo $option;
                    if ($result_rol >0 ) {
                    
                        while ($rol = mysqli_fetch_array($query_rol)) {
                        ?>
                        <option value="<?php echo $rol["id_tipo_seminario"];  ?>"><?php  echo $rol["nombre"]  ?></option>
                        <?php
                            # code...
                        }
                        # code...
                    }
                    
                    ?>
                    
                            
                </select> 

            <label for="capacidad">Capacidad Maxima</label>
            <input type="number" name="capacidad" id="capacidad" placeholder="Capacidad del Evento" value="<?php echo $data_evento['capMax'];?>">
            <label for="direccion">Direccion Seminario</label>
            <input type="text" name="direccion" id="direccion" placeholder="Direccion del Seminario" value="<?php echo $data_evento['direccion'];?>">
            <label for="fecha">Fecha Evento</label>
            <input type="datetime-local" name="fecha_ev" id="fecha_ev" placeholder="Fecha del Evento" value="<?php echo date('Y-m-d\TH:i', strtotime( $data_evento['fecha_evento']))?>"> 

            <div class="photo">
                    <label for="foto">Foto</label>
                    <div class="prevPhoto">
                    <span class="delPhoto <?php echo $classRemove?>">X</span>
                    <label for="foto"></label>
                    <?php echo $foto ?>
                    </div>
                    <div class="upimg">
                    <input type="file" name="foto" id="foto">
                    </div>
                    <div id="form_alert"></div>
            </div>
         
            
            
            <input type="submit" value="Guardar Cambios" class="btn_save">
        
        </form>

        </div>
	</section>
<?php include "includes/footer.php"?>
</body>
</html>