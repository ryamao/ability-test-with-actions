@props(['hasHeader' => true])

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>FashionablyLate</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inika&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app-layout.css') }}" />

    @isset($styles)
    {{ $styles }}
    @endisset
</head>

<body>
    @if ($hasHeader)
    <header>
        <div class="header">
            <h1 class="header__title">FashionablyLate</h1>
        </div>
    </header>
    @endif

    <main>
        <div class="content">
            @isset($subtitle)
            <h2 class="content__title">{{ $subtitle }}</h2>
            @endisset

            {{ $slot }}
        </div>
    </main>
</body>

</html>