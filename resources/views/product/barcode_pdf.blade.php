<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Barcode - {{ $product->name }}</title>
    <style>
        @page { margin: 0; }
        html, body {
            margin: 0;
            padding: 0;
            width: {{ $pageWidthMm }}mm;
            height: {{ $pageHeightMm }}mm;
        }
        .page {
            width: {{ $pageWidthMm }}mm;
            height: {{ $pageHeightMm }}mm;
        }
        .page img {
            width: {{ $pageWidthMm }}mm;
            height: {{ $pageHeightMm }}mm;
            display: block;
            border: 0;
        }
    </style>
</head>
<body>
    @foreach($rowImages ?? [] as $rowImage)
    <div class="page" @if(!$loop->last) style="page-break-after: always;" @endif>
        <img src="{{ $rowImage }}" alt="barcode-row">
    </div>
    @endforeach
</body>
</html>
