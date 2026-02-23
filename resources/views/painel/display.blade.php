<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { width: 100%; height: 100%; overflow: hidden; background: #000; }
        #panel-display { width: 100%; height: 100%; }
    </style>
    @vite(['resources/js/panel-display.jsx'])
</head>
<body>
    <div id="panel-display"
         data-hash="{{ $hash }}"
         data-token="{{ $token }}"
         data-key="{{ $pageKey }}"></div>
</body>
</html>
