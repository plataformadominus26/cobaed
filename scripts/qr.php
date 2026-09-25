<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>COBAED - QR Aula</title>
  <style>
    body {
      font-family: "Helvetica Neue", Arial, sans-serif;
      background: #f8f9fa;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .card {
      background: #fff;
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
      text-align: center;
      max-width: 320px;
      width: 100%;
    }
    .room-name {
      font-size: 1.8rem;
      font-weight: bold;
      margin: 1rem 0 0.5rem;
      color: #333;
    }
    .school-name {
      font-size: 1rem;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: #666;
      margin-bottom: 1rem;
    }
    #qrcode {
      margin: 0 auto;
    }
    .accent-bar {
      height: 6px;
      width: 60%;
      background: linear-gradient(90deg, #2e86de, #00b894);
      border-radius: 3px;
      margin: 1.5rem auto 0;
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="school-name">COBAED</div>
    <div id="qrcode"></div>
    <div class="room-name">Aula 12</div>
    <div class="accent-bar"></div>
  </div>

  <!-- QRCode.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    // Replace with dynamic value (URL, attendance endpoint, etc.)
    const qrData = "https://cobaed.edu.mx/aula/12";
    new QRCode(document.getElementById("qrcode"), {
      text: qrData,
      width: 200,
      height: 200,
      colorDark : "#000",
      colorLight : "#fff",
      correctLevel : QRCode.CorrectLevel.H
    });
  </script>
</body>
</html>
