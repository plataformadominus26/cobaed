var tabla;
    $(document).ready(function () {
        if (!localStorage["cobaed_token"]) location.href = "../login.php";
        setMenu("menuConocimientos", "menuTemas");

        $.post(
            "this.php",
            { fetchAcademias: localStorage["cobaed_token"] },
            function (data) {
            $("#academia_id").html(data.academias);
            },
            "json"
        );
        $("#academia_id").on("change", function () {
            $.post(
            "this.php",
            { fetchMaterias: $("#academia_id").val() },
            function (data) {
                $("#materia_id").html(data.materias);
            }
            );
        });
        $("#materia_id").on("change", function () {
            $.post("this.php",{ fetchUnidades: $("#materia_id").val() },
            function (data) {
                $("#unidad_id").html(data.unidades);
            }
            );  
        });
        $("#unidad_id").on("change", function () {
            $.post("this.php",{ fetchTemas: $("#materia_id").val() , unidad: $("#unidad_id").val() },
            function (data) {
                $("#temas_id").html(data.temas);
            }
            );  
        });

        $("#btAiDo").on("click", function(e){    
            e.preventDefault();
            sugiere();
           
        })
    })

    async function sugiere(){
        

        // A. Validar que tengamos el contexto mínimo
        let temaId = $('#temas_id').val(); // Asumo que el select tiene values numéricos
        let temaTexto = $('#temas_id option:selected').text();
        
        if (!temaId || temaId === "Seleccione Tema...") {
            alert("⚠️ Por favor selecciona un Tema primero para que Kobai sepa qué generar.");
            return;
        }

        // B. UI Feedback (Poner al búho a pensar)
        let btnOriginal = $("#btAiDo").html();
        $("#btAiDo").prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');
        
        try {
            // C. Llamada a la API (Usando tu función apiCall existente)
            const response = await apiCall('sugerir_ingesta', {
                tema_id: temaId,
                tema: temaTexto // Enviamos el texto también por si acaso
            });

            if(response.ok) {
                const data = response.data;
                console.log("🧠 Cerebro Kobai:", data); // Para debug

                // --- D. DISTRIBUIR CONTENIDO EN LAS PESTAÑAS ---

                // 1. Pestaña TEXTO / TEORÍA
                // Convertimos el Markdown a HTML y agregamos la analogía
                let htmlTeoria = marked.parse(data.contenido_teorico_markdown);
                
                // Inyectamos un bloque especial para la analogía
                let htmlAnalogia = `
                    <div class="alert alert-warning border-start border-4 border-warning bg-light">
                        <h6 class="text-warning fw-bold"><i class="fas fa-lightbulb"></i> Analogía Kobai:</h6>
                        <p class="mb-0 small">${data.analogia_vida_real}</p>
                    </div>
                `;

                // Escribimos en el textarea (o div editable si usas Summernote)
                // Si usas un textarea simple:
                $('textarea[name="contenido_texto"]').val(data.contenido_teorico_markdown + "\n\n> Analogía: " + data.analogia_vida_real);
                
                // NOTA: Si usas Summernote u otro editor rico, usa:
                // $('#summernote').summernote('code', htmlTeoria + htmlAnalogia);


                // 2. Pestaña EJERCICIOS
                // Limpiamos el contenedor actual
                $('#exercises-container').empty();
                
                // Iteramos los ejercicios que trajo la IA
                data.ejercicios_practicos.forEach((ej, index) => {
                    agregarEjercicioUI(ej, index);
                });
                
                // Activar pestaña de ejercicios para que el usuario vea que hay cambios (Opcional)
                // $('#ejercicios-tab').tab('show');


                // 3. Pestaña VIDEOS
                if(data.recursos_externos && data.recursos_externos.video_query_sugerido) {
                    // Aquí podrías disparar la búsqueda automática en YouTube que hablamos antes
                    // Por ahora, llenamos el input como sugerencia
                    $('input[name="video_url"]').val(data.recursos_externos.video_query_sugerido);
                    // O si lograste la URL directa: 
                    // $('input[name="video_url"]').val(data.recursos_externos.video_url);
                }


                // 4. Referencia Bibliográfica
                if(data.recursos_externos && data.recursos_externos.bibliografia_sugerida) {
                    let libro = data.recursos_externos.bibliografia_sugerida;
                    let refTexto = `${libro.autor}. ${libro.titulo}.`;
                    if(libro.isbn) refTexto += ` ISBN: ${libro.isbn}`;
                    $('input[name="referencia"]').val(refTexto);
                }

                // 5. Renderizar Matemáticas (LaTeX)
                // Esto busca cualquier $$...$$ nuevo en el DOM y lo convierte a fórmula bonita
                if(window.MathJax) {
                    MathJax.typesetPromise(); 
                }

                // Notificación de éxito
                alert("✅ ¡Kobai ha generado el contenido exitosamente! Revisa las pestañas.");

            } else {
                alert("❌ Error: " + response.message);
            }

        } catch (error) {
            console.error(error);
            alert("Error de comunicación: " + error.message);
        } finally {
            // Restaurar botón
            $(this).prop('disabled', false).html(btnOriginal);
        }
    }
    // Función auxiliar para dibujar la tarjeta de ejercicio
    // Función auxiliar mejorada con VISTA PREVIA
function agregarEjercicioUI(ejercicioData, index) {
    // Preparamos el texto del desarrollo
    let pasosTexto = "SOLUCIÓN PASO A PASO:\n";
    ejercicioData.solucion_paso_a_paso.forEach(p => {
        pasosTexto += `- ${p.paso}: ${p.explicacion} (${p.resultado_parcial_latex})\n`;
    });
    pasosTexto += `\nRESULTADO FINAL: ${ejercicioData.resultado_final_latex}`;

    // Generamos IDs únicos para conectar el textarea con su preview
    let idPlanteamiento = `plant_${index}`;
    let idSolucion = `sol_${index}`;

    let template = `
    <div class="card bg-light border-0 mb-3 p-3 exercise-card shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold text-success m-0"><i class="fas fa-calculator"></i> Ejercicio ${index + 1}</h6>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="$(this).closest('.exercise-card').remove()"><i class="fas fa-trash"></i></button>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label class="small fw-bold text-muted">Pregunta / Planteamiento (LaTeX)</label>
                <textarea class="form-control mb-2 font-monospace" rows="2" 
                    onkeyup="renderMathPreview(this, '#preview_${idPlanteamiento}')">${ejercicioData.planteamiento}</textarea>
                
                <div class="card card-body bg-white border p-2 mb-2" style="min-height: 50px;">
                    <small class="text-muted d-block" style="font-size: 10px;">VISTA PREVIA:</small>
                    <div id="preview_${idPlanteamiento}" class="fw-bold text-dark">${ejercicioData.planteamiento}</div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="small fw-bold text-muted">Desarrollo y Solución (LaTeX)</label>
                <textarea class="form-control form-control-sm font-monospace small mb-2" rows="6" 
                    onkeyup="renderMathPreview(this, '#preview_${idSolucion}')">${pasosTexto}</textarea>
                
                <div class="card card-body bg-white border p-2" style="min-height: 100px; max-height: 200px; overflow-y: auto;">
                    <small class="text-muted d-block" style="font-size: 10px;">VISTA PREVIA:</small>
                    <div id="preview_${idSolucion}" class="small text-dark">${parseMarkdown(pasosTexto)}</div>
                </div>
            </div>
        </div>
    </div>`;

    $('#exercises-container').append(template);
    
    // Renderizar las matemáticas iniciales
    if(window.MathJax) {
        MathJax.typesetPromise();
    }
}

// Nueva función pequeña para renderizar en tiempo real cuando escribes
function renderMathPreview(textarea, targetSelector) {
    let texto = $(textarea).val();
    
    // Si es el campo de solución, aplicamos formato markdown básico (saltos de línea)
    if(targetSelector.includes('sol_')) {
        texto = parseMarkdown(texto); 
    }
    
    $(targetSelector).html(texto);
    
    // Le pedimos a MathJax que vuelva a pintar ese div específico
    if(window.MathJax) {
        MathJax.typesetPromise([document.querySelector(targetSelector)]);
    }
}

// Helper simple para saltos de linea (si no tienes la librería marked cargada en este contexto)
function parseMarkdown(text) {
    if (!text) return '';
    // Convertir saltos de línea (\n) a <br>
    return text.replace(/\n/g, '<br>').replace(/\$\$/g, '$$$$'); 
}
 
