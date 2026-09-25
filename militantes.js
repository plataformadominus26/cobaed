 
    $(document).ready(function() {
        
        $(".footer-nav").css("background-color", "#ff964bff");
        $("#btnRegistrarBeneficiario").on("click", function() {
            $("#hNombre").val($("#inputNombreBeneficiario").val());
            $("#btRegistrar").data("id", "-1").click();
            $("#inputNombreBeneficiario").val("");
            $("#modalRegistrarBeneficiario").modal("hide");
        })
        $("#btRegistrar").on("click", btRegistrarClick);
        $("#searchInput").on("input", populateBeneficiaries);
         
        
        $('#start-btn').click(function () {   
            
        });

         $('#stop-btn').click(function () {
            
        });
    $('.btn-footer').on('click', function() {
       
        // Prevent tab navigation if login is active
        if ($("#login-tab").hasClass("active")) return; 
        const tabId = $(this).data('tab');
        const tabEl = document.querySelector(`#${tabId}-tab`);
        if (tabEl) {
        bootstrap.Tab.getOrCreateInstance(tabEl).show();
        }
    });
        $("#btLogin").on("click", function() {
            const username = $("#username").val();
            const password = $("#password").val();
            $.post("movil.php", { u:username,p: password }, function(data) {
                if (data.ok) {
                    localStorage.setItem("cobaed_token", data.token);
                    const tab = new bootstrap.Tab(document.querySelector('#qr-tab'));
                    tab.show();
                } else {
                    alert("nombre de usuario o contraseña incorrectos");
                }
            }).fail(function(error) {
                console.log(error);
            });
        });
        if(!localStorage.getItem("cobaed_token")){
            $("#login-tab").click();
            }
        populateBeneficiaries();
    });

     // Populate beneficiary cards
    function populateBeneficiaries() {
        
        
    }

    function ok(qrCodeMessage) {
        $("#hQr").val(qrCodeMessage);
        $.post("militantes.php", {parseQr: qrCodeMessage}, function (data) { 
            $(".citizen-info").html(`        
                        <h5 class="card-title">${data.nombre}</h5>
                        <p class="mb-1"><strong>Organización:</strong> ${data.organizacion || ''}</p>
                        <p class="mb-1"><strong>Domicilio:</strong> ${data.dom}</p>
                        <p class="mb-1"><strong>Colonia:</strong> ${data.col}</p>
                        <p class="mb-1"><strong>Teléfono:</strong> ${data.tel}</p>
                        <p class="mb-0"><strong>ID:</strong> ${data.id}</p>
            `)
            id= data.id;
            if(data.ok)
            $("#btRegistrar").removeClass("btn-danger").addClass("btn-success").data("id", data.id);
            else
            if(data.registrado)
                alert("El beneficiario ya está registrado para esta organización");
            else
            {
                $("#btRegistrar").removeClass("btn-success").addClass("btn-danger").data("id", 0);
                $("#modalRegistrarBeneficiario").modal("show");
            }
        })
    }

 function btRegistrarClick(){
    
 }

  