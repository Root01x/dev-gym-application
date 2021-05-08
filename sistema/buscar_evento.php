<?php

session_start(); 
/*if ($_SESSION['rol'] != 1) {  ///validacion de roles
    # code...
    header("location: ./");
}*/

 include "../conection.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>LISTA DE SEMINARIOS</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">

    <?php
        $busqueda = strtolower($_REQUEST['busqueda']) ; ///funcion para trasnsformar a minucula
        if (empty($busqueda)) 
        {
            header("location: lista_clientes.php");

            # code...
        }

    
    ?>
		<h1>Lista de Seminarios</h1>
        

    <form action="buscar_evento.php" method="get" class="form_search">
        <input type="text" name="busqueda" id="busqueda" placeholder="Buscar" value ="<?php echo $busqueda;?>">
        <input type="submit" value="Buscar" class="btn_search" >
    </form>

        <table>
         <tr>
                <th>ID</th>
                <th>Evento</th>
                <th>Precio</th>
                <th>Disponibilidad</th>
                <th>Direccion</th>
                <th>Tipo de Seminario</th>
                
                
                <th>Fecha Evento</th>
                <th>Foto</th> 
                <th>Acciones</th> 
                <?php
                        if ($_SESSION['rol']==1 || $_SESSION['rol']==2) {
                            # code...
                        
                    ?>   
                                  
                <?php }?>
            </tr>


            <?php

            //PAGINADO CCODIGO
         


           $sql_registre = mysqli_query($conection,"SELECT COUNT(*) AS total_registro   FROM evento 
                                                                                        WHERE 
                                                                                        (
                                                                                        codevento       LIKE '%$busqueda%' OR
                                                                                        descripcion	    LIKE '%$busqueda%' OR
                                                                                        direccion	    LIKE '%$busqueda%' OR
                                                                                        precio	        LIKE '%$busqueda%' OR
                                                                                        capMax	        LIKE '%$busqueda%'                                                                                        
                                                                                        
                                                                                        )
                                                                                        AND
                                                                                        status =1");
           $result_registre = mysqli_fetch_array($sql_registre);
           $total_resgistros = $result_registre['total_registro'];
           $por_pagina = 4;

           if (empty($_GET['pagina'])) {
               $pagina =1;
               # code...
           }
           else {
               $pagina = $_GET['pagina'];
           }

           $desde = ($pagina - 1) * $por_pagina;
           $total_paginas = ceil($total_resgistros / $por_pagina);




        $query = mysqli_query($conection,"SELECT e.codevento, e.descripcion, e.precio,
                                                    e.capMax,e.direccion, e.fecha_evento, e.foto,
                                                    e.id_tipo_seminario as id_tipo_s, t.nombre as tipo_seminario
                                                                    
                                                                    FROM evento e
                                                                    INNER JOIN tip_seminario t
                                                                    ON  e.id_tipo_seminario = t.id_tipo_seminario 
                                                                    WHERE 
                                                                    (
                                                                                                            codevento LIKE '%$busqueda%' OR
                                                                                                            descripcion	  LIKE '%$busqueda%' OR
                                                                                                            precio	  LIKE '%$busqueda%' OR
                                                                                                            capMax	  LIKE '%$busqueda%'
                                                                                                                            ) 
                                                                    AND 
                                                                    status = 1                                                                                                                                                                        
                                                                    ORDER BY e.codevento 
                                                                    DESC LIMIT $desde, $por_pagina");
           
           $result = mysqli_num_rows($query);

           if ($result > 0) {
               while ($data = mysqli_fetch_array($query)) {
                if ($data['foto'] !='img_evento.png') {
                    $foto ='img/uploads/'.$data['foto'];
                }else {
                    $foto ='img/'.$data['foto'];
                }
           ?>
            <tr id="row_<?php echo $data["codevento"];?>">
                <td><?php echo $data["codevento"];?></td>
                <td><?php echo $data["descripcion"];?></td>
                <td><?php echo $data["precio"];?></td>
                <td><?php echo $data["capMax"];?></td>
                <td><?php echo $data["direccion"];?></td>
                <td><?php echo $data["tipo_seminario"];?></td>
                <td><?php echo $data["fecha_evento"];?></td>
                <td class="img_evento"><img src="<?php echo $foto;?>" alt="<?php echo $data["descripcion"];?>"></td>
                <td>
                <?php
                        if ($_SESSION['rol']==1 || $_SESSION['rol']==2) {
                            # code...
                        
                    ?>
                
                

                    <a class="link_edit" href="editar_evento.php?id=<?php echo $data["codevento"];?>">Editar</a>                   
                   
                    |
                   <a class="link_delete del_event" href="#" event="<?php echo $data["codevento"];?>">Eliminar</a>
                    |
                   <a class="link_report rep_event" href="#" event="<?php echo $data["codevento"];?>">Reporte</a>

                    </td>
                    <?php
                        }
                    ?>

                    <?php 
                    
                    if ($_SESSION['rol']==5) { 

                            $usuario = $_SESSION['idUser'];
                        $query2 = mysqli_query($conection,"SELECT c.idcliente as idcliente FROM cliente c INNER JOIN usuario u on c.Correo=u.correo WHERE u.idusuario = $usuario");
        
                        $data2 = mysqli_fetch_assoc($query2);
                        $codcliente    = $data2['idcliente'];

                        $token = md5($_SESSION['idUser']);

                        $coddevent = $data['codevento'];

                        $sql = mysqli_query($conection,"SELECT *
                                                        FROM detalle_temp
                                                        WHERE codevento = $coddevent AND token_user='$token'");
                                                        
                        $sql2 = mysqli_query($conection,"SELECT *
                                                        FROM detallefactura
                                                        WHERE codevento = $coddevent AND cod_cliente= $codcliente");
                        $result_sql = mysqli_num_rows($sql);
                        $result_sql2 = mysqli_num_rows($sql2);


                        if($result_sql==0 && $result_sql2==0)
                        {

                        
                         ?>
                        <div class="div_factura">
                        <button class="btn_view view_factura" fac="<?php echo $data["codevento"]; ?>" onclick="event.preventDefault(); mostrar(<?php echo $data['codevento'].','.$data['capMax'] ?>)" ><i class="far fa-trash-alt"></i> Reservar</button>

                        </div>
                        <?php 

                            }else{
                                
                        ?>
                        <div class="div_factura">
                        <button class="btn_view view_factura inactive" disabled><i class="far fa-trash-alt"></i> Reservar</button>

                        </div>

                    <?php } ?>


               </td>
            </tr>
            <?php 
            }}}            
            ?>
            
            <table>
           
            </table>
         
           
        </table>

        <?php
                    if ($total_resgistros != 0) 
                    {
                        
                        
                    
                
                ?>
                <div class="paginador">
                    <ul>
                        <?php
                            if ($pagina != 1) {
                              
                            ?>  
                            <li><a href="?pagina=<?php echo 1;?>&busqueda=<?php echo $busqueda; ?>">|<</a></li>
                            <li><a href="?pagina=<?php echo $pagina-1;?>&busqueda=<?php echo $busqueda; ?>"><<</a></li>

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

                            echo '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>';
                        # code...
                        }


                    }
                    if ($pagina!=$total_paginas) {
   
                    
                    ?>
                     
                    <li><a href="?pagina=<?php echo $pagina+1;?>&busqueda=<?php echo $busqueda; ?>">>></a></li>
                    <li><a href="?pagina=<?php echo $total_paginas;?>&busqueda=<?php echo $busqueda; ?>">>|</a></li>
                        <?php 
                            }
                        
                        ?>
                    </ul>

                </div>
                <?php  
                    }
                ?>


<?php if ($_SESSION['rol']==5) { ?>                    

<table class="tbl_venta">
<thead>

<h2 style="text-align: center;  color: #0a4661; padding: 10px;
font-size: 20pt;"><i class="fas fa-cart-arrow-down"></i> Historial de Reservas</h2>
    

<tr>
    <th>Codigo</th>
    <th colspan="2">Descripcion</th>
   
    <th class="textright">Precio</th>
    
    <th>Accion</th>
</tr>
</thead>
<tbody id="detalle_venta">
<!-- CONTINO AJAX  -->



</tbody>
<tfoot id="detalle_totales">

<!-- CONTINO AJAX  -->

</tfoot>
</table>
<?php } ?>
	</section>
<?php include "includes/footer.php"?>

<script type="text/javascript">

    $(document).ready(function(){
        var usuario_id = '<?php echo $_SESSION['idUser']; ?>';
        serchForDetalle(usuario_id);
    })

</script>
</body>
</html>