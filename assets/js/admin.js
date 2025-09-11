$(document).ready(function() {
	let baseController = 'controllers/packageController.php';

	const checkboxes = document.querySelectorAll(".chk-package");

	checkboxes.forEach(chk => {
		chk.addEventListener("change", function() {
			const tracking = this.getAttribute("data-tracking");
			const isVerified = this.checked ? 1 : 0; // ✅ 1 si está marcado, 0 si no
			console.log("Tracking seleccionado:", tracking, "Estado:", isVerified);

			let formData = new FormData();
			formData.append('tracking', tracking);
			formData.append('is_verified', isVerified); // enviar el valor a la BD
			formData.append('option', 'verifiedPackage');

			$.ajax({
				url: `${base_url}/${baseController}`,
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
			}).done(function(response) {
				console.log("Actualizado:", response);
			}).fail(function(e) {
				console.log("Opps algo salió mal", e);
			});
		});
	});

});