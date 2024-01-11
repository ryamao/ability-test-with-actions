<x-app-layout>
    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
    </x-slot>

    <x-slot name="headerRight">
        <nav>
            <div class="header__nav">
                <a class="header__nav-link" href="/register">register</a>
            </div>
        </nav>
    </x-slot>

    <x-slot name="subtitle">Login</x-slot>

    <div class="login">
        <form class="login__form" action="/login" method="post" novalidate>
            @csrf
            <div class="login__layout">
                <label class="login__group">
                    <div class="login__text">メールアドレス</div>
                    <input class="login__input" type="email" name="email" placeholder="例: test@example.com" value="{{ old('email') }}" />
                    @error('email')
                    <div class="login__validation-alert">
                        {{ $message }}
                    </div>
                    @enderror
                </label>

                <label class="login__group">
                    <div class="login__text">パスワード</div>
                    <input class="login__input" type="password" name="password" placeholder="例: coachtech1106" />
                    @error('password')
                    <div class="login__validation-alert">
                        {{ $message }}
                    </div>
                    @enderror
                </label>
            </div>

            <div class="login__button-layout">
                <button class="login__submit-button" type="submit">ログイン</button>
            </div>
        </form>
    </div>
</x-app-layout>

<!-- <!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FashionablyLate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inika&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
</head>

<body>
    <header>
        <div class="header">
            <h1 class="header__title">FashionablyLate</h1>
            <nav class="header__nav">
                <div class="header__login">
                    <a class="header__login-link" href="/register">register</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="login">
            <h2 class="login__title">Login</h2>

            <form class="login__form" action="/login" method="post" novalidate>
                @csrf
                <div class="login__form-inner">
                    <label class="login__label">
                        <div class="login__label-text">メールアドレス</div>
                        <div class="login__input">
                            <input type="email" name="email" placeholder="例: test@example.com" />
                        </div>
                        @error('email')
                        <div class="login__validation-alert">
                            {{ $message }}
                        </div>
                        @enderror
                    </label>

                    <label class="login__label">
                        <div class="login__label-text">パスワード</div>
                        <div class="login__input">
                            <input type="password" name="password" placeholder="例: coachtech1106" />
                        </div>
                        @error('password')
                        <div class="login__validation-alert">
                            {{ $message }}
                        </div>
                        @enderror
                    </label>

                    <div class="login__button">
                        <button type="submit">ログイン</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>

</html> -->