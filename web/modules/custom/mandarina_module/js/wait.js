(function ($, Drupal) {
	$( ".entrar-wifi" ).on( "click", function() {
		var nombre = $('#edit-nombre').val();
		var apellido = $('#edit-apellido').val();
		var telefono = $('#edit-telefono').val();
		var correo = $('#edit-correo').val();
		if( nombre != '' && apellido != '' && telefono != '' && correo != ''){
		  	$('#wait-modal').modal({
		  		keyboard: false
			});
		}
	});
})(jQuery, Drupal);