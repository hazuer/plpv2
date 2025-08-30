$(document).ready(function() {
	let baseController = 'controllers/waba.php';
    let idLocationSelected = $('#option-location');

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

    	//--------------
	$('#waba-template').click(function(){
		loadModalTemplate();
	});
	async function loadModalTemplate() {
		$('#mTIdLocation').val(idLocationSelected.val());
		$('#modal-template-title').html('WhatsApp Business API');
		$('#modal-template').modal({backdrop: 'static', keyboard: false}, 'show');
		setTimeout(function(){
			$('#mTTemplate').focus();
		}, 600);
	}

    const previewTemplate = `Hola usuario_db,
Tu pedido ya está listo para recoger en {{2}}.
Puedes recogerlo hoy hasta las {{3}} o mañana antes de las {{4}}.  
De lo contrario, será devuelto el {{5}} a las {{6}}.
Si no podrás recogerlo hoy, confirma tu pedido enviando tu identificación oficial con fotografía.
Te compartimos los datos de tu pedido: folios_db.
¡Nos vemos pronto!`;

    function actualizarPreview() {
        let sucursal = $("#field2").val() || "{{2}}";
        let horaHoy = $("#field3").val() || "{{3}}";
        let horaManana = $("#field4").val() || "{{4}}";
        let fechaDev = $("#field5").val() || "{{5}}";
        let horaDev = $("#field6").val() || "{{6}}";

        let newPreview = previewTemplate
            .replace("{{2}}", sucursal)
            .replace("{{3}}", horaHoy)
            .replace("{{4}}", horaManana)
            .replace("{{5}}", fechaDev)
            .replace("{{6}}", horaDev);

        $("#preview-template").text(newPreview);
    }

    // Eventos para actualizar en tiempo real
    $("#field2, #field3, #field4, #field5, #field6").on("input change", actualizarPreview);



$("#btn-send-template").on("click", function() {
    // Campos a validar
    const mBEstatus = $("#mBEstatus").val();
    const mbIdCatParcel = $("#mbIdCatParcel").val();
    const mBListTelefonos = $("#mBListTelefonos").val().trim();
    const field2 = $("#field2").val().trim();
    const field3 = $("#field3").val().trim();
    const field4 = $("#field4").val().trim();
    const field5 = $("#field5").val().trim();
    const field6 = $("#field6").val().trim();

    let errores = [];

    // Validación
    if(mBEstatus === "99" || mBEstatus === "") errores.push("Estatus del Paquete");
    if( mbIdCatParcel === "") errores.push("Paquetería");
    if(mBListTelefonos === "") errores.push("Lista de Teléfonos");
    if(field2 === "") errores.push("Ubicación");
    if(field3 === "") errores.push("Hora Hoy");
    if(field4 === "") errores.push("Hora Mañana");
    if(field5 === "") errores.push("Fecha Devolución");
    if(field6 === "") errores.push("Hora Devolución");

    if(errores.length > 0){
        swal("Atención!", "* Campos requeridos:\n" + errores.join(", "), "error");
        return;
    }

    // Si pasa la validación, continúa con tu lógica (ej. guardar)
    console.log("Validación correcta, puedes continuar...");


		let formData =  new FormData();
		formData.append('id_location', idLocationSelected.val());

        formData.append('mBEstatus',mBEstatus);
        formData.append('mbIdCatParcel',mbIdCatParcel);
        formData.append('mBListTelefonos',mBListTelefonos);
        formData.append('field2',field2);
        formData.append('field3',field3);
        formData.append('field4',field4);
        formData.append('field5',field5);
        formData.append('field6',field6);
		formData.append('option', 'sendTemplate');
		try {
			$.ajax({
				url        : `${base_url}/${baseController}`,
				type       : 'POST',
				data       : formData,
				cache      : false,
				contentType: false,
				processData: false,
				beforeSend : function() {
					showSwal('Enviado mensajes','Espere por favor...');
					$('.swal-button-container').hide();
				}
			})
			.done(function(response) {
				swal.close();
				if(response.success=='true'){
					swal(`${response.message}`, "", "success");
					$('.swal-button-container').hide();
					$('#modal-template').modal('hide');
					setTimeout(function(){
						swal.close();
						window.location.reload();
					}, 1500);
				}
			}).fail(function(e) {
				console.log("Opps algo salio mal",e);
			});
		} catch (error) {
			console.error(error);
		}
	});
});
