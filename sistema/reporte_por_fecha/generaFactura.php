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


 $fecha_de = '';
 $fecha_a = '';

 

 if (isset($_REQUEST['fecha_de']) || isset($_REQUEST['fecha_a'])) {
     if ($_REQUEST['fecha_de'] == '' || $_REQUEST['fecha_a'] == '') {
         header("location: reporte_general.php");
		 
     }
 }

 if (!empty($_REQUEST['fecha_de']) && !empty($_REQUEST['fecha_a'])) {
     $fecha_de = $_REQUEST['fecha_de'];
     $fecha_a = $_REQUEST['fecha_a'];
     $buscar = '';

     if ($fecha_de > $fecha_a) {
         header("location: reporte_general.php");
         # code...
     }else if($fecha_de ==$fecha_a){
         $where = "fecha LIKE '$fecha_de%'";
         $buscar = "fecha_de = $fecha_de&fecha_a=$fecha_a";
     }else {
         $f_de = $fecha_de.' 00:00:00';
         $f_a = $fecha_a.' 23:59:59';
         $where = "fecha BETWEEN '$f_de' AND '$f_a'";
         $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
     }


	 //$codCliente = $_REQUEST['cl'];
	 //$eventoCodigo = $_REQUEST['ev'];
	 $anulada = '';

	 $query_config   = mysqli_query($conection,"SELECT * FROM configuracion");
	 $result_config  = mysqli_num_rows($query_config);
	 if($result_config > 0){
		 $configuracion = mysqli_fetch_assoc($query_config);
	 }


	 //$query = mysqli_query($conection,"SELECT CONCAT(cl.nombre, ' ', cl.apellidos) as cliente, cl.cedula, cl.idcliente, cl.telefono FROM detallefactura df INNER JOIN cliente cl ON df.cod_cliente = cl.idcliente WHERE df.codevento = $eventoCodigo");
	 $query = mysqli_query($conection," SELECT f.nofactura as codfactura,f.fecha as fechaF,f.totaltFactura as total ,f.codcliente,f.status as estado,
											u.nombre as encargado,
											cl.nombre as cliente
										FROM factura f
										INNER JOIN usuario u
										ON f.usuario = u.idusuario
										INNER JOIN cliente cl
										ON f.codcliente = cl.idcliente
										WHERE $where and f.status!=2 and f.status!=6
										ORDER BY f.fecha 
										");

	

	 $result = mysqli_num_rows($query);

	 if($result > 0){

		 //$clientes = mysqli_fetch_assoc($query);


	 


		 $query_efectivo = mysqli_query($conection,"SELECT totaltFactura FROM factura WHERE $where AND status =1");
		 $query_tarjeta = mysqli_query($conection,"SELECT totaltFactura FROM factura WHERE  $where AND status =5");
		 $query_deposito = mysqli_query($conection,"SELECT totaltFactura FROM factura WHERE  $where AND status =3");
		 $query_total = mysqli_query($conection,"SELECT totaltFactura FROM factura WHERE  $where AND status !=2 AND status !=6");






		 $user2     = $_SESSION['nombre'];
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