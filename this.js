$(document).ready(function() {
    if(localStorage["cobaed_imei"] === undefined){
        localStorage["cobaed_imei"] = $("#hRand15").val();
    }

    if(localStorage["cobaed_aTkn"] !== undefined){
        location.href = "cobaedsn/index.html";
    }

    if(localStorage["cobaed_tTkn"] !== undefined){
        location.href = "docentes/index.php"
    }
     
     updateDate();
    // Agregar interactividad a las tarjetas de enlace
    const linkCards = document.querySelectorAll('.link-card');
    linkCards.forEach(card => {
        card.addEventListener('click', function() {
            alert('Esta funcionalidad estará disponible próximamente');
        });
    });
})

 function updateDate() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateString = now.toLocaleDateString('es-ES', options);
            document.getElementById('current-date').textContent = dateString;
        }
        
