<?php
	$total_seminario =0;
	//print_r($_REQUEST);
	//exit;
	//echo base64_encode('2');
	//exit;
	session_start();
	if(empty($_SESSION['active']))
	{
		header('location: ../');
	}

	include "../../conection.php";
	require_once '../pdf/vendor/autoload.php';
	use Dompdf\Dompdf;

	if(empty($_REQUEST['ev']))
	{
		echo "No es posible generar la factura.";
	}else{
		//$codCliente = $_REQUEST['cl'];
		$eventoCodigo = $_REQUEST['ev'];
		$anulada = '';

		$query_config   = mysqli_query($conection,"SELECT * FROM configuracion");
		$result_config  = mysqli_num_rows($query_config);
		if($result_config > 0){
			$configuracion = mysqli_fetch_assoc($query_config);
		}


		//$query = mysqli_query($conection,"SELECT CONCAT(cl.nombre, ' ', cl.apellidos) as cliente, cl.cedula, cl.idcliente, cl.telefono FROM detallefactura df INNER JOIN cliente cl ON df.cod_cliente = cl.idcliente WHERE df.codevento = $eventoCodigo");
		$query = mysqli_query($conection,"SELECT codevento, descripcion, precio FROM evento ");

		$result = mysqli_num_rows($query);

		if($result > 0){

			//$clientes = mysqli_fetch_assoc($query);

			$query_efectivo = mysqli_query($conection,"SELECT totaltFactura FROM factura WHERE status =1");
			$query_tarjeta = mysqli_query($conection,"SELECT totaltFactura FROM factura WHERE status =5");
			$query_deposito = mysqli_query($conection,"SELECT totaltFactura FROM factura WHERE status =3");
			$query_total = mysqli_query($conection,"SELECT totaltFactura FROM factura WHERE status !=2");

			//$seminario = mysqli_fetch_assoc($query_efectivo);
			$cod_semi = $seminario['codevento'];




			ob_start();
		    include(dirname('__FILE__').'/factura.php');
		    $html = ob_get_clean();

			// instantiate and use the dompdf class
			$dompdf = new Dompdf();

			$dompdf->loadHtml($html);
			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('letter', 'portrait');
			// Render the HTML as PDF
			$dompdf->render();
			// Output the generated PDF to Browser

			ob_get_clean();
			$dompdf->stream('factura_'.$cod_semi.'.pdf',array('Attachment'=>0));
			exit;
		}
	}

?>