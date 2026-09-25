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
            $('#qrScannerModal').on('shown.bs.modal', this.startScanner.bind(this));
            $('#qrScannerModal').on('hidden.bs.modal',this.stopScanner.bind(this));
            $('#toggleCamera').click(this.switchCamera.bind(this));
            $('#stopScanner').click(() => $('#qrScannerModal').modal('hide'));
        },
        startScanner: async function() {
            $('#scanResult').html('Initializing scanner...').removeClass('text-success text-danger');
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: this.currentCamera,
                        width: { ideal: 960 },   // 1280 * 0.75 = 960
                        height: { ideal: 540 }   // 720 * 0.75 = 540
                    },
                    audio: false
                });
                const videoElement = document.getElementById('video');
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

            const videoElement = document.getElementById('video');
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
            if (qrFound){
                if(miTimeOut) clearTimeout(miTimeOut);
                miTimeOut=setTimeout(() => { qrFound = false; }, 5000);
                return;
            }
            if (!result.data) {
                $('#aError').html('No se detectó un QR válido.').removeClass('d-none');
                if(miTimeOut) clearTimeout(miTimeOut);
                miTimeOut=setTimeout(() => { qrFound = false; 
                    $("#aError").addClass("d-none").text("");
                }, 5000);
                return;
            }
            if (typeof result.data === "string") {
                 result = result.data; // Clean up the data if needed
            } else {
                $('#scanResult').html('no es un QR válido.').addClass('text-danger');
                return;
            }
  
         // alert("QR Escaneado: " + result);
            // If the scanned text contains the target token, trigger checkin()
            if (typeof result === 'string' && result.includes('5DCB38B62EF893AAB6D76AC24ECE3')) {
                $('#scanResult').html('Token detected — iniciando check-in...').removeClass('text-danger').addClass('text-success');
                // Call checkin function if available
                $("#checkinModal").modal("show");
                
                $('#qrScannerModal').modal('hide');
            }
            if (typeof result === 'string' && result.includes('83AA486A6CB6D73BC63AF1299D654')) {
                $('#scanResult').html('Token detected — iniciando check-in...').removeClass('text-danger').addClass('text-success');
                // Call checkin function if available
                $("#bookBorrowModal").modal("show");
                
                $('#qrScannerModal').modal('hide');
            }
            
            if (typeof result === 'string' && result.includes('1F3148DAC122FCBB439168969F5E3')) {
                $('#scanResult').html('Token detected — iniciando check-in...').removeClass('text-danger').addClass('text-success');
                // Call checkin function if available
                $("#cafeteriaModal").modal("show");                
                $('#qrScannerModal').modal('hide');
            }
            
            qrFound = true; // Set flag to prevent further scans
            console.log("QR Scanned:", result); 
            
            
             
        }
    };


 

$(document).ready(function() {
    
    // Request fullscreen on the first user click anywhere in the document
    

    (function(){
        function enterFullscreen() {
            // Consider device "mobile" if it supports touch/gesture/swipe input
            const hasTouchSupport = (
                'ontouchstart' in window ||
                (navigator.maxTouchPoints && navigator.maxTouchPoints > 0) ||
                (navigator.msMaxTouchPoints && navigator.msMaxTouchPoints > 0) ||
                (window.matchMedia && window.matchMedia('(pointer: coarse)').matches)
            );
            if (!hasTouchSupport) return;

            if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) return;
            const el = document.documentElement;
            const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
            if (req) {
                try { req.call(el); } catch (e) { /* ignore promise rejection or unsupported */ }
            }
        }   document.addEventListener('click', enterFullscreen, { once: true, capture: true });
    })();
    if(localStorage["cobaed_tkn"]==undefined){
        window.location.href = "login.html";
        return;
    }
    $("#studentName").text(localStorage["cobaed_nombre"]);
    $("#scanQrBtn").click(function() {
        $('#qrScannerModal').modal('show');
    });
    qrScanner.init();

     /* =========================================================
       FAB DRAG & SAVE POSITION
    ==========================================================*/
    const $fab = $(".fab-add");
    const LS_KEY = "fabAddPos";
    const DRAG_THRESHOLD = 10; // px

    // Restaurar posición guardada
    const saved = localStorage.getItem(LS_KEY);
    if (saved) {
        try {
            const pos = JSON.parse(saved);
            $fab.css({
                left: pos.left,
                top: pos.top,
                right: "auto",
                bottom: "auto",
            });
        } catch (e) {}
    }

    let dragging = false;
    let dragStarted = false;
    let startX = 0, startY = 0;
    let origX = 0, origY = 0;

    function pt(e) {
        return e.touches ? e.touches[0] : e;
    }

    // Inicio de drag / posible tap
    $fab.on("mousedown touchstart", function (e) {
        const p = pt(e);
        dragging = true;
        dragStarted = false; // puede ser sólo tap

        startX = p.clientX;
        startY = p.clientY;

        const rect = $fab[0].getBoundingClientRect();
        origX = rect.left;
        origY = rect.top;
        // NO preventDefault aquí: permitimos click
    });

    // Movimiento
    $(document).on("mousemove touchmove", function (e) {
        if (!dragging) return;
        const p = pt(e);

        const dx = p.clientX - startX;
        const dy = p.clientY - startY;

        // Threshold para decidir si es drag real
        if (!dragStarted) {
            if (Math.abs(dx) < DRAG_THRESHOLD && Math.abs(dy) < DRAG_THRESHOLD) {
                return; // sigue siendo posible un tap
            }
            dragStarted = true;
            $fab.addClass("dragging");
        }

        // Drag real
        e.preventDefault();

        const left = origX + dx;
        const top  = origY + dy;

        $fab.css({
            left: left + "px",
            top: top + "px",
            right: "auto",
            bottom: "auto",
        });
    });

    // Fin de drag / tap
    $(document).on("mouseup touchend touchcancel", function (e) {
        if (!dragging) return;
        dragging = false;

        // Si nunca se superó el threshold => fue tap, dejamos que el click normal ocurra
        if (!dragStarted) {
            $fab.removeClass("dragging");
            return;
        }

        // Drag real: guardamos posición
        $fab.removeClass("dragging");

        const rect = $fab[0].getBoundingClientRect();
        const pos = {
            left: rect.left + "px",
            top: rect.top + "px",
        };
        localStorage.setItem(LS_KEY, JSON.stringify(pos));

        if (e.type.startsWith("touch")) {
            e.preventDefault(); // evita tap/click fantasma después del drag
        }
    });
      

})