$(document).ready(function(){
	$("#contactConsulta").submit(function( event ){
		event.preventDefault();

		$.ajax({
			type: 'POST',
			url: './assets/mail/contact_me_consulta.php',
			data: $(this).serialize(),
			success: function(data){
				$("#popupRespuesta").slideDown();
				$("#popupRespuesta").html(data);
                $('#contactConsulta').trigger("reset");
			}
		});

		return false;
	});
});