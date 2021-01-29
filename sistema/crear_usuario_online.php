<?php

//session_start();







 include "../conection.php";
 if(!empty($_POST))
 {
     $alert = '';
     if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario'])  || empty($_POST['clave']) ) {
       $alert = '<p class="msg_error"> Todos los campos son obligatorios</p>';  # code...
     }
     
     else{
         
        
         $nombre = $_POST['nombre'];
         $email = $_POST['correo'];
         $user = $_POST['usuario'];
         $clave = md5($_POST['clave']);
         //$rol = $_POST['rol'];
         $query = mysqli_query($conection,"SELECT * FROM usuario WHERE usuario = '$user' OR correo = '$email' ");
         $result = mysqli_fetch_array($query);
         if($result > 0){
            $alert = '<p class="msg_error"> El correo o usuario ya existet</p>';
         }else{
             $query_insert = mysqli_query($conection,"INSERT INTO usuario(nombre,correo,usuario,clave,rol) VALUES('$nombre', '$email', '$user', '$clave', 5)") ;
            if ($query_insert) {
                $alert = '<p class="msg_save"> Usuario creado correctamente</p>';
                # code...
            }
            else{
                $alert = '<p class="msg_error"> Error al crear usuario</p>';
            }
         }
     }
 }


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>REGISTRO DE USUARIOS</title>
</head>
<body>
 
	<section id="container">
		<div class="form_registre">

        <h1><i class="fas fa-user-plus"></i> Nuevo Usuario</h1>
        <hr>
        <div class="alert"> <?php  echo isset($alert) ? $alert : '';   ?></div>

        <form action="" method="post">

            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo">
            <label for="nombre">Correo Electronico</label>
            <input type="email" name="correo" id="correo" placeholder="Correo Electronico">
            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" placeholder="Usuario">
            <label for="nombre">Clave</label>
            <input type="password" name="clave" id="clave" placeholder="Clave">
            

            <?php
                $query_rol = mysqli_query($conection, "SELECT * FROM rol");
                mysqli_close($conection);
                $result_rol = mysqli_num_rows($query_rol);
                           
            
            ?>
                      
            <button type="submit"  class="btn_save"><i class="fas fa-sd-card"></i> Guardar Usuario</button>  
            <div class="voler" style="text-align: center;">       
            <a  class="btn_ok textcenter" href="index.php"><i class="fas fa-arrow-circle-left"></i> Volver</a>
            </div>   
        
        </form>

        </div>
	</section>
<?php include "includes/footer.php"?>
</body>
</html>