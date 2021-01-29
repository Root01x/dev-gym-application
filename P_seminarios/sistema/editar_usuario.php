<?php

    session_start();
    if ($_SESSION['rol'] != 1) {  ///validacion de roles
        # code...
        header("location: ./");
    }
    
 include "../conection.php";
 if(!empty($_POST))
 {
     $alert = '';
     if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['nombre']) || empty($_POST['rol'])) {
       $alert = '<p class="msg_error"> Todos los campos son obligatorios</p>';  # code...
     }
     else{
         
         $idusuario = $_POST['id'];
         $nombre    = $_POST['nombre'];
         $email     = $_POST['correo'];
         $user      = $_POST['usuario'];
         $clave     = md5($_POST['clave']);
         $rol       = $_POST['rol'];
         $query     = mysqli_query($conection,"SELECT * FROM usuario 
                                                        WHERE (usuario = '$user' AND idusuario != $idusuario)
                                                        OR (correo = '$email' AND idusuario != $idusuario) ");
         $result    = mysqli_fetch_array($query);
         //$result    = count($result);
         if($result > 0){
            $alert = '<p class="msg_error"> El correo o usuario ya existe</p>';
         }else{
            
            if (empty($_POST['clave'])) {
               $sql_update = mysqli_query($conection,"UPDATE    usuario
                                                      SET       nombre = '$nombre', correo = '$email', usuario = '$user', rol='$rol'
                                                      WHERE     idusuario = $idusuario");
            }
            else{
                $sql_update = mysqli_query($conection,"UPDATE    usuario
                                                      SET       nombre = '$nombre', correo = '$email', usuario = '$user', clave = '$clave', rol='$rol'
                                                      WHERE     idusuario = $idusuario");

            }
            
            
           
            
             if ($sql_update) {
                $alert = '<p class="msg_save"> Usuario Actualizado Correctamente</p>';
                # code...
            }
            else{
                $alert = '<p class="msg_error"> Error al actualizar usuario</p>';
            }
         }
     }
 }

//mostrar datos
if (empty($_REQUEST['id'])) {
    header('Location: lista_usuarios.php');
    mysqli_close($conection);
    # code...
}

$iduser = $_REQUEST['id'];
$sql = mysqli_query($conection,"SELECT u.idusuario, u.nombre, u.correo, u.usuario, (u.rol) AS idrol, (r.rol) as rol
                                FROM usuario u
                                INNER JOIN rol r
                                ON u.rol = r.idrol
                                WHERE idusuario = $iduser AND Status = 1");
mysqli_close($conection);                                
$result_sql = mysqli_num_rows($sql);
if($result_sql == 0){
    header('Location: lista_usuarios.php');

}else{
    $option = '';
    while ($data = mysqli_fetch_array($sql)) {
        $iduser     = $data['idusuario'];
        $nombre     = $data['nombre'];
        $correo     = $data['correo'];
        $usuario    = $data['usuario'];
        $idrol      = $data['idrol'];
        $rol        = $data['rol'];
        
        if ($idrol == 1) {
            $option = '<option value="'.$idrol.'" select>'.$rol.'</option>';
            # code...
        }else if ($idrol == 2) {
            $option = '<option value="'.$idrol.'" select>'.$rol.'</option>';
        }else if ($idrol == 3) {
            $option = '<option value="'.$idrol.'" select>'.$rol.'</option>';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>ACTUALIZAR USUARIOS</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
		<div class="form_registre">

        <h1>ACTUALIZAR USUARIOS</h1>
        <hr>
        <div class="alert"> <?php  echo isset($alert) ? $alert : '';   ?></div>

        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo $iduser;?>">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo" value="<?php echo $nombre; ?>">
            <label for="nombre">Correo Electronico</label>
            <input type="email" name="correo" id="correo" placeholder="Correo Electronico " value="<?php echo $correo; ?>">
            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" placeholder="Usuario" value="<?php echo $usuario; ?>">
            <label for="nombre">Clave</label>
            <input type="password" name="clave" id="clave" placeholder="Clave" value="">
            <label for="nombre">Tipo de Usuario</label>

            <?php
                include "../conection.php";
                $query_rol = mysqli_query($conection, "SELECT * FROM rol");
                mysqli_close($conection);
                $result_rol = mysqli_num_rows($query_rol);
                           
            
            ?>
            <select name="rol" id="rol" class="notItemOne"> 
                <?php 
                 echo $option;
                if ($result_rol >0 ) {
                
                    while ($rol = mysqli_fetch_array($query_rol)) {
                      ?>
                      <option value="<?php echo $rol["idrol"];  ?>"><?php  echo $rol["rol"]  ?></option>
                      <?php
                        # code...
                    }
                       # code...
                   }
                
                ?>
                
                        
            </select>
            <input type="submit" value="Guardar Cambios" class="btn_save">
        
        </form>

        </div>
	</section>
<?php include "includes/footer.php"?>
</body>
</html>