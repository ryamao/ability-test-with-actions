<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FashionablyLate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inika&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/register.css') }}" />
</head>

<body>
    <header>
        <div class="header">
            <h1 class="header__title">FashionablyLate</h1>
            <nav class="header__nav">
                <div class="header__login">
                    <a class="header__login-link" href="/login">login</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <div class="register">
            <h2 class="register__title">Register</h2>

            <form class="register__form" action="/register" method="post">
                @csrf
                <div class="register__form-inner">
                    <label class="register__label">
                        <div class="register__label-text">お名前</div>
                        <div class="register__input">
                            <input type="text" name="name" placeholder="例: 山田　太郎" />
                        </div>
                    </label>

                    <label class="register__label">
                        <div class="register__label-text">メールアドレス</div>
                        <div class="register__input">
                            <input type="email" name="email" placeholder="例: test@example.com" />
                        </div>
                    </label>

                    <label class="register__label">
                        <div class="register__label-text">パスワード</div>
                        <div class="register__input">
                            <input type="password" name="password" placeholder="例: coachtech1106" />
                        </div>
                    </label>

                    <div class="register__button">
                        <button type="submit">登録</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>

</html>