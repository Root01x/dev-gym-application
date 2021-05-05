<?php
include "../../conection.php";
	$subtotal 	= 0;
	$iva 	 	= 0;
	$impuesto 	= 0;
	$tl_sniva   = 0;
	$total 		= 0;
	$precio_seminario =0;
	$total_seminario_efectivo = 0;
	$total_seminario_tarjeta = 0;
	$total_seminario_deposito = 0;
	$total_seminarios = 0;
 //print_r($configuracion); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Factura</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php echo $anulada; ?>
<div id="page_pdf">
	<table id="factura_head">
		<tr>
			<td class="logo_factura">
				<div>
					<img src="img/logo.png">
				</div>
			</td>
			<td class="info_empresa">
				<?php
					if($result_config > 0){
						$iva = $configuracion['iva'];
				 ?>
				<div>
					<span class="h2"><?php echo strtoupper($configuracion['nombre']); ?></span>
					<p><?php echo $configuracion['razon_social']; ?></p>
					<p><?php echo $configuracion['direccion']; ?></p>
					<p>RUC: <?php echo $configuracion['cedula']; ?></p>
					<p>Teléfono: <?php echo $configuracion['telefono']; ?></p>
					<p>Email: <?php echo $configuracion['email']; ?></p>
				</div>
				<?php
					}
				 ?>
			</td>
			<td class="info_factura">
				<div class="round">
					<span class="h3">REPORTE GENERAL</span>	
							
					<p>Fecha: <?php 
					$fechaActual = date('d-m-Y');
   
					echo $fechaActual;?></p>
					<p>Hora: <?php 
					date_default_timezone_set('America/Mexico_City');
					$hora = date("H:i:s");;  
					
					echo $hora ?></p>

					<p>Encargado: <?php echo $user2 ?></p>
				</div>
			</td>
		</tr>
	</table>
	<!--
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<span class="h3">DATOS DEL SEMINARIO</span>
					<table class="datos_cliente">
						<tr>
							
							<td><label>Descripcion:</label><p><?php //echo $seminario['descripcion'] ?></p></td>
							<td><label>Precio:</label> <p><?php //echo $seminario['precio'] ?></p></td>
						</tr>
						<tr>
							<td><label>Direccion:</label> <p><?php //echo $seminario['direccion'] ?></p></td>
							<td><label>Fecha:</label> <p><?php //echo $seminario['fecha_evento']; ?></p></td>
						</tr>
					</table>
				</div>
			</td>

		</tr>
	</table>
	-->
	<span class="h3">SEMINARIOS ACTIVOS</span>
	
	<table id="factura_detalle">
			<thead>
				<tr>
					<th width="50px">Cod.</th>
					<th class="textleft">Seminario</th>
					<th class="textright" width="150px">Precio</th>
					<th class="textright" width="150px">Participantes</th>
					<th class="textright" width="150px">Total Recuadado</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">

			<?php

				if($result > 0){
					
					while ($row = mysqli_fetch_assoc($query)){
			 ?>
				<tr>
					<td class="textcenter"><?php echo $row['codevento']; ?></td>
					<td><?php echo $row['descripcion'];?></td>

					
					<td class="textright"><?php echo $row['precio']."$"; ?></td>
					<td class="textright"><?php

					$codevento=$row['codevento'];
					$precio_seminario = $row['precio'];
					$conts = mysqli_query($conection,"SELECT CONCAT(cl.nombre, ' ', cl.apellidos) as cliente, cl.cedula, cl.idcliente, cl.telefono FROM detallefactura df INNER JOIN cliente cl ON df.cod_cliente = cl.idcliente WHERE df.codevento = $codevento");
					$contador = mysqli_num_rows($conts);
					
					
					echo $contador; ?></td>
					<td class="textright"><?php 
					

					if ($contador > 0) {
						
						$total 	 = round($precio_seminario * $contador,2);
						echo $total."$";
					}else {
						echo "0$";
					}
					
					
					?></td>
				</tr>
			<?php
						//$precio_total = $row['precio_total'];
						//$subtotal = round($subtotal + $precio_total, 2);
					}

					
				


				}

				//$impuesto 	= round($subtotal * ($iva / 100), 2);
				//$tl_sniva 	= round($subtotal - $impuesto,2 );
				//$total 		= round($tl_sniva + $impuesto,2);
			?>
			</tbody>
			<tfoot id="detalle_totales">
				
				<tr>
					<td></td>
					<td colspan="3" class="textright"><span>TOTAL EN EFECTIVO :</span></td>
					<td class="textright">
					<span><?php 

						while ($row2 = mysqli_fetch_assoc($query_efectivo)){
							$precio_total2 = $row2['totaltFactura'];
							$total_seminario = round($total_seminario + $precio_total2, 2);					
	
						}
					
						echo $total_seminario."$";?>
					</span></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="3" class="textright"><span>TOTAL POR TARJETA :</span></td>
					<td class="textright"><span><?php

						while ($row2 = mysqli_fetch_assoc($query_tarjeta)){
							$precio_total2 = $row2['totaltFactura'];
							$total_seminario_tarjeta = round($total_seminario_tarjeta + $precio_total2, 2);					

						}
				
						echo $total_seminario_tarjeta."$";?>
					
					
					 </span></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="3" class="textright"><span>TOTAL POR TRANSFERENCIA :</span></td>
					<td class="textright"><span><?php
					
					while ($row2 = mysqli_fetch_assoc($query_deposito)){
						$precio_total2 = $row2['totaltFactura'];
						$total_seminario_deposito = round($total_seminario_deposito + $precio_total2, 2);					

					}
			
					echo $total_seminario_deposito."$";?>			
					 </span></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="3" class="textright"><span>VALOR TOTAL RECAUDADO:</span></td>
					<td class="textright"><span>
					
					<?php
					
					while ($row2 = mysqli_fetch_assoc($query_total)){
						$precio_total2 = $row2['totaltFactura'];
						$total_seminarios = round($total_seminarios + $precio_total2, 2);					

					}
			
					echo $total_seminarios."$";?>
					</span></td>
				</tr>
				
		</tfoot>
	</table>
	<div>
		
	</div>

</div>

</body>
</html>