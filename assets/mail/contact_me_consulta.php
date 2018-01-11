<?php

$asunto = htmlentities($_POST['inputAsunto']);	
$nombre = htmlentities($_POST['inputNombre']);
$comuna = htmlentities($_POST['inputComuna']);
$email = htmlentities($_POST['inputEmail']);
$telefono = $_POST['inputTelefono'];
$edad = $_POST['inputEdad'];
$mensaje = nl2br(htmlentities($_POST['inputMensaje']));
	
// Create the email and send the message
$to = 'contacto@tusafe.cl'; // Add your email address inbetween the '' replacing yourname@yourdomain.com - This is where the form will send a message to.
$email_subject = "$asunto";
$email_body = "Has recibido un mensaje a través del formulario de contacto CONSULTA.\n\n"."Los detalles son:\n\nNombre: $nombre , su edad es $edad años\n\nComuna: $comuna\n\nEmail: $email\n\nTeléfono: $telefono\n\nMensaje:\n$mensaje";
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