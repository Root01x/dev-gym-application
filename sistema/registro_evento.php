<?php

session_start();


if ($_SESSION['rol'] != 1) {
    # code...

    header("location: ./");
}



 include "../conection.php";

 if(!empty($_POST)) //validacion de campos vacios
 {
     $alert = '';
     if (empty($_POST['evento']) ||  $_POST['precio'] < 0 || $_POST['capacidad'] <= 0) 
     {
       $alert = '<p class="msg_error"> Todos los campos son obligatorios</p>';  # code...
     }
     else{
         
            $evento         = $_POST['evento'];        
            $precio         = $_POST['precio'];
            $capacidad      = $_POST['capacidad'];
            $direccion      = $_POST["direccion"];
            $rol            = $_POST['rol'];
            $fecha_evento   = date('Y-m-d H:m:s', strtotime($_POST['fecha_ev']));             
            $usuario_id     = $_SESSION['idUser'];
            $foto           = $_FILES['foto'];
            $nombre_foto    = $foto['name'];
            $type           = $foto['type'];
            $url_temp       = $foto['tmp_name'];
            $imgEvento      = 'img_evento.png';

            if ($nombre_foto != '') 
            {
                $destino = 'img/uploads/';
                $img_nombre = 'img_'.md5(date('d-m-Y H:m:s'));
                $imgEvento = $img_nombre.'.jpg';
                $src        = $destino.$imgEvento;
                # code...
            }

            $result = 0;

                $query_insert = mysqli_query($conection,"INSERT INTO evento(descripcion,precio,capMax,direccion,foto,usuario_id,fecha_evento,	id_tipo_seminario) 
                                                      VALUES('$evento', '$precio', '$capacidad','$direccion', '$imgEvento', '$usuario_id', ' $fecha_evento', '$rol')") ;
                
                if ($query_insert) {
                    if ($nombre_foto!='') {
                        move_uploaded_file($url_temp,$src);
                        # code...
                    }
                    $alert = '<p class="msg_save"> Evento Guardado Correctamente</p>';
                    # code...
                }
                else{
                    $alert = '<p class="msg_error"> Error al Guardar Evento</p>';
                }
            

        }
            //mysqli_close($conection);

 }


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>REGISTRO SEMINARIO</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
		<div class="form_registre">

        <h1><i class="fas fa-calendar-plus"></i> Nuevo Seminario</h1>
        <hr>
        <div class="alert"> <?php  echo isset($alert) ? $alert : '';   ?></div>

        <form action="" method="post" enctype="multipart/form-data">
            <label for="evento">Nombre Seminario</label>
            <input type="text" name="evento" id="evento" placeholder="Nombre del Seminario" autofocus>
            <label for="precio">Precio</label>
            <input type="number"  name="precio" id="precio" placeholder="Precio del Evento">
            <label for="nombre">Tipo de Seminario</label>

                <?php
                    $query_rol = mysqli_query($conection, "SELECT * FROM tip_seminario");
                    mysqli_close($conection);
                    $result_rol = mysqli_num_rows($query_rol);
                            

                ?>
                <select name="rol" id="rol">
                    <?php 
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
            <input type="number" name="capacidad" id="capacidad" placeholder="Capacidad del Evento">
            <label for="direccion">Direccion Seminario</label>
            <input type="text" name="direccion" id="direccion" placeholder="Direccion del Seminario">
            <label for="fecha">Fecha Evento</label>
            <input type="datetime-local" name="fecha_ev" id="fecha_ev" placeholder="Fecha del Evento"> 

            <div class="photo">
                    <label for="foto">Foto</label>
                    <div class="prevPhoto">
                    <span class="delPhoto notBlock">X</span>
                    <label for="foto"></label>
                    </div>
                    <div class="upimg">
                    <input type="file" name="foto" id="foto">
                    </div>
                    <div id="form_alert"></div>
            </div>
         
            
            
            <input type="submit" value="Agregar Evento" class="btn_save">
        
        </form>

        </div>
	</section>
<?php include "includes/footer.php"?>
</body>
</html>