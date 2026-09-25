$(document).ready(function() {
    // Inicializar DataTable
     $('#btn-mic').click(function(){
            $(this).toggleClass('recording');
            if($(this).hasClass('recording')){
                $('#voice-label').text("Escuchando... (Toca para detener)");
                $('#voice-label').addClass('text-danger fw-bold');
            } else {
                $('#voice-label').text("Procesando voz...");
                setTimeout(() => {
                    $('#pills-text-tab').tab('show');
                    $('#raw-text-input').val("El teorema de Pitágoras establece que en todo triángulo rectángulo, el cuadrado de la longitud de la hipotenusa es igual a la suma de los cuadrados de las respectivas longitudes de los catetos.");
                    $('#voice-label').removeClass('text-danger fw-bold').text("Toca para dictar a Kobai");
                }, 800);
            }
        });

        $('#btn-process').click(function(){
            $('#ai-loader').css('display', 'flex');
            setTimeout(function(){
                $('#ai-loader').fadeOut();
                $('#card-input').slideUp();
                $('#card-result').removeClass('d-none');
                $('#editor-canvas').html('<p>En todo <strong>triángulo rectángulo</strong>, el cuadrado de la hipotenusa es igual a la suma de los cuadrados de los catetos.</p><div class="p-2 bg-light border rounded text-center my-2">$$ c^2 = a^2 + b^2 $$</div>');
            }, 1500);
        });

        $('#btn-close-result').click(function(){
            $('#card-result').addClass('d-none');
            $('#card-input').slideDown();
        });
        $("#btSugerir").on("click", btSugerirClick);
});

async function btSugerirClick() {
    payload={}
    payload["token"]=localStorage['cobaed_tTkn'];
    payload["rama"]=$("#rama").val();
    payload["tema"]=$("#tema").val();
    payload["academia"]=$("#academia").val()
    await apiCall("sugerir_ingesta",payload)
    .then((response)=>{
        if(response.ok){
            alert("¡Sugerencia enviada! Gracias por contribuir a mejorar Kobai.");
        } else {
            alert("Error al enviar la sugerencia: " + response.error);
        }
    })
    .catch((error)=>{
        alert("Error en la comunicación con el servidor: " + error.message);
    });
    
}