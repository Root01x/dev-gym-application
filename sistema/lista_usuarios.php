<?php

session_start();
if ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2) {
    # code...

    header("location: ../");
}
 include "../conection.php";

?>

<!DOCTYPE html> 
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>LISTA DE USUARIOS</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
		<h1><i class="fas fa-list-alt"></i> Lista de Usuarios</h1>
        <a href="registro_usuarios.php" class="btn_new">Crear Usuario</a>

    <form action="buscar_usuario.php" method="get" class="form_search">
        <input type="text" name="busqueda" id="busqueda" placeholder="Buscar">
        <input type="submit" value="Buscar" class="btn_search" >
    </form>
    <hr>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>

            <?php

            //PAGINADO CCODIGO
           $sql_registre = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM usuario WHERE Status =1");
           $result_registre = mysqli_fetch_array($sql_registre);
           $total_resgistros = $result_registre['total_registro'];
           $por_pagina = 8;

           if (empty($_GET['pagina'])) {
               $pagina =1;
               # code...
           }
           else {
               $pagina = $_GET['pagina'];
           }

           $desde = ($pagina - 1) * $por_pagina;
           $total_paginas = ceil($total_resgistros / $por_pagina);


           $query = mysqli_query($conection,"SELECT u.Idusuario, u.nombre, u.correo, u.usuario, r.rol FROM usuario u 
                                                                                                      INNER JOIN rol r 
                                                                                                      ON u.rol = r.idrol 
                                                                                                      WHERE Status = 1                                                                                                      
                                                                                                      ORDER BY idusuario ASC LIMIT $desde, $por_pagina");
           $result = mysqli_num_rows($query);
           mysqli_close($conection);
           if ($result > 0) {
               while ($data = mysqli_fetch_array($query)) {
           ?>
            <tr>
                <td><?php echo $data["Idusuario"];?></td>
                <td><?php echo $data["nombre"];?></td>
                <td><?php echo $data["correo"];?></td>
                <td><?php echo $data["usuario"];?></td>
                <td><?php echo $data["rol"];?></td>
                <td>
                    <a class="link_edit" href="editar_usuario.php?id=<?php echo $data["Idusuario"];?>">Editar</a>
                    |
                    <?php
                        if ($data["Idusuario"] !=1) {
                            # code...
                        
                    ?>
                    <a class="link_delete" href="eliminar_usuario.php?id=<?php echo $data["Idusuario"];?>">Eliminar</a>

                    <?php
                        }
                    ?>
                </td>
            </tr>

                <?php
                   # code...
               }
               # code...
           }


            ?>
            <table>
               
            </table>
         
           
        </table>


        <div class="paginador">
                    <ul>
                        <?php
                            if ($pagina != 1) {
                              
                            ?>  
                            <li><a href="?pagina=<?php echo 1;?>">|<</a></li>
                            <li><a href="?pagina=<?php echo $pagina-1;?>"><<</a></li>

                            <?php
                                # code...
                            }
                        
                        
                        ?>
                    

                    <?php 
                    
                    for ($i=1; $i <= $total_paginas; $i++) { 

                        if ($i == $pagina) {
                            
                            echo '<li class="pageSelected">'.$i.'</li>';
                            # code...
                        }
                        else {

                            echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
                        # code...
                        }


                    }
                    if ($pagina!=$total_paginas) {
   
                    
                    ?>
                     
                    <li><a href="?pagina=<?php echo $pagina+1;?>">>></a></li>
                    <li><a href="?pagina=<?php echo $total_paginas;?>">>|</a></li>
                        <?php 
                            }
                        
                        ?>
                    </ul>

                </div>
	</section>
<?php include "includes/footer.php"?>
</body>
</html>