$('#btn-publish').click(function() {
    // 1. Recolectar datos finales (puede que el maestro haya editado el HTML)
    let finalHtml = $('#editor-canvas').html();
    let finalTitle = $('#result-title-input').val(); // Asegúrate de ponerle este ID al input del título
    
    // Estos datos deben persistir desde la selección inicial
    // Puedes guardarlos en variables globales o leerlos de los selects de arriba
    let materiaSelect = "Matemáticas I"; // O $('#select-materia').val()
    let temaSelect = "Teorema de Pitágoras"; // O $('#select-tema').val()
    
    // Recolectar tags (si los guardaste en algún lado, o enviarlos vacíos por ahora)
    let tagsArray = ["Álgebra", "Básico"]; 

    let payload = {
        materia_nombre: materiaSelect,
        tema_nombre: temaSelect,
        titulo: finalTitle,
        contenido_html: finalHtml,
        tags: tagsArray
    };

    // UI: Loading
    $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    // 2. Enviar al Backend
    $.ajax({
        url: 'backend/save_knowledge.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        success: function(response) {
            if(response.success) {
                alert("¡Éxito! El conocimiento ha sido guardado en la Academia.");
                location.reload(); // Recargar para limpiar todo
            } else {
                alert("Error al guardar: " + response.error);
                $('#btn-publish').prop('disabled', false).html('PUBLICAR EN ACADEMIA');
            }
        },
        error: function() {
            alert("Error fatal de comunicación.");
            $('#btn-publish').prop('disabled', false).html('PUBLICAR EN ACADEMIA');
        }
    });
});