$.post("this.php", { action: "getConfig" }, function(data) {
            if(data.mascotImg) {
                $('#mascotImg').attr('src', data.mascotImg);
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
                alert("Por favor, complete todos los campos.");
                return;
            }

            $.post("this.php", { action: "login", username: username, password: password }, function(response) {
                if(response.ok === "true") {
                    localStorage["cobaed_tkn"] = response.token;
                    localStorage["cobaed_nombre"] = response.nombre;
                    
                    window.location.href = "index.html"; // Redirigir al dashboard
                } else {
                    alert("Credenciales incorrectas. Intente nuevamente.");
                }
            }, 'json');
        })