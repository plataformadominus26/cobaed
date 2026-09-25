var tabla;

    $(document).ready(function() {
         


        if(!localStorage['cobaed_token']){
            location.href="../login.php";
            return;
        }
        $.post("this.php", { fetchMaestros: localStorage['cobaed_token']}, function(data) {
            $("#selectMaestro").html(data);
             doTabla();
        })

         
        $("#menuHorarios").addClass("active");
        $("#btRegistrar").on('click', btRegistrarclick);
        $("#btAgregar").on('click', btAgregarclick);
        $("#btRegistrarClase").on('click', btRegistrarClaseClick);

        $(document)
            .on("click", '.btn-edit', function() {
                btEditClick($(this).closest('tr').attr('id').split("_")[1]);
            })
            .on("click", "#dataTable tbody tr", function() {
                $(".trSelected").removeClass("trSelected");
                $(this).toggleClass("trSelected");
            })
            .on("dblclick", "#dataTable tbody tr", function() {
                $(this).find('.btn-edit').trigger('click');
            })
            .on("click","#dataTable td",function(){
                $(".este").removeClass("este");
                $(this).addClass("este");
                txt = $(this).text().trim().replace(/\u00a0/g, "");
                if (txt!="")
                    $("#btEliminarClase").removeClass("d-none");
                else
                    $("#btEliminarClase").addClass("d-none");
                if($(this).index()>0){
                    seleccionaArea($(this).index(),$(this).closest("tr").find("td:first").text());
                }
            })
            .on("click", ".libre", function() {
                $(".esta").removeClass("esta");
                $(this).toggleClass("esta");
            })
            
        
        
    });

    $("#btEliminarClase").on("click",btEliminarClaseClick);

    function btAgregarclick() {
        $.post("this.php", { fetchListas: localStorage['spp_token'], usr: 0 }, function(data) {
            $(".dta").each(function() {
                $(this).val("");
            });

            $.each(data, function(key, value) {
                $("#" + key).html(value);
            });

            $("#ingreso").val(new Date().toISOString().slice(0, 10));
            $('#modalWindowLabel').text('Agregar Maestro(a)');
            $('.avatar-preview').css('background-image', 'url(../assets/img/usuario.png)');
            $('#modalWindow').modal('show').data("nuevo", true);
        });
    }
    function btEliminarClaseClick(){
        if(confirm("¿Realmente desea eliminar esta clase?")){
            maestro=$("#selectMaestro").val();
            dia=$("#newClassmodal").data("dia");
            hora=$("#newClassmodal").data("hora");
            x={eliminarClase:localStorage['cobaed_token'], maestro:maestro, dia:dia, hora:hora};
             $.post("this.php",x,function(data){
                if(data.ok){
                    $("#newClassmodal").modal("hide");
                    tabla.ajax.reload();
                }else{
                    alert("Error al eliminar la clase: "+data.message);
                }
            });
        }
    }



    function btEditClick(id) {
        controles="";
        coma="";

        $(".dta").each(function(){
            controles += coma +  $(this).attr('id')
            coma=",";
        })
        $.post("this.php", {fetchListas: localStorage['spp_token'], usr: id, controles:controles}, function(data) {            
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
        datos["registrar"]=localStorage['spp_token'];
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
        const columns = ["nombre", "lun", "mar", "mie", "jue", "vie", "sab"]
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
                $(".dtaFiltro").off("change").on("change", function() {
                    tabla.ajax.reload();
                })
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
            alert(xhr.responseText);
            console.error("DataTables AJAX error:", error, thrown);
        }

        function highlightSelectedRows() {
            $('#dataTable').find("tr:first-child").addClass("trSelected");
        }
    }

    function seleccionaArea(dia, hora) {
        $("#newClassmodal").data("dia",dia);
        $("#newClassmodal").data("hora",hora);
        $("#maestro").val($("#selectMaestro option:selected").text());
        $("#dia").val($("#dataTable thead th").eq(dia).text());
        const [inicio, fin] = hora.split('-').map(str => str.trim());
        $("#inicio").val(inicio);
        $("#fin").val(fin);
        
        x={
            fetchAreas:localStorage['cobaed_token'], 
            maestro:$("#selectMaestro").val(), 
            dia:dia, 
            hora:hora};
        $.post("this.php",x,function(data){
            $("#tbAreas tbody").html(data);
            $("#newClassmodal").modal("show");

        }); 
    }

    function btRegistrarClaseClick(){
        const inicio = $("#inicio").val();
        const fin = $("#fin").val();
        const inicioParts = inicio.split(":").map(Number);
        const finParts = fin.split(":").map(Number);
        const inicioDate = new Date(0, 0, 0, inicioParts[0], inicioParts[1]);
        const finDate = new Date(0, 0, 0, finParts[0], finParts[1]);
        const diffMinutes = (finDate - inicioDate) / (1000 * 60);
        if (diffMinutes < 15) {
            alert("La hora de fin debe ser al menos 15 minutos después de la hora de inicio.");
            return;
        }

        if($(".esta").length==0){
            alert("Debe seleccionar un área.");
            return;
        }
   
        maestro=$("#selectMaestro").val();
        dia=$("#newClassmodal").data("dia");
        hora=$("#newClassmodal").data("hora");
        hora=inicio+" - "+fin;
        area=$(".esta").data("id");
        $(".este").html($(".esta").html());
        x={registrarClase:localStorage['cobaed_token'], maestro:maestro, dia:dia, hora:hora, area:area};
         $.post("this.php",x,function(data){
            $("#newClassmodal").modal("hide");
            if(data.ok){
                $("#newClassmodal").modal("hide");
                tabla.ajax.reload();
            }else{
                alert("Error al registrar la clase: "+data.message);
            }
        });

    }