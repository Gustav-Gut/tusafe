$(document).ready(function(){
	$("#contactTrabajo").submit(function( event ){
		event.preventDefault();

		$.ajax({
			type: 'POST',
			url: './assets/mail/contact_me_trabajo.php',
			data: $(this).serialize(),
			success: function(data){
				$("#popupRespuestaTrabajo").slideDown();
				$("#popupRespuestaTrabajo").html(data);
                $('#contactTrabajo').trigger("reset");
			}
		});

		return false;
	});
});