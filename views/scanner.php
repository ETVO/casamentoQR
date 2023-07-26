<a href="?" class="btn btn-outline-primary mb-3 small">Voltar ao Início</a>
<div id="reader">
  <div id="spinner"><span class="bi-arrow-repeat"></span></div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
  function onScanSuccess(decodedText, decodedResult) {
    // handle the scanned code as you like, for example:
    console.log(`Code matched = ${decodedText}`, decodedResult);
    window.location.href = "?checkin=" + decodedText;
  }

  function onScanFailure(error) {
  }

  let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader", {
      fps: 25,
      qrbox: {
        width: 250,
        height: 250
      },
      formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
    },
    true);
  html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>