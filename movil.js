 var qrFound=0;
 var registro={}
 miTimeOut=null;

    const qrScanner = {
        scanner: null,
        currentCamera: 'environment', // Default to rear camera
        devices: [],
        init: function() {
            this.bindEvents();
        },
        bindEvents: function() {
            $('#qr-tab').on('shown.bs.tab', this.startScanner.bind(this));
            $('#qr-tab').on('hidden.bs.tab', this.stopScanner.bind(this));
            $('#toggleCamera').click(this.switchCamera.bind(this));
            $('#stopScanner').click(() => $('#qr-tab').tab('hide'));      },
        startScanner: async function() {
            $('#scanResult').html('Initializing scanner...').removeClass('text-success text-danger');
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: this.currentCamera,
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                });
                const videoElement = document.getElementById('qrScanner');
                videoElement.srcObject = stream;
                videoElement.play();

                // Initialize QR scanner
                this.scanner = new QrScanner(
                    videoElement,
                    result => this.handleScan(result),
                    {
                        preferredCamera: this.currentCamera,
                        highlightScanRegion: true,
                        highlightCodeOutline: true,
                        maxScansPerSecond: 5
                    }
                );

                await this.scanner.start();
                $('#scanResult').html('Scanning...');

                // List available cameras
                this.devices = await QrScanner.listCameras();
                if (this.devices.length < 2) {
                    $('#toggleCamera').hide();
                }
            } catch (err) {
                console.error('Scanner error:', err);
                $('#scanResult').html('Error: ' + err.message).addClass('text-danger');
            }
        },
        stopScanner: function() {
            if (this.scanner) {
                this.scanner.stop();
                this.scanner.destroy();
                this.scanner = null;
            }

            const videoElement = document.getElementById('qrScanner');
            if (videoElement.srcObject) {
                videoElement.srcObject.getTracks().forEach(track => track.stop());
                videoElement.srcObject = null;
            }

            $('#scanResult').empty();
        },
        switchCamera: function() {
            if (this.devices.length < 2) return;

            this.currentCamera = this.currentCamera === 'environment' ? 'user' : 'environment';
            this.stopScanner();
            this.startScanner();
        },
        handleScan: function(result) {
            if (window.navigator.vibrate) {
                    window.navigator.vibrate([100, 50, 100]);
                } else if (window.navigator.userAgent.includes('iPhone') || window.navigator.userAgent.includes('iPad')) {
                    // iOS does not support vibration API, consider using a sound or visual cue if needed
                }
            if (qrFound){
                if(miTimeOut) clearTimeout(miTimeOut);
                miTimeOut=setTimeout(() => { qrFound = false; }, 5000);
                return;
            }
               
 
            if (!result.data) {
                $('#scanResult').html('No se encontro un QR Válido.').removeClass('text-success text-danger');
                return;
            }
            if (typeof result.data === "string") {
                 result = result.data; // Clean up the data if needed
            } else {
                $('#scanResult').html('no es un QR válido.').addClass('text-danger');
                return;
            }
            qrFound = true; // Set flag to prevent further scans
            console.log("QR Scanned:", result);
            setTimeout(() => {
                qrFound = false; // Reset flag after processing
            }, 5000); // Reset after 5 seconds
            $('#qrScannerModal').modal('hide');

            $('#scanResult').html(`Scanned: <strong>${result}</strong>`).addClass('text-success');
            $.post("movil.php", { "passQr": result, tkn:localStorage.getItem("cobaed_token") }, (response) => {
                
                if(!response.ok) 
                    $("#userComments").val(response.msg);
                else
                    historial();
                $("#teacherName").text(response.maestro || "No registrado");
                $("#classArea").text(response.area || "No registrado");
                $("#startTime").text(response.checkin || "--:--");
                $("#endTime").text(response.checkout || "--:--");
                registro["area_id"]=response.area_id || 0;
                registro["maestro"]=response.maestro_id || 0;
                registro["inicio"]=response.checkin || "00:00";
                registro["fin"]=response.checkout || "00:00";
                registro["comments"]=$("#userComments").val();
                disabled=response.ok?"":"disabled";
                $("#actionButtons button").prop(disabled);
                 
                 
            });
        }
    };

    $(document).ready(function() {
        $("#iLogo").on("click", function() {
            location.reload(true);
        });
        registro['tkn']=localStorage.getItem("cobaed_token");
        registro['action']=0;
        registro["area_id"]=0;
        registro["maestro"]=0;
        registro["inicio"]="00:00";
        registro["fin"]="00:00";
        registro["checkin"]="00:00";   
        registro["comments"]="";
        $("#actionButtons button").on("click", function(e){
            
            actionButtonClick($(e.currentTarget).data('value'));
        });
        // Enter fullscreen on first document click for mobile devices
        if (/Mobi|Android/i.test(navigator.userAgent)) {
            let fullscreenRequested = false;
            $(document).one('click', function() {
            if (!fullscreenRequested) {
                const elem = document.documentElement;
                if (elem.requestFullscreen) {
                elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) { // Safari
                elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) { // IE11
                elem.msRequestFullscreen();
                }
                fullscreenRequested = true;
            }
            });
        }
        qrScanner.init();
        let qrFound = false; // Flag to prevent multiple scans
        // Change footer color
        $(".footer-nav").css("background-color", "#ff964bff");

        $('.btn-footer').on('click', function() {
            if ($("#login-tab").hasClass("active")) return;
            const tabId = $(this).data('tab');
            const tabEl = document.querySelector(`#${tabId}-tab`);
            if (tabEl) 
            bootstrap.Tab.getOrCreateInstance(tabEl).show();
        });
        $("#btLogin").on("click", function() {
            const username = $("#username").val();
            const password = $("#password").val();
            $.post("movil.php", { u:username,p: password }, function(data) {
                if (data.ok) {
                    localStorage.setItem("cobaed_token", data.token);
                    const tab = new bootstrap.Tab(document.querySelector('#qr-tab'));
                    tab.show();
                    historial();
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
            else
                historial();
        
    });

   

    function ok(qrCodeMessage) {
        alert(qrCodeMessage);
    }

    function actionButtonClick(valor) {
         registro["action"]=valor;
         registro["comments"]=$("#userComments").val();
         registro['tkn']=localStorage.getItem("cobaed_token");
         $.post("movil.php", registro, function(data) {
                $("#userComments").val("");
                $("#teacherName").text(data.maestro || "No registrado");
                $("#classArea").text(data.area || "No registrado");
                $("#startTime").text(data.checkin || "--:--");
                $("#endTime").text(data.checkout || "--:--");
                if (window.navigator.vibrate) {
                    window.navigator.vibrate([100, 50, 100]);
                } else if (window.navigator.userAgent.includes('iPhone') || window.navigator.userAgent.includes('iPad')) {
                    // iOS does not support vibration API, consider using a sound or visual cue if needed
                }
                historial();
             
        }).fail(function(error) {
            console.log(error);
        });
    }
    function historial(){
        $.post("movil.php", { historial: localStorage.getItem("cobaed_token") }, function(data) {
            $("#historyContent").html(data);
        }).fail(function(error) {
            console.log(error);
        });
    }
 