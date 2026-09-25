 var tabla;
    $(document).ready(function () {
        if (!localStorage["cobaed_token"]) 
            location.href = "../login.php";
    $("#menuMaestros").addClass("active");
    $("#btRegistrar").on("click", btRegistrarclick);
    $("#btAgregar").on("click", btAgregarclick);
    doTabla();
    $(document)
        .on("click", ".btn-edit", function () {
        btEditClick($(this).closest("tr").attr("id").split("_")[1]);
        })
        .on("click", "#dataTable tbody tr", function () {
        $(".trSelected").removeClass("trSelected");
        $(this).toggleClass("trSelected");
        })
        .on("dblclick", "#dataTable tbody tr", function () {
        $(this).find(".btn-edit").trigger("click");
        })
        .on("click", ".btn-delete", (e) => {
        if (confirm("¿Está seguro de eliminar este usuario?")) {
            tkn = $(e.currentTarget).closest("tr").attr("id").split("_")[1];
            $(e.$urrentTarget).closest("tr").addClass("bye");
            $.post("this.php", { eliminar: tkn }, function (response) {
            $(".bye").fadeOut(1300, function () {
                $(".bye").remove();
            });

            tabla.ajax.reload();
            });
        }
        }).on("input", "#searchInput", function () {
            $(this).addClass("dtaFiltro")
            tabla.ajax.reload();
        }).on("click", ".bi-qr-code", function () {
            id = $(this).closest("tr").attr("id").split("_")[1];
            $("#qrModalLabel").text("QR de " + $(this).closest("tr").find("td:nth-child(2)").text());
            $("#qrImage").attr(
            "src",
            "https://api.qrserver.com/v1/create-qr-code/?data=" + location.host + "/cobaed/enrolltchr.php?ttkn=" +
                id +
                "&size=150x150"
            );
            $("#qrModal").modal("show");
        })
        .on("click", "#btImprimirQR", function () {
            id = $(".trSelected").attr("id").split("_")[1];
            if (!id) {
            alert("Seleccione un maestro para imprimir su QR");
            return;
            }
            window.open("https://api.qrserver.com/v1/create-qr-code/?data=" + location.host + "/cobaed/enrolltchr.php?ttkn=" + id + "&size=150x150", "_blank");
        })

    // Avatar upload preview
    function readURL(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(".avatar-preview").css(
            "background-image",
            "url(" + e.target.result + ")"
            );
        };
        reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imageUpload").change(function () {
        readURL(this);
    });
    });

    function btAgregarclick() {
    $.post(
        "this.php",
        { fetchListas: localStorage["cobaed_token"], usr: 0 },
        function (data) {
        $(".dta").each(function () {
            $(this).val("");
        });
        $("#nivel").val("1");

        $.each(data, function (key, value) {
            $("#" + key).html(value);
        });
        $("#ingreso").val(new Date().toISOString().slice(0, 10));
        $("#modalWindowLabel").text("Agregar Maestro(a)");
        $(".avatar-preview").css(
            "background-image",
            "url(../assets/img/usuario.png)"
        );
        $("#modalWindow").modal("show").data("nuevo", true);
        }
    );
    }



    function btEditClick(id) {
        controles="";
        coma="";

        $(".dta").each(function(){
            controles += coma +  $(this).attr('id')
            coma=",";
        })
        $.post("this.php", {fetchListas: localStorage['cobaed_token'], usr: id, controles:controles}, function(data) {            
            $.each(data, function(key, value) {
                $("#"+key).html(value);
            });

            $.each(data["_controles_"], function(key, value) {
                $("#"+key).val(value);
            });
            $('#modalWindowLabel').text('Editar Maestro(a)');
            $('.avatar-preview').css('background-image', 'url(../assets/img/usuario.png)');
            $('#modalWindow').modal('show')
            .data("nuevo", false);
        });
    }

function btRegistrarclick() {
    $('.dta').each(function() {
        if ($(this).val().trim() === '') {
            $(this).addClass('is-invalid').removeClass('is-valid');
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    });
    if ($('.is-invalid').length === 0) {
        var formData = {};
        $('.dta').each(function(index, element) {
            var value = $(element).val();
            formData[$(element).attr('id')] = value;
        });
        datos={}
        datos["registrar"]=localStorage["cobaed_token"];
        datos["dta"]=formData;
        datos["nuevo"]=$("#modalWindow").data("nuevo");
        datos["token"]=$("#modalWindow").data("nuevo")?"":$(".trSelected").attr("id").split("_")[1] ;
        $.post('this.php', datos,
            function(response) {
                if (response.ok) {
                    $('#modalWindow').modal('hide');
                    tabla.ajax.reload();
                    alert('Usuario registrado correctamente');
                     
                } else 
                    alert('Error al registrar el usuario: ' + response.message);
                
            }
        );
    }

}
        function doTabla(){
        const $window = $(window);
        const isMobile = $window.width() <= 768;
        const token = localStorage["obranet_token"];
        const columns = ["acciones","nombre", "puesto", "lun", "mar", "mie", "jue", "vie", "sab", "total", "estado"]
            .map(column => ({ data: column, orderable: false }));
        tabla = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            draw: 1,
            language: {
                url: '../assets/js/es-ES.json'
            },
            columns: columns,
            ajax: {
                url: "data.php",
                type: "POST",
                data: buildAjaxData,
                error: handleAjaxError
            },
            drawCallback: function(settings) {
                 
                    
                highlightSelectedRows();
                $(".circle").each(function() {
                    if($(this).text()=="0") 
                        $(this).addClass("circle-zero");
                })
                // Move #btAgregar into the filter area when render is ready
            }
        });
        let controlsInitialized = false;
        
        function buildAjaxData(d) {
            if (!controlsInitialized) {
                initializeControls();
                controlsInitialized = true;
            }
            d.token = token;
            $(".dtaFiltro").each(function() {
                d[this.id] = $(this).val();
            });       
            d["token"] = localStorage["cobaed_token"];
            return d;
        }


        function initializeControls() {
            $(".dataTables_info").addClass("p-3");
             $(".dataTables_paginate").addClass("px-3");
            $(".dataTables_filter").addClass("p-1 px-3");
            $(".dataTables_length").addClass("d-none");
           
            const controlsHtml = `
            <div style="float:right;margin-left:200px">
            <input type="text" id="edLatLng">
            <button class="btn btn-primary" id="btEstos">Mover 0 Personas</button>
            <button class="btn btn-success" id="btSecs">Secciones</button>
            <button class="btn btn-success" id="btCols">Colonias</button>
            </div>`;
         //   $(".dataTables_length").append(controlsHtml);
             
        }

        function handleAjaxError(xhr, error, thrown) {
            console.error("DataTables AJAX error:", error, thrown);
        }

        function highlightSelectedRows() {
            $('#dataTable').find("tr:first-child").addClass("trSelected");
        }
    }