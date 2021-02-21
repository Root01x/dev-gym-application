<?php 
require_once('stripe-php/init.php');
session_start(); 
include "../conection.php";
\Stripe\Stripe::setApiKey('sk_test_51IMmgKIFXq65j7mHdpO2Vy3sKvJtRHLxgBh8nx98yzW1VS1Bz0QVp5gOXwTf8bBtMShpDPTPcGHi84McavrPrwqD00Ur0eoZkT');

$token = $_POST['stripeToken'];
$total  = $_POST["total"];

try {
	// Crear cargo de Stripe
$charge = \Stripe\Charge::create(
    array(
        'amount' => $total*100,
        'currency' => "USD", // Cambiar el tipo de moneda
        'source' => $token,
    )
);

	   $token = md5($_SESSION['idUser']);
       $usuario = $_SESSION['idUser'];

       $query = mysqli_query($conection,"SELECT * FROM detalle_temp WHERE token_user = '$token'");
       $result = mysqli_num_rows($query);

	   $query2 = mysqli_query($conection,"SELECT c.idcliente as idcliente FROM cliente c INNER JOIN usuario u on c.Correo=u.correo WHERE u.idusuario = $usuario");
       
	   //$result = mysqli_num_rows($query);

	   $data = mysqli_fetch_assoc($query2);
	   $codcliente    = $data['idcliente'];

     





if($charge->status=="succeeded"){
	

	if ($result > 0) {
		$query_procesar = mysqli_query($conection,"CALL procesar_transaccion($usuario,$codcliente,'$token')");
		$result_detalle = mysqli_num_rows($query_procesar);

		if ($result_detalle > 0) {
			
			echo "<script>alert('TRANSACCION EXITOSA!');</script>";

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
	echo "<script>window.location='pagos.php';</script>";
?>