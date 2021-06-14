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
    <style>
body {font-family: Arial, Helvetica, sans-serif;}

#myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal1 {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation */
.modal-content, #caption {  
  -webkit-animation-name: zoom;
  -webkit-animation-duration: 0.6s;
  animation-name: zoom;
  animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)} 
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)} 
  to {transform:scale(1)}
}

/* The Close Button */
.close1 {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close1:hover,
.close1:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content {
    width: 100%;
  }
}
</style>

	<?php include "includes/scripts.php"?>
	<title>LISTA DE TRANSACCIONES</title>
</head>
<body>
 <?php include "includes/header.php"?>


 

<!-- The Modal -->
<div id="myModal" class="modal1">
  <span class="close1">&times;</span>
  <img class="modal-content" id="img01">
  <div id="caption"></div>
</div>



	<section id="container">
		
        <h1><i class="fas fa-list-alt"></i> Lista de Transacciones</h1>

        <a href="Nueva_transaccion.php" class="btn_new">Nueva Transaccion</a>

    <form action="buscar_transaccion.php" method="get" class="form_search">
        <input type="text" name="busqueda" id="busqueda" placeholder="No. Transaccion">
        <button type ="submit" class="btn_search"><i class="fas fa-search"></i></button>
    </form>
    
    <div>
        <h5>Buscar por Fecha</h5>
        <form action="buscar_transaccion.php" method="get" class="form_search_date">
            <label for="">De: </label>
            <input type="date" name="fecha_de" id="fecha_de" required>
            <label for="">A</label>
            <input type="date" name="fecha_a" id="fecha_a" required>
            <button type="submit" class="btn_view">BUSCAR</button>
        </form>
    

        <table>
            <tr>
                <th>NO.</th>
                <th>Fecha / Hora</th>
                <th>Cliente</th>
                <th>Encargado</th>
                <th>Estado</th>
                <th>Imagen Boucher</th>
                <th class="textright">Valor Total</th>
                <th class="" style="text-align: center">Acciones</th>

                
            </tr>

            <?php

            //PAGINADO CCODIGO
           $sql_registre = mysqli_query($conection,"SELECT COUNT(*) AS total_registro FROM factura WHERE status !=10");
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


           $query = mysqli_query($conection,"SELECT f.nofactura,f.fecha,f.totaltFactura,f.codcliente,f.status,f.img_boucher,
                                                    u.nombre as encargado,
                                                    cl.nombre as cliente,
                                                    f.boucher
                                            FROM factura f
                                            INNER JOIN usuario u
                                            ON f.usuario = u.idusuario
                                            INNER JOIN cliente cl
                                            ON f.codcliente = cl.idcliente
                                            WHERE f.status != 10
                                            ORDER BY f.fecha DESC LIMIT $desde,$por_pagina");
           $result = mysqli_num_rows($query);
           mysqli_close($conection);
           if ($result > 0) {
               while ($data = mysqli_fetch_array($query)) {

                if ($data['img_boucher'] !='img_boucher.png') {
                    $foto ='img/bouchers/'.$data['img_boucher'];
                }else {
                    $foto ='img/'.$data['img_boucher'];  
                }


                    if ($data["status"] == 1 || $data["status"] == 5 || $data["status"] == 3) {
                        $estado = '<span class="pagada">Pagada</span>';
                        # code...
                    }else if ($data["status"] == 6) {
                        $estado = '<span class="pendiente">Pendiente</span>';
                        # code...
                    }
                    
                    else {
                        $estado = '<span class="anulada">Anulada</span>';
                    }
           ?>
            <tr id="row_<?php echo $data["nofactura"];?>">
                <td><?php echo $data["nofactura"];?></td>
                <td><?php echo $data["fecha"];?></td>
                <td><?php echo $data["cliente"];?></td>
                <td><?php echo $data["encargado"];?></td>
                <td class="estado"><?php echo $estado;?></td>
                <td class="img_evento"><img class="open_img" id="myImg" src="<?php echo $foto;?>" alt="boucher"></td>
                <td class="textright"><?php echo '$ '.$data["totaltFactura"]; ?></td>
               
                <td>
                    <div class="div_acciones">
                  

                    <?php 
                        if($data["status"]==1 || $data["status"]==5 || $data["status"]==3  )
                        {

                        
                    ?>
                    <div class="div_factura">

                            <button class="btn_anular anular_factura" fac="<?php echo $data["nofactura"]; ?>" title="Anular"><i class="fas fa-ban"></i></button>
                            <button type="button" class="btn_aprobar inactive"><i class="fas fa-check-circle"></i></button>
                            
                    </div>
                    <?php 
                        }else if($data["status"]==6){
                            
                    ?>
                    <div class="div_factura">

                        <button type="button" class="btn_anular inactive"><i class="fas fa-ban"></i></button>
                        <button class="btn_aprobar aprobar_factura" fac="<?php echo $data["nofactura"]; ?>" title="Aprobar"><i class="fas fa-check-circle"></i></button>
                          

                    </div>

                    <?php }
                    else {
                        ?>
                        
                        <button type="button" class="btn_anular inactive"><i class="fas fa-ban"></i></button>
                        <button type="button" class="btn_aprobar inactive"><i class="fas fa-check-circle"></i></button>

                        <?php
                    }
                    
                    
                    ?>
                    
                    </div>
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