$(document).ready(function() {
	let baseController = 'controllers/waba.php';
    let idLocationSelected = $('#option-location');

      	let table = $('#tbl-msj-whats').DataTable({
		"language": {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
            aria: {
                sortAscending: ": Activar para ordenar la columna de forma ascendente",
                sortDescending: ": Activar para ordenar la columna de forma descendente"
            }
        },
		"bPaginate": true,
		"lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]], // Definir las opciones de longitud del menú
        "pageLength": 500, // Establecer el número de registros por página predeterminado
        "bInfo" : true,
		scrollCollapse: true,
		scroller: true,
		scrollY: 450,
		scrollX: true,
		dom: 'Bfrtip',
		buttons: [
			'excel'
		],
		"columns" : [
			{title: `Telefono`,               name:`sender_phone`,       data:`sender_phone`},      //0
			{title: `Contacto`,        name:`contact_name`,    data:`contact_name`},   //1
			{title: `Último mensaje`,       name:`last_message`,      data:`last_message`},     //2
			{title: `Fecha mensaje`,   name:`last_date`,   data:`last_date`},  //3
            {title: `opc`,   name:`opc`,   data:`opc`},  //3
		],
        "columnDefs": [
			// { "width": "40%", "targets": [1,2] }
		],
        'order': [[3, 'asc']]
	});

	//funcion para borrar campo de busqueda
	let clearButton = $(`<span id="clear-search" style="cursor: pointer;">&nbsp;<i class="fa fa-eraser fa-lg" aria-hidden="true"></i></span>`);
	clearButton.click(function() {
		$("#tbl-msj-whats_filter input[type='search']").val("");
		setTimeout(function() {
			$("#tbl-msj-whats_filter input[type='search']").trigger('mouseup').focus();
		}, 100);
	});
	$("#tbl-msj-whats_filter label").append(clearButton);


    // 👉 Función que carga los mensajes
    function cargarMensajes(tophone, phoneWaba) {
        console.log(tophone, phoneWaba);
        $.ajax({
            url: `${base_url}/${baseController}`,
            type: 'POST',
            data: { 
                phone: tophone, 
                phoneWaba: phoneWaba,
                option: 'getAllMessagesToRead' 
            },
            dataType: 'json',
            beforeSend: function() {
                showSwal('Cargando mensajes', 'Espere por favor...');
                $('.swal-button-container').hide();
            },
            success: function(mensajes) {
                let html = '';
                if (mensajes.length > 0) {
                    mensajes.forEach(msg => {
                        const myNumber = phoneWaba;
                        let tipo = (msg.sender_phone === myNumber) ? 'sent' : 'received';
                        let fechaHora = new Date(msg.datelog.replace(' ', 'T')).toLocaleString([], {
                            weekday: 'short',
                            day: '2-digit',
                            month: 'short',
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
                $('#chat-container').scrollTop($('#chat-container')[0].scrollHeight);
                swal.close();
                setTimeout(() => $('#chat-input').focus(), 600);
            },
            error: function(xhr, status, error) {
                swal.close();
                console.error("Error al cargar mensajes:", error);
            }
        });
    }



let chatInterval = null; // 👉 Variable global para controlar el intervalo

// Abrir modal de chat
$(`#tbl-msj-whats tbody`).on('click', '#btn-read-w', function () {
    let row = table.row($(this).closest('tr')).data();
    $("#tophone").val(row.sender_phone);
    let tophone = $("#tophone").val();
    let phoneWaba = $("#phone_waba").val();

    cargarMensajes(tophone, phoneWaba);

    $('#modal-chat-w-title').html(`${row.sender_phone} - ${row.contact_name}`);
    $('#modal-chat-w').modal({backdrop: 'static', keyboard: false}, 'show');

    // 👉 Iniciar recarga automática cada 15 segundos
    if (chatInterval) clearInterval(chatInterval); // por si ya estaba corriendo
    chatInterval = setInterval(function() {
        cargarMensajes(tophone, phoneWaba);
    }, 15000);
});

// Cerrar modal
$('#btn-close-chatw, #btn-close-chatw-1').click(function(){
    if (chatInterval) {
        clearInterval(chatInterval); // 👉 Detener recarga automática
        chatInterval = null;
    }
    window.location.reload();
});

// Enviar mensaje
$('#btn-send').on('click', function() {
    sendWhats();
});

function sendWhats(){
    console.log('okas clic');
    let tophone = $("#tophone").val();
    let tokenWaba = $("#tokenWaba").val();
    let phoneWaba = $("#phone_waba").val();
    let phoneNumberId = $("#phone_number_id").val();
    let mensaje = $("#chat-input").val();

    if(mensaje.trim() !== '') {
        $.ajax({
            url: `${base_url}/${baseController}`,
            type: 'POST',
            data: { 
                tophone: tophone, 
                tokenWaba: tokenWaba,
                phoneWaba: phoneWaba,
                msj: mensaje,
                phoneNumberId: phoneNumberId,
                option: 'sendMessage' 
            },
            success: function(response) {
                console.log(response);
                $("#chat-input").val('');
                console.log('mensaje enviado');
                // 👉 Refrescar mensajes inmediatamente después de enviar
                cargarMensajes(tophone, phoneWaba);
            },
            error: function(xhr, status, error) {
                console.error("Error al enviar mensaje:", error);
            }
        });
    }
}

// Enviar con Enter
$('#chat-input').on('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault(); 
        sendWhats();
    }
});






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
            formData.append('phoneWaba', $("#phone_waba").val());
            formData.append('phoneNumberId', $("#phone_number_id").val());
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


