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
     if (empty($_POST['nombre']) || empty($_POST['cedula']) || empty($_POST['correo']) ||  empty($_POST['telefono']) || empty($_POST['direccion'])) 
     {
       $alert = '<p class="msg_error"> Todos los campos son obligatorios</p>';  # code...
     }
     else{
         
            $cedula     = $_POST['cedula'];        
            $nombre     = $_POST['nombre'];
            $email      = $_POST['correo'];
            $telefono   = $_POST['telefono'];            
            $direccion  = $_POST['direccion'];
            $codTarjeta = $_POST['cod_tarjeta'];
            $usuario_id = $_SESSION['idUser'];

            $result = 0;

            if (is_numeric($cedula)) 
            {
   
                $query      = mysqli_query($conection,"SELECT * FROM cliente WHERE (cedula = '$cedula' or cod_tarjeta = '$codTarjeta') and status=1"); # code...
                $result     = mysqli_fetch_array($query);
            }

            if ($result > 0) 
            {
                $alert = '<p class="msg_error"> EL NUMERO DE CEDULA O CODIGO DE TARJETA YA ESTA EN USO</p>';
            }

            else {

                $query_insert = mysqli_query($conection,"INSERT INTO cliente(cedula,nombre,Correo,telefono,direccion,usuario_id,cod_tarjeta) 
                                                      VALUES('$cedula', '$nombre', '$email', '$telefono', '$direccion', '$usuario_id','$codTarjeta')") ;
                
                if ($query_insert) {
                    $alert = '<p class="msg_save"> Cliente guardado correctamente</p>';
                    # code...
                }
                else{
                    $alert = '<p class="msg_error"> Error al guardar usuario</p>';
                }
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
	<title>REGISTRO CLIENTE</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
		<div class="form_registre">

        <h1><i class="fas fa-user-tag"></i> Registro Cliente</h1>
        <hr>
        <div class="alert"> <?php  echo isset($alert) ? $alert : '';   ?></div>

        <form action="" method="post">
            <label for="cedula">Cedula</label>
            <input type="text" name="cedula" id="cedula" placeholder="Numero de Cedula">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo">
            <label for="correo">Correo Electronico</label>
            <input type="email" name="correo" id="correo" placeholder="Correo Electronico">
            <label for="telefono">Telefono</label>
            <input type="number" name="telefono" id="telefono" placeholder="Numero de Telefono">
            <label for="direccion">Direccion</label>
            
            <input type="text" name="direccion" id="direccion" placeholder="Direccion Completa">

            <div class="wd30">
                    <label for="">Codigo Tarjeta</label>
                    <input type="text" name="cod_tarjeta" id="cod_tarjeta"  required >
                   

                </div>

                <div class="wd30">
                
                <button class="btn_view" id="btn_refresh" type="button" style="margin: 25px auto 10px auto;" ><i class="fas fa-sync"></i></button>
               

                
                </div>
            
            <input type="submit" value="Agregar Cliente" class="btn_save">
        
        </form>

        </div>
	</section>
<?php include "includes/footer.php"?>
</body>
</html>