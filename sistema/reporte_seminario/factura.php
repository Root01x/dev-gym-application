<?php
	$subtotal 	= 0;
	$iva 	 	= 0;
	$impuesto 	= 0;
	$tl_sniva   = 0;
	$total 		= 0;
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
					<span class="h3">REPORTE</span>
					<p>Seminario: <strong><?php echo 1 ?></strong></p>
					<p>Fecha: <?php echo 2 ?></p>
					<p>Hora: <?php echo 3 ?></p>
					<p>Encargado: <?php echo 4 ?></p>
				</div>
			</td>
		</tr>
	</table>
	<table id="factura_cliente">
		<tr>
			<td class="info_cliente">
				<div class="round">
					<span class="h3">DATOS DEL SEMINARIO</span>
					<table class="datos_cliente">
						<tr>
							
							<td><label>Descripcion:</label><p><?php echo $seminario['descripcion'] ?></p></td>
							<td><label>Precio:</label> <p><?php echo $seminario['precio'] ?></p></td>
						</tr>
						<tr>
							<td><label>Direccion:</label> <p><?php echo $seminario['direccion'] ?></p></td>
							<td><label>Fecha:</label> <p><?php echo $seminario['fecha_evento']; ?></p></td>
						</tr>
					</table>
				</div>
			</td>

		</tr>
	</table>
	<span class="h3">PARTICIPANTES DEL SEMINARIO</span>
	
	<table id="factura_detalle">
			<thead>
				<tr>
					<th width="50px">Cod.</th>
					<th class="textleft">Nombre</th>
					<th class="textright" width="150px">Cedula.</th>
					<th class="textright" width="150px">Telefono</th>
				</tr>
			</thead>
			<tbody id="detalle_productos">

			<?php

				if($result > 0){
					
					while ($row = mysqli_fetch_assoc($query)){
			 ?>
				<tr>
					<td class="textcenter"><?php echo $row['idcliente']; ?></td>
					<td><?php echo $row['cliente'];?></td>
					<td class="textright"><?php echo $row['cedula']; ?></td>
					<td class="textright"><?php echo $row['telefono'];?></td>
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
					<td colspan="3" class="textright"><span>TOTAL EN EFECTIVO :</span></td>
					<td class="textright"><span><?php echo 1; ?></span></td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>TOTAL POR TARJETA :</span></td>
					<td class="textright"><span><?php echo 3; ?></span></td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>TOTAL POR TRANSFERENCIA :</span></td>
					<td class="textright"><span><?php echo 4; ?></span></td>
				</tr>
				<tr>
					<td colspan="3" class="textright"><span>VALOR TOTAL RECAUDADO:</span></td>
					<td class="textright"><span><?php echo 4; ?></span></td>
				</tr>
		</tfoot>
	</table>
	<div>
		
	</div>

</div>

</body>
</html>