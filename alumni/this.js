 var tabla;
     var rtdbCapturedBlob = null;
     var rtdbCapturedUrl = "";
     var alumniFilters = {
        fltNombre: "",
        fltSemestre: "-1",
        fltGrupo: "-1",
        fltTurno: "-1",
        fltEstado: "-1"
     };
    $(document).ready(function () {
        if (!localStorage["cobaed_token"]) 
            location.href = "../login.php";
    $("#menuAlumnos").addClass("active");
    $("#btRegistrar").on("click", btRegistrarclick);
    $("#btAgregar").on("click", btAgregarclick);
    doTabla();
    createAlumniOpsUI();
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
            $(e.currentTarget).closest("tr").addClass("bye");
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
        })
        .on("click", "#btFiltroAvanzado", function () {
            $("#modalFiltroAlumni").modal("show");
        })
        .on("click", "#btAplicarFiltrosAlumni", function () {
            alumniFilters.fltNombre = ($("#fltNombre").val() || "").trim();
            alumniFilters.fltSemestre = $("#fltSemestre").val() || "-1";
            alumniFilters.fltGrupo = $("#fltGrupo").val() || "-1";
            alumniFilters.fltTurno = $("#fltTurno").val() || "-1";
            alumniFilters.fltEstado = $("#fltEstado").val() || "-1";
            tabla.ajax.reload();
            $("#modalFiltroAlumni").modal("hide");
            updateFiltroBadge();
        })
        .on("click", "#btLimpiarFiltrosAlumni", function () {
            alumniFilters = { fltNombre: "", fltSemestre: "-1", fltGrupo: "-1", fltTurno: "-1", fltEstado: "-1" };
            $("#modalFiltroAlumni").find("input,select").each(function() { $(this).val($(this).attr("data-default") || ""); });
            tabla.ajax.reload();
            updateFiltroBadge();
        })
        .on("click", "#btMensajesBatch", function () {
            $("#batchResult").html("Selecciona un filtro y pulsa Previsualizar.");
            $("#modalMensajesBatch").modal("show");
        })
        .on("click", "#btPreviewBatch", function () {
            previewBatchTargets();
        })
        .on("click", "#btEnviarBatch", function () {
            sendBatchMessages();
        })
        .on("click", ".bi-qr-code", function () {
            id = $(this).closest("tr").attr("id").split("_")[1];
            $("#qrModalLabel").text("QR de " + $(this).closest("tr").find("td:nth-child(2)").text());
            xDeprecated="https://api.qrserver.com/v1/create-qr-code/?data=" + location.host + "/cobaed/enrollalumni.php?atkn=" +id +"&size=150x150"
            payload=location.origin + "/validid?payload="+id+"&logo=logocobaed.png&style=neon"
            url='../../--id/assets/api/qr.php?data='+payload;
            $("#qrImage").attr("src",url);

            $("#qrModal").modal("show");
        })
        .on("click", "#btImprimirQR", function () {
            id = $(".trSelected").attr("id").split("_")[1];
            if (!id) {
            alert("Seleccione un alumno para imprimir su QR");
            return;
            }
            window.open("https://api.qrserver.com/v1/create-qr-code/?data=" + location.host + "/cobaed/enrollalumni.php?atkn=" + id + "&size=150x150", "_blank");
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

        $.each(data, function (key, value) {
            $("#" + key).html(value);
        });
        $("#ingreso").val(new Date().toISOString().slice(0, 10));
        $("#modalWindowLabel").text("Agregar Alumno(a)");
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
        $(".avatar-preview").css('background-image', 'url(../assets/img/usuario.png)');
        $.post("this.php", {fetchListas: localStorage['cobaed_token'], usr: id, controles:controles}, function(data) {            
            $.each(data, function(key, value) {
                $("#"+key).html(value);
            });

            $.each(data["_controles_"], function(key, value) {
                $("#"+key).val(value);
            });
            if(data["img"]!="")
                $(".avatar-preview").css('background-image', 'url(../assets/uploads/'+data["img"]+')'); 
            else
                $(".avatar-preview").css('background-image', 'url(../assets/img/usuario.png)');
            $('#modalWindowLabel').text('Editar Alumno(a)');
            $('#modalWindow').modal('show')
            .data("nuevo", false);
        });
    }

function btRegistrarclick() {
    // 1. Validación visual
    $('.dta').each(function() {
        tipo=$(this).prop("tagName").toLowerCase();
        switch(tipo) {
            case "input":
                valido = $(this).val().trim() !== ""; 
            break;
            case "select":
                valido = $(this).val() !== "-1" && $(this).val() !== null;
            break;
            default:
                valido=true; // Otros tipos de controles no se validan por ahora
        }

        if (!valido) {
            $(this).addClass('is-invalid').removeClass('is-valid');
            alert('Por favor, complete el campo: ' + $(this).parent().find('label').text());
            return false; // Detiene el proceso si hay un campo inválido
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    });

    if ($('.is-invalid').length === 0) {
        // Inicializamos FormData correctamente
        var formData = new FormData();
        
        // 2. Recolección de datos de texto
        var dtaFields = {};
        $('.dta').each(function() {
            var id = $(this).attr('id');
            if (id) dtaFields[id] = $(this).val();
        });

        // 3. Llenado del FormData (Usando .append)
        formData.append("registrar", localStorage["cobaed_token"]);
        formData.append("dta", JSON.stringify(dtaFields)); // Lo enviamos como JSON para el PHP
        formData.append("nuevo", $("#modalWindow").data("nuevo"));
        
        var selectedId = $(".trSelected").attr("id");
        var token = $("#modalWindow").data("nuevo") ? "" : (selectedId ? selectedId.split("_")[1] : "");
        formData.append("token", token);

        // 4. AGREGAR LA FOTO REAL
        var fileInput = $("#imageUpload")[0];
        if (fileInput.files && fileInput.files[0]) {
            formData.append("foto", fileInput.files[0]); // Aquí va el binario
        } else if (rtdbCapturedBlob) {
            formData.append("foto", rtdbCapturedBlob, "rtdb_capture.jpg");
        }
        formData.append("rtdb_image_url", rtdbCapturedUrl || "");

        // 5. Envío mediante $.ajax (Obligatorio para archivos)
        $.ajax({
            url: 'this.php',
            type: 'POST',
            data: formData,
            processData: false, // Evita que jQuery transforme el FormData en string
            contentType: false, // Evita que jQuery ponga un Content-Type incorrecto
            dataType: 'json',
            success: function(response) {
                if (response.ok) {
                    $('#modalWindow').modal('hide');
                    tabla.ajax.reload();
                    alert('Usuario registrado correctamente');
                } else {
                    alert('Error: ' + response.message);
                }
            },
error: function(xhr, status, error) {
    console.log("Status: " + status);
    console.log("Error: " + error);
    console.log("Respuesta del servidor: " + xhr.responseText); // ESTO TE DIRÁ EL ERROR REAL
}
        });
    }
}
        function doTabla(){
        const $window = $(window);
        const isMobile = $window.width() <= 768;
        const token = localStorage["obranet_token"];
        const columns = ["acciones","nombre", "semestre","grupo", "telefono", "sexo", "estado","campus"]
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
            d.fltNombre = alumniFilters.fltNombre;
            d.fltSemestre = alumniFilters.fltSemestre;
            d.fltGrupo = alumniFilters.fltGrupo;
            d.fltTurno = alumniFilters.fltTurno;
            d.fltEstado = alumniFilters.fltEstado;
            d["token"] = localStorage["cobaed_token"];
            return d;
        }


        function initializeControls() {
            $(".dataTables_info").addClass("p-3");
             $(".dataTables_paginate").addClass("px-3");
            $(".dataTables_filter").addClass("p-1 px-3");
            $(".dataTables_length").addClass("d-none");

            const controlsHtml = `
            <button class="btn btn-outline-primary" id="btFiltroAvanzado">Filtro</button>
            <button class="btn btn-outline-success" id="btMensajesBatch">Mensajes</button>
            <span id="filtroBadge" class="badge bg-secondary">Sin filtro</span>`;

            const $toolbar = $("#alumniToolbar");
            if ($toolbar.length && !$toolbar.find("#btFiltroAvanzado").length) {
                $toolbar.html(controlsHtml);
            }
            updateFiltroBadge();
             
        }

        function handleAjaxError(xhr, error, thrown) {
            console.error("DataTables AJAX error:", error, thrown);
        }

        function highlightSelectedRows() {
            $('#dataTable').find("tr:first-child").addClass("trSelected");
        }
    }

    // ===== Listener RTDB real (Firebase client SDK) =====
    var rtdbUnsubscribe = null;
    var rtdbStationToken = '';

    (function initRtdbListener() {
        startRtdbListener();
    })();

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[src="' + src + '"]');
            if (existing) {
                resolve();
                return;
            }

            var script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = function () {
                script.dataset.loaded = '1';
                resolve();
            };
            script.onerror = function () {
                reject(new Error('No se pudo cargar: ' + src));
            };
            document.head.appendChild(script);
        });
    }

    function ensureFirebaseSdk() {
        if (window.firebase && typeof window.firebase.database === 'function') {
            return Promise.resolve();
        }

        return loadScript('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js')
            .then(function () {
                return loadScript('https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js');
            });
    }

    function ensureFirebaseConfig() {
        if (window.RTDB_CONFIG || window.firebaseConfig) {
            return Promise.resolve();
        }

        return loadScript('firebase-config.js').catch(function () {
            return Promise.resolve();
        });
    }

    function startRtdbListener() {
        ensureFirebaseConfig()
            .then(function () {
                return ensureFirebaseSdk();
            })
            .then(function () {
                var config = window.RTDB_CONFIG || window.firebaseConfig;
                if (!config) {
                    console.warn('RTDB listener inactivo: falta config en firebase-config.js (window.RTDB_CONFIG o window.firebaseConfig).');
                    return;
                }

                if (!window.firebase.apps.length) {
                    window.firebase.initializeApp(config);
                }

                var token = String(localStorage["cobaed_camara"] || '').trim();
                if (!token) {
                    console.warn('RTDB listener inactivo: falta localStorage["cobaed_camara"].');
                    return;
                }
                rtdbStationToken = token;

                var configuredPath = window.RTDB_LISTENER_PATH || window.rtdbListenerPath || 'cam_uploads/{token}/latest';
                var listenerPath = String(configuredPath).replace('{token}', token);
                var updatesRef = window.firebase.database().ref(listenerPath);
                var onListenError = function (error) {
                    console.error('Error listener RTDB (' + listenerPath + '):', error);
                };

                var onValueUpdate = function (snapshot) {
                    var item = snapshot.val();
                    if (!item || typeof item !== 'object') {
                        return;
                    }

                    var estacion = item.estacion || '';
                    if (rtdbStationToken && estacion && estacion !== rtdbStationToken) {
                        return;
                    }

                    var imageUrl = item.url || item.image_url || '';
                    if (!imageUrl) {
                        return;
                    }

                    $('.avatar-preview').css('background-image', 'url(' + imageUrl + ')');
                    cacheRtdbImageForSubmit(imageUrl);
                };

                updatesRef.on('value', onValueUpdate, onListenError);

                rtdbUnsubscribe = function () {
                    updatesRef.off('value', onValueUpdate);
                };
            })
            .catch(function (error) {
                console.error('Error iniciando listener RTDB:', error);
            });
    }

    window.addEventListener('beforeunload', function () {
        if (typeof rtdbUnsubscribe === 'function') {
            rtdbUnsubscribe();
        }
    });

    function cacheRtdbImageForSubmit(imageUrl) {
        if (!imageUrl) {
            rtdbCapturedBlob = null;
            rtdbCapturedUrl = "";
            return;
        }

        rtdbCapturedUrl = imageUrl;
        if (!window.fetch) {
            return;
        }

        fetch(imageUrl, { cache: 'no-store' })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.blob();
            })
            .then(function (blob) {
                if (blob && blob.size > 0) {
                    rtdbCapturedBlob = blob;
                }
            })
            .catch(function (error) {
                console.warn('No se pudo cachear captura RTDB para submit:', error);
                rtdbCapturedBlob = null;
            });
    }

    function createAlumniOpsUI() {
        if (!document.getElementById("modalFiltroAlumni")) {
            $("body").append(`
                <div class="modal fade" id="modalFiltroAlumni" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header"><h5 class="modal-title">Filtros de Alumnos</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body row g-3">
                                <div class="col-md-6"><label class="form-label">Nombre</label><input class="form-control" id="fltNombre" data-default="" placeholder="match parcial"></div>
                                <div class="col-md-3"><label class="form-label">Semestre</label><select class="form-select" id="fltSemestre" data-default="-1"><option value="-1">Todos</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option></select></div>
                                <div class="col-md-3"><label class="form-label">Grupo</label><input class="form-control" id="fltGrupo" data-default="-1" placeholder="Ej. A"></div>
                                <div class="col-md-6"><label class="form-label">Turno</label><select class="form-select" id="fltTurno" data-default="-1"><option value="-1">Todos</option><option value="1">Matutino</option><option value="2">Vespertino</option><option value="3">Nocturno</option></select></div>
                                <div class="col-md-6"><label class="form-label">Estado</label><select class="form-select" id="fltEstado" data-default="-1"><option value="-1">Todos</option><option value="1">Activo</option><option value="0">Inactivo</option><option value="2">Vacaciones</option><option value="3">Baja</option></select></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" id="btLimpiarFiltrosAlumni">Limpiar</button>
                                <button type="button" class="btn btn-primary" id="btAplicarFiltrosAlumni">Aplicar</button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }

        if (!document.getElementById("modalMensajesBatch")) {
            $("body").append(`
                <div class="modal fade" id="modalMensajesBatch" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header"><h5 class="modal-title">Mensajes Batch</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body">
                                <div class="mb-2"><label class="form-label">Título</label><input id="msgTitulo" class="form-control" maxlength="120"></div>
                                <div class="mb-2"><label class="form-label">Mensaje</label><textarea id="msgTexto" class="form-control" rows="3" maxlength="400"></textarea></div>
                                <div id="batchResult" class="alert alert-secondary">Selecciona un filtro y pulsa Previsualizar.</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-primary" id="btPreviewBatch">Previsualizar target</button>
                                <button type="button" class="btn btn-success" id="btEnviarBatch">Enviar</button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
    }

    function updateFiltroBadge() {
        const active = Object.values(alumniFilters).filter(v => String(v).trim() !== "" && String(v) !== "-1").length;
        const $badge = $("#filtroBadge");
        if (!$badge.length) return;
        if (active === 0) {
            $badge.removeClass("bg-primary").addClass("bg-secondary").text("Sin filtro");
        } else {
            $badge.removeClass("bg-secondary").addClass("bg-primary").text(active + " filtros");
        }
    }

    function buildBatchPayload(baseKey) {
        return {
            [baseKey]: localStorage["cobaed_token"],
            fltNombre: alumniFilters.fltNombre,
            fltSemestre: alumniFilters.fltSemestre,
            fltGrupo: alumniFilters.fltGrupo,
            fltTurno: alumniFilters.fltTurno,
            fltEstado: alumniFilters.fltEstado,
            titulo: $("#msgTitulo").val() || "",
            mensaje: $("#msgTexto").val() || ""
        };
    }

    function previewBatchTargets() {
        const payload = buildBatchPayload("previewBatchPush");
        $.post("this.php", payload, function(resp) {
            if (!resp || !resp.ok) {
                $("#batchResult").removeClass("alert-secondary alert-success").addClass("alert-danger").text("No se pudo previsualizar target");
                return;
            }
            $("#batchResult").removeClass("alert-secondary alert-danger").addClass("alert-success").html("Target: <b>" + resp.total + "</b> alumnos");
        }, "json").fail(function(xhr){
            $("#batchResult").removeClass("alert-secondary alert-success").addClass("alert-danger").text("Error preview: " + xhr.status);
        });
    }

    function sendBatchMessages() {
        const payload = buildBatchPayload("sendBatchPush");
        if (!payload.titulo.trim() || !payload.mensaje.trim()) {
            $("#batchResult").removeClass("alert-secondary alert-success").addClass("alert-danger").text("Captura título y mensaje");
            return;
        }
        $.post("this.php", payload, function(resp) {
            if (!resp || !resp.ok) {
                $("#batchResult").removeClass("alert-secondary alert-success").addClass("alert-danger").text("No se pudo enviar batch");
                return;
            }
            var queued = (typeof resp.queued !== "undefined") ? resp.queued : resp.sent;
            $("#batchResult").removeClass("alert-secondary alert-danger").addClass("alert-success").html("En cola: <b>" + queued + "</b>" + (resp.message_id ? " | id: " + resp.message_id : ""));
        }, "json").fail(function(xhr){
            $("#batchResult").removeClass("alert-secondary alert-success").addClass("alert-danger").text("Error envío: " + xhr.status);
        });
    }
    