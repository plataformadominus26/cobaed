  const priorityCycle = [
      { value: '0', class: 'bg-success', text: 'Baja' },
      { value: '1', class: 'bg-warning text-dark', text: 'Media' },
      { value: '2', class: 'bg-danger', text: 'Alta' },
    ];
 var tabla;
    $(document).ready(function() {
        $(document).on("click", ".sp24", function(e) {
            self=e.currentTarget;
            control=$(self).closest("td").find(".h24");
            $(control).toggleClass("d-none").focus()
            $(self).toggleClass("d-none");
        })
        .on("change", ".h24", function(e) {
            self=e.currentTarget;
            valor=$(self).val();
            valor24h = valor.replace(/^(\d{1,2}):(\d{2})\s*(AM|PM)?$/i, function(_, h, m, ap) {
                h = parseInt(h, 10);
                if (ap) {
                    if (ap.toUpperCase() === 'PM' && h < 12) h += 12;
                    if (ap.toUpperCase() === 'AM' && h === 12) h = 0;
                }
                return (h < 10 ? '0' : '') + h + ':' + m;
            }); control=$(self).closest("td").find(".sp24");
            $(control).toggleClass("d-none").html(valor24h);

            $(self).toggleClass("d-none");
        })
            setMenu("menuAreas","tareasSubMenu");
            $("#btRegistrar").on('click', btRegistrarclick);
            $("#btAgregar").on('click', btAgregarclick);
            $.post("this.php",{fetchFiltros:localStorage["cobaed_token"]},(filtros)=>{doTabla(filtros);});
            $("#tareasTable").on("click", ".btCamara , .chDia", function(e) {
                 $(this).toggleClass("si");
            })
            .on('click', '.bgPrioridad', function() {
                const $badge = $(this);
                const currentValue = $badge.text().trim();
                const currentIndex = priorityCycle.findIndex(p => p.text === currentValue);
                const nextIndex = (currentIndex + 1) % priorityCycle.length;
                const nextPriority = priorityCycle[nextIndex];
                $badge
                    .removeClass('bg-danger bg-warning text-dark bg-success')
                    .addClass(nextPriority.class)
                    .text(nextPriority.text)
                    .attr('data-prioridad', nextPriority.value);
                    $badge.trigger('priority-change', [nextPriority.value]);
            })
            .on('click', '.btEliminarTarea', function() {
                if (confirm('¿Está seguro de eliminar esta tarea?')) {
                    $(this).closest('tr').fadeOut(500, function() {
                        $(this).remove();
                    });
                }  
            })
            .on('click', '.btDuplicarTarea', function() {
                const $tr = $(this).closest('tr');
                const $clone = $tr.clone(true, true);
                $clone.hide();
                $tr.after($clone);
                $clone.fadeIn(400);
            })

             $(document).on("click" ,'.btn-edit',function(e) {
                e.preventDefault();
                self=e.currentTarget;
                $(".trSelected").removeClass("trSelected");
                $(self).closest('tr').addClass("trSelected");
                btEditClick();
            })
            .on("click", "#dataTable tbody tr", function() {
                $(".trSelected").removeClass("trSelected");
                $(this).toggleClass("trSelected");
            })
            .on("dblclick", "#dataTable tbody tr", function() {
                $(this).find('.btn-edit').trigger('click');
            })
            .on("click",'.btn-delete',(e)=>{
                if (confirm('¿Está seguro de eliminar este usuario?')) {
                    tkn = $(e.currentTarget).closest('tr').attr('id').split("_")[1];
                    $(e.$urrentTarget).closest('tr').addClass("bye");
                    $.post('this.php', { eliminar: tkn}, function(response) {
                        
                        $(".bye").fadeOut(1300, function() {
                         $(".bye").remove();
                        });

                            tabla.ajax.reload();
                         
                    });
                }
            })
            .on("click", ".btn-qr", function() {
                var area = $(this).closest("tr").attr('id').split("_")[1];
                var nombre = $(this).closest("tr").find("td").eq(1).text().trim().replace(/\u00a0/g, "");
                window.open("qr.php?data=" + area + "&text=" + nombre, "_blank");

            });        
            
            // Avatar upload preview
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('.avatar-preview').css('background-image', 'url('+e.target.result+')');
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            
            $("#imageUpload").change(function() {
                readURL(this);
            });

    $('#tareasTable')
    .on('focus', '.edAccion', function () {
        const $input = $(this);
        if (!$input.data('autocomplete-init')) {
            $input.autocomplete({
                source: function (request, response) {
                    $.getJSON('this.php', {
                        autocomplete: localStorage['cobaed_token'],
                        term: request.term
                    })
                        .done(response)
                        .fail(function () {
                            response([]);
                        });
                },
                minLength: 1,
                autoFocus: true,
                appendTo: $(this).closest('.modal-body'),
                position: {
                    my: "left top",
                    at: "left bottom",
                    collision: "flip"
                }
            }).autocomplete("instance")._renderItem = function (ul, item) {
                return $("<li>")
                    .append("<div>" + item.label + "</div>")
                    .appendTo(ul);
            };
            $input.data('autocomplete-init', true);
        }
        $input.select();
    })
    .on("focus", ".time-picker", function () {
        const $input = $(this);
        if (!$input.data('timepicker-init')) {
            $input.timepicker({
                timeFormat: 'HH:mm',
                interval: 30, // 30 minute intervals
                minTime: '08:00',
                maxTime: '20:00',
                startTime: '08:00',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });
            $input.data('timepicker-init', true);
        }
        $input.select();
    });  $("#btAddTarea").on('click', addTareaClick);
        

    });

    

    function addTareaClick() {
         // Create new row with all elements
         rowCounter = $('#tareasTable tr').length; // Get current row count
        const newRow = $(`
        <tr class="trTarea" data-id="-1">
            <td>
                <button class="btn btn-sm btn-danger me-1 btEliminarTarea" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
                <button class="btn btn-sm btn-success me-1 btDuplicarTarea" title="Duplicar">
                    <i class="bi bi-plus-square-dotted"></i>
                </button>

            </td>
            <td>
                <button type="button" class="btn btn-sm btn-light d-flex align-items-center justify-content-center w-100 border btCamara" id="foto${rowCounter}" name="foto[${rowCounter}]" title="Requiere foto">
                    <i class="bi bi-check-circle-fill text-success d-none chSi"></i>
                    <i class="bi bi-circle text-success chNo"></i>
                    <i class="fas fa-camera ms-2"></i>
                </button>
            </td>
            <td><input type="text" class="form-control edAccion" value="Nueva Tarea"></td>
            <td class="text-center chDia">
                <i class="bi bi-check-circle-fill text-success d-none chSi"></i>
                <i class="bi bi-circle text-success chNo"></i>
            </td>
            <td class="text-center chDia">
                <i class="bi bi-check-circle-fill text-success d-none chSi"></i>
                <i class="bi bi-circle text-success chNo"></i>
            </td>
            <td class="text-center chDia">
                <i class="bi bi-check-circle-fill text-success d-none chSi"></i>
                <i class="bi bi-circle text-success chNo"></i>
            </td>
            <td class="text-center chDia">
                <i class="bi bi-check-circle-fill text-success d-none chSi"></i>
                <i class="bi bi-circle text-success chNo"></i>
            </td>
            <td class="text-center chDia">
                <i class="bi bi-check-circle-fill text-success d-none chSi"></i>
                <i class="bi bi-circle text-success chNo"></i>
            </td>
            <td class="text-center chDia">
                <i class="bi bi-check-circle-fill text-success d-none chSi"></i>
                <i class="bi bi-circle text-success chNo"></i>
            </td>
            <td class="text-center chDia">
                <i class="bi bi-check-circle-fill text-success d-none chSi"></i>
                <i class="bi bi-circle text-success chNo"></i>
            </td>
            <td class="time-picker-cell"><input type="text" class="time-picker" value="17:00"></td>
            <td>
                <span class="badge bgPrioridad bg-success" data-prioridad="0">Baja</span>
            </td>
            <td class="text-center cbEmpleado">
                <select class="form-select">
                    <option value="-1">USR</option>
                    <option value="-1">JHH</option>
                    <option value="-1">RBH</option>
                    <option value="-1">RBG</option>
                    
                </select>
            </td>
        </tr>
        `);
        
        // Append to table
        $('#tareasTable tbody').prepend(newRow);
        
        
        // Increment counter for next row
        rowCounter++;
         
    } 
    
    
// Call the function when DOM is ready
    function btAgregarclick() {
        $("#tareasTable tbody").empty();
        $("#btAddTarea").trigger("click");
        $("#nombre").val("");
        $("#modalWindow").data("nuevo", true).modal('show');
        }




    function btEditClick() {
        
        $.post("this.php", {fetch_tareas:$(".trSelected").attr("id").split("_")[1]}, function(data) {     

            $(".h24").attr('lang', 'en-GB'); // British English uses 24-hour format
            $("#nombre").val(data.area);
            $("#tareasTable tbody").empty()
            .html(data.tareas)  
            $('#modalWindowLabel').text('Editar Área');
            $('#modalWindow').modal('show')
            .data("nuevo", false);
        });
    }

function btRegistrarclick() {
    var $nombre = $('#nombre');
    if ($nombre.val().trim() === '') {
        $nombre.addClass('is-invalid').removeClass('is-valid');
    } else {
        $nombre.removeClass('is-invalid').addClass('is-valid');
    }
    if ($('.is-invalid').length === 0) {  
        var formData = {};
        formData["nombre"] = $nombre.val();
        formData["tipo_id"] = $("#tipo_id").val();
        var tareas = [];
        $("#tareasTable tbody tr").each(function() {
            var $tr = $(this);
            var tarea = {
                foto: $tr.find('.btCamara').hasClass('si') ? 1 : 0,
                tarea: $tr.find('.edAccion').val(),
                dia_1: $tr.find('.chDia').eq(0).hasClass('si') ? 1 : 0,
                dia_2: $tr.find('.chDia').eq(1).hasClass('si') ? 1 : 0,
                dia_3: $tr.find('.chDia').eq(2).hasClass('si') ? 1 : 0,
                dia_4: $tr.find('.chDia').eq(3).hasClass('si') ? 1 : 0,
                dia_5: $tr.find('.chDia').eq(4).hasClass('si') ? 1 : 0,
                dia_6: $tr.find('.chDia').eq(5).hasClass('si') ? 1 : 0,
                dia_7: $tr.find('.chDia').eq(6).hasClass('si') ? 1 : 0,
                hora: $tr.find('.time-picker').val(),
                prioridad: $tr.find('.bgPrioridad').attr('data-prioridad'),
                id: $tr.data('id') || -1 // Use data-id attribute for new tasks
            };
             tareas.push(tarea);
        });
        formData["tareas"] = tareas;
        formData["uid"] = localStorage['cobaed_token'];
        formData["registrar"] = $('#modalWindow').data("nuevo");
        var selectedRow = $("#dataTable .trSelected");
        formData["tkn"] = (selectedRow.length && selectedRow.attr("id")) ? selectedRow.attr("id").split("_")[1] : "";

        $.post('this.php', formData, function(data) {
            
                $('#modalWindow').modal('hide');
                tabla.ajax.reload();
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert('Error al registrar: ' + (jqXHR.responseText || textStatus));
        }); }

}
    function doTabla(filtros){
        const $window = $(window);
        const isMobile = $window.width() <= 768;
        const token = localStorage["obranet_token"];
        const columns = ["acciones", "nombre","tipo", "lun", "mar", "mie", "jue", "vie", "sab", "total", "tareas"]
        .map(column => ({ data: column }));
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
            drawCallback: highlightSelectedRows
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
            

            $(".dataTables_length label").addClass("d-none")
            
           
             
        }

        function handleAjaxError(xhr, error, thrown) {
            console.error("DataTables AJAX error:", error, thrown);
        }

        function highlightSelectedRows() {
            $('#dataTable').find("tr:first-child").addClass("trSelected");
        }
    }