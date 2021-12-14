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
	<title>Nueva Recarga</title>
</head>
<body>
 <?php include "includes/header.php"?>
	<section id="container">
        <div class="title_page"><h1><i class="fas fa-check-circle"></i> Nueva Recarga</h1></div>

        <hr>
		
        <div class="datos_cliente">
            <div class="action_cliente">
                <h4>DATOS DEL CLIENTE:</h4>
                <a href="registrar_usuario_cliente.php" target="_blank" class="btn_new btn_new_cliente3"> <i class="fas fa-plus"> </i> Nuevo Cliente</a>

            </div>
            <form name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos" action="">
                
                <input type="hidden" name="action" value="addCliente">
                <input type="hidden" id="idcliente" name="idcliente" value="" required> 
                <div class="wd30">
                    <label for="">Cedula</label>
                    <input type="number" name="nit_cliente2" id="nit_cliente">
                
                </div>
                <div class="wd30">
                    <label for="">Nombre</label>
                    <input type="text" name="nom_cliente" id="nom_cliente" disabled required>
                
                </div>
                <div class="wd30">
                    <label for="">Telefono</label>
                    <input type="number" name="tel_cliente" id="tel_cliente" disabled required>
                
                </div>
                <div class="wd100">
                    <label for="">Correo</label>
                    <input type="email" name="correo_cliente" id="correo_cliente" disabled required>
                
                </div>
                <div class="wd100">
                    <label for="">Direccion</label>
                    <input type="text" name="dir_cliente" id="dir_cliente" disabled required>
                
                </div>
 
                
                <div class="wd30">
                    <label for="">Codigo Tarjeta</label>
                    <input type="text" name="cod_tarjeta" id="cod_tarjeta" disabled required >
                   

                </div>

                <div class="wd30">
                
                <button class="btn_view inactive" id="btn_refresh" type="button" style="margin: 25px auto 10px auto;" disabled><i class="fas fa-sync"></i></button>
                <div class="alert alertErrorEvento"></div>

                
                </div>
                <div class="wd30">
                    
                </div>
                <div class="wd30">
                
                </div>
             
                
                <div id="div_registro_cliente" class="wd100">
                    <button type="submit" class="btn_save" ><i class="far fa-save fa-lg"></i> Guardar</button>
                
                </div>
                
                            
               

                
            </form>
        </div>
        <div class="datos_venta">
            <h4>DETALLE DE RECARGAS:</h4>
            <div class="datos">
                <div class="wd100">
                <table class="tbl_venta">
                    <thead>
                        
                        <tr>
                            <th style="color:white" class="borderRadiusleft">Plan</th>
                            <th colspan="2">Numero de Accesos</th>
                        
                            <th class="textright//">Duracion en Dias</th>
                            <th>Precio</th>
                            <th class="borderRadiusRight">Accion</th>
                        </tr>
                    </thead>
                    <tbody id="detalle_venta-2">
                        <!-- CONTINO AJAX  -->

                        

                    </tbody>
                    
                    <tfoot id="detalle_totales-2">
                           
                        <!-- CONTINO AJAX  -->
                    
                    </tfoot>
                </table>            
                
                </div>
                
                <div class="wd100">
                    <label for="" Acciones></label>
                    <div class="centrar-contenido" id="acciones_venta">
                      
                        <a href="#" class="btn_new textcenter" id="btn_procesar_recarga" style="display: none;"><i class="far fa-edit"></i> Confirmar Recarga</a>
                        <a href="#" class="btn_ok textcenter" id="btn_anular_recargar" style="background:tomato"><i class="fas fa-ban"></i> Anular</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="datos_venta">
            <h4>RECARGA PERSONALIZADA:</h4>
            <div class="datos">
            <div class="wd30">
                    <label for="">Cantidad de Accesos</label>
                    <input type="number" name="cant_accesos" id="cant_accesos">
                
                </div>
                <div class="wd30">
                    <label for="">Duracion en Dias</label>
                    <input type="number" name="num_dias" id="num_dias" >
                
                </div>
                
                <div class="wd50">
                <!-- <a href="#" class="btn_ok textcenter" id="btn_recargar_tarjeta" > Agregar</a> -->
                <a href="#" class="btn_ok textcenter" id="add_recarga_detalle"  > Agregar</a>
                
                </div>
            </div>
        </div>
        <div class="datos_venta">
            <h4>PLANES</h4>
            <div class="datos">
                <div class='wrapper'>
                    <div class='package'>
                        <div class='name'>Regular</div>
                        <div class='price'>$70</div>
                        <div class='trial'>3 meses</div>
                        <hr>
                        <ul>
                        <li>
                            <strong>90</strong>
                            accesos al gym
                        </li>
                        <li>
                            <strong>Vence</strong>
                            en 3 meses
                        </li>
                        <li>
                            <strong>Gratis</strong>
                            bebidas
                        </li>
                        </ul>
                        <a href="#" class="btn_ok textcenter" onclick="event.preventDefault(); add_planes(3,90,90);" style="padding:10px; margin-top:2rem"> Seleccionar</a>

                    </div>
                    <div class='package brilliant'>
                        <div class='name'>Pro</div>
                        <div class='price'>$100</div>
                        <div class='trial'>6 meses</div>
                        <hr>
                        <ul>
                        <li>
                            <strong>200</strong>
                            Accesos al gym
                        </li>
                        <li>
                            <strong>Vence</strong>
                            en 6 meses
                        </li>
                        <li>
                            <strong>Gratis bebidas</strong>
                            
                        </li>
                        <li>
                            Acceso a sala vip
                        </li>
                        
                        </ul>
                        <a href="#" class="btn_ok textcenter" onclick="event.preventDefault(); add_planes(2,200,180);" style="padding:10px"> Seleccionar</a>

                    </div>
                    <div class='package'>
                        <div class='name'>Basico</div>
                        <div class='price'>$30</div>
                        <div class='trial'>30 dias</div>
                        <hr>
                        <ul>
                        <li>
                            <strong>30</strong>
                            accesos al gym
                        </li>
                        <li>
                            <strong>Vence</strong>
                            en 30 dias
                        </li>
                        
                        </ul>
                        <a href="#" class="btn_ok textcenter" onclick="event.preventDefault(); add_planes(4,30,30);" style="padding:10px; margin-top:4rem"> Seleccionar</a>

                    </div>
                    </div>
                </div>
            
        </div>
                 

                

      
	</section>
<?php include "includes/footer.php"?>
<script type="text/javascript">

    $(document).ready(function(){
        
        var usuario_id = '<?php echo $_SESSION['idUser']; ?>';
        serchForDetalleRecarga(usuario_id);
    })

</script>
</body>
</html>