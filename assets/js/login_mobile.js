    $(document).ready(function(){
        apiCall("getMascot",{})
        .then(function(response){
            if(response.ok){
                $('#mascotImg').attr('src', response.data);
            }
        })
                
        // Lógica del botón "Ver Contraseña" (Hold to view)
        const $btnEye = $('#btn-view-pass');
        const $inputPass = $('#txtPass');
        const $iconEye = $btnEye.find('i');

        // Eventos para mouse (PC) y touch (Móvil)
        $btnEye.on('mousedown touchstart', function(e) {
            e.preventDefault(); // Evita perder el foco
            $inputPass.attr('type', 'text');
            $iconEye.removeClass('bi-eye').addClass('bi-eye-slash');
        });

        $btnEye.on('mouseup mouseleave touchend', function(e) {
            $inputPass.attr('type', 'password');
            $iconEye.removeClass('bi-eye-slash').addClass('bi-eye');
        });

        $('#btLogin').on('click', function(){
            const username = $('#txtUser').val().trim();
            const password = $('#txtPass').val().trim();

            if(username === "" || password === "") {
                alerta("Por favor, complete todos los campos.","danger", 13000);
                return;
            }
            apiCall("login", { username: username, password: password })
            .then(function(response) {
                if(response.ok) {
                    response = response.data;
                    localStorage["cobaed_nombre"] = response.nombre;
                    localStorage["cobaed_puesto"] = response.puesto;
                    localStorage["cobaed_token"] = response.tkn;
                    let redir="";
                    if(response.tipo=="alumno"){
                        localStorage["cobaed_aTkn"] = response.tkn;
                        redir="cobaedsn";
                    }
                    else{
                        localStorage["cobaed_tTkn"] = response.tkn;
                        redir="app_docentes";
                    }
                    redir=response["tipo"]=="alumno"?"cobaedsn":"app_docentes";
                    window.location.href = redir; // Redirigir al dashboard
                } else {
                    alerta("Credenciales incorrectas. Intente nuevamente.","danger", 13000);
                }
            })
            .catch(function(error) {
                alerta("Error en la conexión. Intente nuevamente más tarde.","danger", 13000);
            });
        });

    })