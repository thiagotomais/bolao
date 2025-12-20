<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Bolão Mega da Virada {{ env('APP_ANO') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        h1, h2 {
            margin-top: 0;
        }

        .muted {
            color: #666;
        }

        .pix-box {
            background: #f9f9f9;
            border: 1px dashed #ccc;
            padding: 15px;
            border-radius: 6px;
            word-break: break-all;
            font-family: monospace;
            margin-top: 10px;
        }

        button {
            background: #1976d2;
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #125aa3;
        }

        .rules li {
            margin-bottom: 8px;
        }

        img {
            max-width: 100%;
        }
    </style>

    <script>
        function copiarPix() {
            const pix = document.getElementById('pix-copia-cola').innerText;
            navigator.clipboard.writeText(pix).then(() => {
                alert('Chave PIX copiada!');
            });
        }
		
		function copiarChavePix() {
            const pix = document.getElementById('pix').innerText;
            navigator.clipboard.writeText(pix).then(() => {
                alert('Chave PIX copiada!');
            });
        }
    </script>
</head>
<body>

<div class="container">

    <!-- TÍTULO -->
    <div class="card">
        <h1>🎉 Bolão Mega da Virada {{ env('APP_ANO') }}</h1>
        <p class="muted">
            Participe do bolão e concorra ao maior prêmio do ano!
        </p>
    </div>

    <!-- COMO PARTICIPAR -->
    <div class="card">
        <h2>📌 Como participar</h2>

        <ul class="rules">
            <li>A participação é feita via <strong>PIX</strong>;</li>
			<li><span style="color:#FF0000">Para receber o link de acompanhamento, <strong>COLOQUE SEU NÚMERO DO WhatsApp na mensagem do PIX</strong>;</li>
			<li>Não é necessário o envio do <strong>comprovante do PIX via WhatsApp</strong>;</li>
            <li>O valor da participação é em <strong>múltiplos de R$ 50,00</strong> (50, 100, 150, 200, etc);</li>
            <li>A participação será registrada no <strong>nome do titular do PIX</strong> e no <strong>número de celular (com DDD)</strong>;</li>
            <li>As participações serão aceitas até <strong>30/12/2025 às 23:59:59</strong>.</li>
        </ul>
    </div>

    <!-- PIX -->
    <div class="card">
        <h2>💰 Pagamento via PIX</h2>

        <p><strong>Chave PIX:</strong></p>

        <div class="pix-box" id="pix">pix@tomais.com.br</div>
	    <button onclick="copiarChavePix()">Copiar chave PIX</button>
		
       <!-- <p><strong>Pix "Copia e cola":</strong></p>

        <div class="pix-box" id="pix-copia-cola">
            00020126930014br.gov.bcb.pix0117pix@tomais.com.br0250Apenas multiplos de 50 Informe seu numero Whatsapp5204000053039865802BR5925THIAGO GABRIEL DA SILVA V6006SANTOS62470509BOLAO202550300017br.gov.bcb.brcode01051.0.0630449E2
        </div>

        <button onclick="copiarPix()">Copiar PIX copia e cola</button> -->

        <p class="muted" style="margin-top:12px">
            Após o envio do PIX, sua participação será registrada automaticamente.
        </p>
    </div>

    <!-- QR CODE 
    <div class="card">
        <h2>📷 Ou pague pelo QR Code</h2>

        <p class="muted">Aponte a câmera do celular para o QR Code abaixo:</p>

        <img src="{{ asset('images/qrcode.png') }}" alt="QR Code PIX">
    </div>
   -->
</div>

</body>
</html>
