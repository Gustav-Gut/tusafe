<?php

$trabajo = $_POST['inputSelectTrabajo'];	
$nombre = htmlentities($_POST['inputNombreTrabajo']);
$comuna = htmlentities($_POST['inputComunaTrabajo']);
$email = htmlentities($_POST['inputEmailTrabajo']);
$conociste = $_POST['inputSelectConociste'];
$mensaje = nl2br(htmlentities($_POST['inputMensajeTrabajo']));
	
// Create the email and send the message
$to = 'contacto@tusafe.cl'; // Add your email address inbetween the '' replacing yourname@yourdomain.com - This is where the form will send a message to.
$email_subject = "Solicitud de tranajo tuSafe";
$email_body = "Has recibido un mensaje a través del formulario de contacto TRABAJO.\n\n"."Los detalles son:\n\nNombre: $nombre\n\nComuna: $comuna\n\nEmail: $email\n\nEsta interesado en el trabajo de: $trabajo\n\nConocio tuSafe via: $conociste\n\nMensaje:\n$mensaje";
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