<?php 

    $alert = '';
    session_start();
    if (!empty($_SESSION['active'])) {
        header('location: sistema/');
    }
    
    else{

    if (!empty($_POST))
     {
         if (empty($_POST['usuario']) || empty($_POST['clave'])) {
            $alert = "Ingrese su usuario y clave";
             # code...
         } else{

            require "conection.php";
            $user = mysqli_real_escape_string($conection,$_POST['usuario']);
            $pass = md5(mysqli_real_escape_string($conection,$_POST['clave']));
            $query = mysqli_query($conection,"SELECT * FROM usuario WHERE usuario = '$user' AND clave = '$pass'");
            mysqli_close($conection);
            $result = mysqli_num_rows($query);

        if($result > 0)

            {
                $data = mysqli_fetch_array($query);
                
                $_SESSION['active'] = true;
                $_SESSION['idUser'] = $data['idusuario'];
                $_SESSION['nombre'] = $data['nombre'];
                $_SESSION['email'] = $data['email'];
                $_SESSION['user'] = $data['usuario'];
                $_SESSION['rol'] = $data['rol'];
                header('location: sistema/');
                //print_r($data);
            }else{

                $alert = 'Las credenciales son incorrectas';
                session_destroy();
            }

        }
       
        # code...
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login| Sistema de Control de Seminarios</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="shortcut icon" href="img/login.png" type="image/x-icon">

</head>
<body>
    <section id="container">
        <form action="" method="post">
            <h3>Sistema de Control de Gimnacios</h3>
            <img src="img/login.png" alt="Login">
            <div class="centrar-span">
                <span class="span1">Ingresa </span><span class="span2">a tu cuenta</span>
            </div>
            
            <input type="text" name="usuario" placeholder="Usuario">
            <input type="password" name="clave" placeholder="Clave">
            <div class="alert"> <?php echo isset($alert)? $alert : '';?></div>
            <input type="submit" value="INGRESAR">
            <div class="crear_cuenta"><a href="sistema/crear_usuario_online.php"><i class="fas fa-plus-circle"></i> CREAR CUENTA</a></div>
        </form>
    </section>
</body>
</html>