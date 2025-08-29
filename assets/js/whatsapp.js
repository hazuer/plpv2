$(document).ready(function() {
	let baseController = 'controllers/packageController.php';

		$('#btn-send').click(function(){
		sendwts();
	});

	function sendwts() {
		let tophone =  $('#tophone').val();
		let msj= $('#chat-input').val();
		if(tophone=='' || msj==''){
			swal("Atención!", "* Campos requeridos", "error");
			return;
		}
		$.ajax({
    url: "https://graph.facebook.com/v19.0/683077594899877/messages",
    method: "POST",
    headers: {
        "Authorization": "Bearer EAAYTcZCLS2AABPfHbZCFMSbJn4ZAFLl2cg2YZBqvZBZA4ALnd3vKcLB1UPDwCnaBs5lcXEDHOk4sFcFqAFydAyACGvQPtrVv8030CBPu2OSjwIlU13YBPZCyljubi0ZCNKXbbTrmYZAnO65WLXTHNg9tFDfSVOkZA4YZCQU8DiKtj3DdtfZBXZA8cJVNjReM2AEuh2BtXtiubpCIRBZAbhvplNUNAsXlw9ZAbhKppCqSCoNdEK3",
        "Content-Type": "application/json"
    },
    data: JSON.stringify({
        "messaging_product": "whatsapp",
        "to": tophone,
        "type": "text",
        "text": {
            "body": msj
        }
    }),
    success: function(response) {
        console.log("Mensaje enviado:", response);
		$('#chat-input').val('');
		swal(`Éxito`, `Mensaje enviado`, "success");
    },
    error: function(xhr) {
        console.error("Error:", xhr.responseText);
    }
});

	}

});

