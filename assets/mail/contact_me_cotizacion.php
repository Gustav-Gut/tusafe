<?php

$modalidad = htmlentities($_POST['inputSelectModoTraslado']);	
$direccion = htmlentities($_POST['inputDireccion']);
$comuna = htmlentities($_POST['inputComunaCotizacion']);
$email = htmlentities($_POST['inputEmailCotizacion']);
$ninos = $_POST['inputNinos'];
$mensaje = nl2br(htmlentities($_POST['inputMensajeCotizacion']));
$colegio = htmlentities($_POST['inputColegio']);
	
// Create the email and send the message
$to = 'contacto@tusafe.cl'; // Add your email address inbetween the '' replacing yourname@yourdomain.com - This is where the form will send a message to.
$email_subject = "Cotización de servicio tuSafe";
$email_body = "Has recibido un mensaje a través del formulario de contacto COTIZACION.\n\n"."Los detalles son:\n\nCantidad de ninos: $ninos\n\nModalidad de traslado: $modalidad\n\nComuna: $comuna\n\nDirección origen: $direccion \n\nColegio de destino: $colegio\n\nEmail: $email\n\nMensaje:\n$mensaje";
$headers = "From: noreply@tusafe.cl\n"; // This is the email address the generated message will be from. We recommend using something like noreply@yourdomain.com.
$headers .= "Reply-To: $email";  

mail($to,$email_subject,$email_body,$headers);

echo '<div class="alert alert-success">
         <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
         <strong><i class="fa fa-check"></i> Éxito!</strong>
         Tu mensaje ha sido enviado, seras contactado a la brevedad!
      </div>';

return true;      

?>