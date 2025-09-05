$(document).ready(function() {
	let baseController = 'controllers/waba.php';
    let idLocationSelected = $('#option-location');

    $('#btn-send').click(function(){
        sendwts();
    });

    $('#chat-input').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // evita que haga salto de línea si es textarea
            sendwts();
        }
    });

	function sendwts() {
		 let tophone = $('#tophone').val();
    let msj = $('#chat-input').val();

    $.ajax({
        url: baseController,
        method: "POST",
        data: {
            action: 'sendMessage',
            tophone: tophone,
            msj: msj,
            tokenWaba: $("#tokenWaba").val(),
            option:'sendMessage'
        },
        dataType: 'json',
        beforeSend: function() {
            showSwal('Enviando mensaje', 'Espere por favor...');
            $('.swal-button-container').hide();
        },
        success: function(response) {
            swal.close();
            if (response.success) {
                //console.log("Mensaje enviado:", response);
                $('#chat-input').val('');
                // ✅ Opcional: recargar mensajes
                //loadChatMessages(tophone);
            } else {
                swal("Error", response.message, "error");
            }
        },
        error: function(xhr) {
            swal.close();
            swal("Error", "Hubo un problema al enviar el mensaje.", "error");
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
    $("#field2, #field3, #field4, #field5, #field6, #mBEstatus").on("input change", actualizarPreview);


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
        let successCount = 0;
        let errorCount = 0;

        function enviarSiguiente() {
            if (index >= total) {
                swal("¡Proceso completado!", 
                    `${total} mensajes procesados.\n✅ Éxitosos: ${successCount}\n❌ Errores: ${errorCount}`, 
                    "success");
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
            let texto = $("#preview-template").text();
            formData.append('txtTemplate', texto);
            formData.append('tokenWaba', $("#tokenWaba").val());
            formData.append('option', 'sendTemplate');

            $.ajax({
                url: `${base_url}/${baseController}`,
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    try {
                        if (response.success === "true" || response.success === true) {
                            successCount++;
                        } else {
                            errorCount++;
                        }
                    } catch (e) {
                        errorCount++; // si no se puede parsear el JSON, lo contamos como error
                    }

                    index++;
                    let porcentaje = Math.round((index / total) * 100);
                    $("#progress-bar").css("width", porcentaje + "%");
                    $("#progress-text").text(`${index} de ${total}`);
                    enviarSiguiente(); // Llamar al siguiente
                },
                error: function () {
                    errorCount++;
                    index++;
                    $("#progress-text").text(`${index} de ${total} (Error)`);
                    enviarSiguiente();
                }
            });
        }

        enviarSiguiente();
    });


$(document).on('click', '.chat-item', function() {
    let phone = $(this).data('phone'); // data-phone en el <li>
    
    $.ajax({
        url: `${base_url}/${baseController}`,
        type: 'POST',
        data: { phone: phone, option: 'getAllMessagesToRead' },
        dataType: 'json',
        beforeSend: function() {
            showSwal('Cargando mensajes', 'Espere por favor...');
            $('.swal-button-container').hide();
        },
        success: function(mensajes) {
            let html = '';
            
            if (mensajes.length > 0) {
                $("#tophone").val(phone);
                //console.log('ok mensajes');
                mensajes.forEach(msg => {
                    console.log(msg);
                    const myNumber = '5217344093961'; // tu número WABA
                let tipo = (msg.sender_phone === myNumber) ? 'sent' : 'received';
                    let fechaHora = new Date(msg.datelog.replace(' ', 'T')).toLocaleString([], {
                        weekday: 'short',  // ej: "lun"
                        day: '2-digit',    // ej: "03"
                        month: 'short',    // ej: "sep"
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    html += `<div class="chat-bubble ${tipo}">
                                ${msg.message_text}
                                <span class="time">${fechaHora}</span>
                             </div>`;
                });
            } else {
                html = "<p style='text-align:center;color:#777;'>No hay mensajes.</p>";
            }

            $('#chat-container').html(html);

            // ✅ Scroll al final
            $('#chat-container').scrollTop($('#chat-container')[0].scrollHeight);

            // ✅ Cierra SweetAlert después de cargar mensajes
            swal.close();
        },
        error: function(xhr, status, error) {
            swal.close();
            console.error("Error al cargar mensajes:", error);
        }
    });
});


    $('#btn-read').click(function(){
        markasread();
    });
    function markasread() {
		let tophone = $('#tophone').val();
    $.ajax({
            url: baseController,
            method: "POST",
            data: {
                tophone: tophone,
                option:'markAsRead'
            },
            dataType: 'json',
            success: function(response) {
                swal("Éxito", "Mensajes leidos", "success");
            },
            error: function(xhr) {
                swal.close();
                swal("Error", "Error al actualizar lectura.", "error");
            }
        });
	}


});


