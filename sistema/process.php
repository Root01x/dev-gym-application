<script>
  function PdfCreate(cliente,factura){
    var ancho = 1000;
    var alto = 800;
    //calcular la posicion x, y para centrar la ventana
    var x = parseInt((window.screen.width/2)-(ancho/2));
    var y = parseInt((window.screen.height/2)-(alto/2));

    $url = 'factura/generaFactura.php?cl='+cliente+'&f='+factura;
    window.open($url,"Factura","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizeble=si,menubar=no");

}
</script>
<?php 
require_once('stripe-php/init.php');
session_start(); 

if ($_SESSION['rol'] != 5) {
    # code...

    header("location: ../");
}

include "../conection.php";
\Stripe\Stripe::setApiKey('sk_test_51IMmgKIFXq65j7mHdpO2Vy3sKvJtRHLxgBh8nx98yzW1VS1Bz0QVp5gOXwTf8bBtMShpDPTPcGHi84McavrPrwqD00Ur0eoZkT');

$token = $_POST['stripeToken'];
$total  = 0;





   
	$token2      = md5($_SESSION['idUser']);


	$query = mysqli_query($conection,"SELECT    tmp.correlativo,
												tmp.token_user,
												tmp.cantidad,
												tmp.precio_venta,
												p.codevento,
												p.descripcion
									  FROM detalle_temp tmp
									  INNER JOIN evento p
									  ON tmp.codevento = p.codevento 
									  WHERE token_user = '$token2' ");         
												
												
	$result = mysqli_num_rows($query);

	$query_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
	$result_iva = mysqli_num_rows($query_iva);
	
   

	$detalleTabla = '';
	$sub_total = 0;
	$iva       = 0;
	$total     = 0;
	$arrayData = array();

	if ($result > 0) {

		if ($result_iva > 0) {
			$info_iva = mysqli_fetch_assoc($query_iva);
			$iva = $info_iva['iva'];
			# code...
		}
			while ($data = mysqli_fetch_assoc($query)) {
			   
				$precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
				$sub_total = round($sub_total + $precioTotal, 2);
				$total = round($total + $precioTotal, 2);

				
			}
			

			$impuesto = round($sub_total * ($iva / 100), 2);
			$tl_sniva = round($sub_total - $impuesto, 2);
			$total = round($tl_sniva + $impuesto, 2);

			  
		   
		   
		# code...
	}else {
		echo "<script>alert('NO EXISTEN SEMINARIOS EN EL DETALLE');</script>";
		echo "<script>window.location='lista_eventos.php';</script>";
		exit;
	}

	















try {
	// Crear cargo de Stripe
$charge = \Stripe\Charge::create(
    array(
        'amount' => $total*100,
        'currency' => "USD", // Cambiar el tipo de moneda
        'source' => $token,
    )
);

	   $token3 = md5($_SESSION['idUser']);
       $usuario = $_SESSION['idUser'];

       $query = mysqli_query($conection,"SELECT * FROM detalle_temp WHERE token_user = '$token3'");
       $result = mysqli_num_rows($query);

	   $query2 = mysqli_query($conection,"SELECT c.idcliente as idcliente FROM cliente c INNER JOIN usuario u on c.Correo=u.correo WHERE u.idusuario = $usuario");
       
	   //$result = mysqli_num_rows($query);

	   $data = mysqli_fetch_assoc($query2);
	   $codcliente    = $data['idcliente'];

     





if($charge->status=="succeeded"){
	

	if ($result > 0) {
		$query_procesar = mysqli_query($conection,"CALL procesar_transaccion($usuario,$codcliente,'$token3')");
		$result_detalle = mysqli_num_rows($query_procesar);





		if ($result_detalle > 0) {
			
			
			$data = mysqli_fetch_assoc($query_procesar);
			$cliente=$data['codcliente'];
			$factu=$data['nofactura'];
			//echo "<script>alert('TRANSACCION EXITOSA!');</script>";
			
			echo "<script>PdfCreate($cliente,$factu);</script>";

			
			


		}else {

			echo "<script>alert('Error al pagar!');</script>";
			Core::alert("ERROR EN LA TRANSACCION!");

			
		}


	}else {
		
	}


}else{
	echo "<script>alert('Error al pagar!');</script>";
	Core::alert("ERROR EN LA TRANSACCION!");	
}
}catch(Exception $e){
	echo "<script>alert('".$e->getMessage()."');</script>";
}
	echo "<script>window.location='lista_eventos.php';</script>";
?>

