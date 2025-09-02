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
                "Authorization": "Bearer EAAYTcZCLS2AABPWBNEmHaeUcQMoHC8M3XfB2l9rrSHECsZB66Fo5X1M8h1giJDx4VoOZBZBYKrwjSpoBspaM7sgE31BLBqvXROCI34SFZBZBfcSIABALLmlyZApk7NcZCQgR8fpRLVXfLiXqj2AkQ4LdFFM02hlNqaM3H1rYr6pKuYyQyEqv8uhq1WaZBIQHNRXZCAzc6wju4cYilVVNpZAoHdLKeeZARynpST4mu94dUXANvG0YGQZDZD",
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

    $("#btn-send-template").on("click", function () {
    const numeros = $("#mBListTelefonos").val().trim().split("\n").filter(n => n !== "");
    const total = numeros.length;

    if (total === 0) {
        swal("Atención!", "Lista de Teléfonos vacía", "error");
        return;
    }

    // SweetAlert con barra de progreso dentro
    swal({
        title: "Enviando mensajes...",
        content: {
            element: "div",
            attributes: {
                innerHTML: `
                    <div style="width:100%;border:1px solid #ccc;border-radius:5px;">
                        <div id="progress-bar" style="width:0%;background:#28a745;height:20px;border-radius:5px;"></div>
                    </div>
                    <p id="progress-text" style="margin-top:10px;">0 de ${total}</p>
                `
            }
        },
        buttons: false,
        closeOnClickOutside: false,
        closeOnEsc: false
    });

    let index = 0;

    function enviarSiguiente() {
        if (index >= total) {
            swal("¡Proceso completado!", `${total} mensajes enviados correctamente.`, "success");
            return;
        }

        const numero = numeros[index];

        let formData = new FormData();
        formData.append('id_location', idLocationSelected.val());
        formData.append('mBEstatus', $("#mBEstatus").val());
        formData.append('mbIdCatParcel', $("#mbIdCatParcel").val());
        formData.append('field2', $("#field2").val());
        formData.append('field3', $("#field3").val());
        formData.append('field4', $("#field4").val());
        formData.append('field5', $("#field5").val());
        formData.append('field6', $("#field6").val());
        formData.append('number', numero);
        formData.append('option', 'sendTemplate');

        $.ajax({
            url: `${base_url}/${baseController}`,
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                index++;
                let porcentaje = Math.round((index / total) * 100);
                $("#progress-bar").css("width", porcentaje + "%");
                $("#progress-text").text(`${index} de ${total}`);
                enviarSiguiente(); // Llamar al siguiente
            },
            error: function () {
                index++;
                $("#progress-text").text(`${index} de ${total} (Error)`);
                enviarSiguiente();
            }
        });
    }

    enviarSiguiente();
});

});
