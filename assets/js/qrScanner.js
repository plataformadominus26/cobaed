    var qrFound = 0;
    const qrScanner = {
        scanner: null,
        currentCamera: 'environment', // Default to rear camera
        devices: [],
        init: function () {
            this.bindEvents();
        },
        bindEvents: function () {
            $('#qrScannerModal').on('shown.bs.modal', this.startScanner.bind(this));
            $('#qrScannerModal').on('hidden.bs.modal', this.stopScanner.bind(this));
            $('#toggleCamera').click(this.switchCamera.bind(this));
            $('#stopScanner').click(() => $('#qrScannerModal').modal('hide'));
        },
        startScanner: async function () {
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
        stopScanner: function () {
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

        switchCamera: function () {
            if (this.devices.length < 2) return;

            this.currentCamera = this.currentCamera === 'environment' ? 'user' : 'environment';
            this.stopScanner();
            this.startScanner();
        },

        handleScan: function (result) {
            if (qrFound) return; // Prevent multiple scans
            if (!result.data) {
                $('#scanResult').html('No se encontro un QR Válido.').removeClass('text-success text-danger');
                return;
            }
            if (typeof result.data === "string" && result.data.includes("?token=")) {
                result = result.data.split("?token=")[1]; // Clean up the data if needed
            } else {
                $('#scanResult').html('no es un QR válido.')
                    .addClass('text-danger');
                return;
            }
            qrFound = true; // Set flag to prevent further scans
            console.log("QR Scanned:", result);
            setTimeout(() => {
                qrFound = false; // Reset flag after processing
            }
                , 5000); // Reset after 5 seconds
            $('#qrScannerModal').modal('hide');

            $('#scanResult').html(`Scanned: <strong>${result}</strong>`).addClass('text-success');
            $.post("checador.php", { "checar": localStorage["spp_token"], "qr": result }, (response) => {
                sendMessage(response, "checador");
                setTimeout(() => {
                    this.stopScanner();
                    $('#qrScannerModal').modal('hide');
                }, 1000);
            })
        }

    }