<?php

session_start();

/*
if ($_SESSION['rol'] != 1) {
    # code...

    header("location: ./");
}*/



 include "../conection.php";

 if(!empty($_POST)) //validacion de campos vacios
 {
     $alert = '';
     if (empty($_POST['evento']) ||  $_POST['precio'] < 0 || $_POST['capacidad'] <= 0 || empty($_POST['id']) || empty($_POST['foto_actual']) || empty($_POST['foto_remove'])) 
     {
       $alert = '<p class="msg_error"> Todos los campos son obligatorios</p>';  # code...
     }
     else{
         
            $codevento = $_POST['id'];
            $evento     = $_POST['evento'];        
            $precio     = $_POST['precio'];
            $capacidad  = $_POST['capacidad'];

            $fecha_evento = date('Y-m-d H:m:s', strtotime($_POST['fecha_ev'])); 
            
            $imgEvento = $_POST['foto_actual'];
            $imgRemove = $_POST['foto_remove'];

            $foto       = $_FILES['foto'];
            $nombre_foto = $foto['name'];
            $type       = $foto['type'];
            $url_temp   = $foto['tmp_name'];
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
                                                         SET descripcion = '$evento', 
                                                             precio = $precio, 
                                                             capMax = $capacidad, 
                                                             foto = '$imgEvento',
                                                             fecha_evento = '$fecha_evento'
                                                         WHERE codevento = $codevento   
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
     $query_evento = mysqli_query($conection,"SELECT * FROM evento WHERE codevento = $id_evento AND status = 1");
     $result_evento = mysqli_num_rows($query_evento);
     
     $foto = '';
     $classRemove = 'notBlock';

     if ($result_evento > 0) {
         $data_evento = mysqli_fetch_assoc($query_evento);
            if ($data_evento['foto'] != 'img_evento.png') {
              $classRemove = '';
              $foto = '<img id="img" src="img/uploads/'.$data_evento['foto'].'" alt="Evento">';  # code...
            }
         # code...
     }else {
        header("location: lista_eventos.php");# code...
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

            <label for="evento">Nombre Evento</label>
            <input type="text" name="evento" id="evento" placeholder="Nombre del Evento" value="<?php echo $data_evento['descripcion'];?>">
            <label for="precio">Precio</label>
            <input type="number" name="precio" id="precio" placeholder="Precio del Evento" value="<?php echo $data_evento['precio'];?>">
            <label for="capacidad">Capacidad Maxima</label>
            <input type="number" name="capacidad" id="capacidad" placeholder="Capacidad del Evento" value="<?php echo $data_evento['capMax'];?>">
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