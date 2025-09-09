$(document).ready(function() {
	let baseController = 'controllers/packageController.php';

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
			{title: `ID Package`,               name:`id_package`,       data:`id_package`},      //0
			{title: `Phone`,        name:`phone`,    data:`phone`},   //1
			{title: `F. Notificación`,       name:`n_date`,      data:`n_date`},     //2
			{title: `Contact Registro`,   name:`contact_name`,   data:`contact_name`},  //3
			{title: `Message ID`,         name:`message_id`,   data:`message_id`},  //4
			{title: `Último Estatus`,             name:`statusName`,             data:`statusName`},            //5
			{title: `Fecha Estatus`,            name:`statusDate`,            data:`statusDate`},           //6
		],
        'order': [[2, 'desc']]
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

});