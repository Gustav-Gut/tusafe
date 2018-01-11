$(document).ready(function(){
	$("#contactCotizacion").submit(function( event ){
		event.preventDefault();

		$.ajax({
			type: 'POST',
			url: './assets/mail/contact_me_cotizacion.php',
			data: $(this).serialize(),
			success: function(data){
				$("#popupRespuestaCotizacion").slideDown();
				$("#popupRespuestaCotizacion").html(data);
                $('#contactCotizacion').trigger("reset");
			}
		});

		return false;
	});
});