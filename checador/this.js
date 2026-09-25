let minuto =0
var qr = "";

    $(document).ready(function () {
        $("#dNombre").data("default-text", $("#dNombre").html());
        if( localStorage["spp_estacion"] === undefined )
            localStorage["spp_estacion"] = $("#hEstacion").val();

          // Sound slider logic
                const soundSlider = document.getElementById('soundSlider');
                const soundIcon = document.getElementById('soundIcon');
                const soundToggleBtn = document.getElementById('soundToggleBtn');

                soundSlider.addEventListener('input', function() {
                    updateSoundIcon(this.value);
                    if (this.value == 1) {
                        if (!dingSound.paused) {
                            dingSound.pause();
                            dingSound.currentTime = 0;
                        }
                        dingSound.play();
                    }
                    // You can add your sound enable/disable logic here
                    // Example: window.soundEnabled = (this.value == 1);
                });

                soundToggleBtn.addEventListener('click', function() {
                    soundSlider.value = soundSlider.value == 1 ? 0 : 1;
                    soundSlider.dispatchEvent(new Event('input'));
                });

                // Initialize icon
                updateSoundIcon(soundSlider.value);
        fetchNewQR(); 
        $("#refreshQR").click(function () {
            fetchNewQR();
        });
        $("#manualCheckinBtn").click(function () {
            $("#manualCheckinForm").toggle();
        });
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("sidebarToggle");
        let sidebarOpen = false;
        toggleBtn.addEventListener("click", function () {
            sidebarOpen = !sidebarOpen;
            sidebar.style.left = sidebarOpen ? "0" : "-240px";
        }); 
        document.addEventListener("click", function (e) {
            if (sidebarOpen && !sidebar.contains(e.target) && e.target !== toggleBtn) {
                sidebar.style.left = "-240px";
            sidebarOpen = false;
            }
        });
     /*   if (
            localStorage["spp_qrs"] === undefined ||
            localStorage["spp_qrs"] === null ||
            !localStorage["spp_qrs"]    
        ) 
            window.location.href = "login.php?esChecador=1";*/
        initxe3("checador",true);
        setInterval(updateTime, 1000);
        updateTime();
    })

    function generateQR(data) {
        
        $("#qrCodeDisplay").empty();
        const canvas = document.createElement("canvas");
        $("#qrCodeDisplay").append(canvas);
        QRCode.toCanvas(
        canvas,
        data,
        {
            width: 200,
            margin: 2,
            color: {
            dark: "#000000",
            light: "#ffffff",
            },
        },
        function (error) {
            if (error) {
            $("#qrCodeDisplay").html(
                '<div class="alert alert-danger">Failed to generate QR</div>'
            );
            console.error("QR Error:", error);
            }
        }
        );

        // Start countdown (5 minutes)
    }


    function updateTime() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString("en-US", { hour12: false });
        const [hours, mins, secs] = timeStr.split(":");
        $("#current-time").html(`
            <span class="lcd-digit">${hours[0]}</span><span class="lcd-digit">${hours[1]}</span>
            <span class="lcd-separator">:</span>
            <span class="lcd-digit">${mins[0]}</span><span class="lcd-digit">${mins[1]}</span>
            <span class="lcd-separator">:</span>
            <span class="lcd-digit">${secs[0]}</span><span class="lcd-digit">${secs[1]}</span>
        `);
        if( minuto != mins) {
            minuto = mins;
            fetchNewQR();
        }
    }

    // Fetch new QR from server
    function fetchNewQR() {
        $.post(
        "index.php",
        {
            fetch_new: localStorage["spp_token"],
            qr:qr,
            estacion : localStorage["spp_estacion"],
        },
        function (data) {
            if (data) {
                generateQR(data);
                data = data.split("?token=")[1]; // Clean up the data if needed
                qr= data;
                $("#hQr").val(data);
            } else {
            $("#qrCodeDisplay").html(
                '<div class="alert alert-danger">no se pudo cargar un nuevo QR!!!</div>'
            );
            }
        }
        ).fail(function () {
            $("#qrCodeDisplay").html('<div class="alert alert-danger">Connection error</div>');
        });
    }

 function updateSoundIcon(val) {
                    if (val == 1) {
                        soundIcon.className = 'bi bi-volume-up-fill';
                    } else {
                        soundIcon.className = 'bi bi-volume-mute-fill';
                    }
                }