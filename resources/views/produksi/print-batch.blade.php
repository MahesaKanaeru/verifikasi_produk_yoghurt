//print-bacth.blade.php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: 215mm 330mm;
            margin: 5mm;
        }
        body {
            margin: 0;
            padding: 0;
        }
        .label-wrapper {
            width: 100%;
            text-align: center;
            margin-bottom: 2mm;
        }
        .label-wrapper img {
            width: 160mm;
            height: auto;
            border: 0.5px dashed #ccc;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @php
        $perPage = 2;
        $pages   = (int) ceil($qty / $perPage);
    @endphp

    @for ($p = 0; $p < $pages; $p++)
        @for ($i = 0; $i < $perPage; $i++)
            @php $current = $p * $perPage + $i; @endphp
            @if ($current < $qty)
                <div class="label-wrapper">
                    <img src="{{ $imageSrc }}" alt="Label">
                </div>
            @endif
        @endfor

        @if ($p < $pages - 1)
            <div class="page-break"></div>
        @endif
    @endfor
</body>
</html>