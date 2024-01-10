<x-app-layout>
    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('css/register.css') }}" />
    </x-slot>

    <x-slot name="headerRight">
        <nav>
            <div class="header__nav">
                <a class="header__nav-link" href="/login">login</a>
            </div>
        </nav>
    </x-slot>

    <x-slot name="subtitle">Register</x-slot>

    <div class="register">
        <form class="register__form" action="/register" method="post" novalidate>
            @csrf
            <div class="register__layout">
                <label class="register__input-unit">
                    <div class="register__label-text">お名前</div>
                    <div class="register__input">
                        <input type="text" name="name" placeholder="例: 山田　太郎" />
                    </div>
                    @error('name')
                    <div class="register__validation-alert">
                        {{ $message }}
                    </div>
                    @enderror
                </label>

                <label class="register__input-unit">
                    <div class="register__label-text">メールアドレス</div>
                    <div class="register__input">
                        <input type="email" name="email" placeholder="例: test@example.com" />
                    </div>
                    @error('email')
                    <div class="register__validation-alert">
                        {{ $message }}
                    </div>
                    @enderror
                </label>

                <label class="register__input-unit">
                    <div class="register__label-text">パスワード</div>
                    <div class="register__input">
                        <input type="password" name="password" placeholder="例: coachtech1106" />
                    </div>
                    @error('password')
                    <div class="register__validation-alert">
                        {{ $message }}
                    </div>
                    @enderror
                </label>
            </div>

            <div class="register__button">
                <button type="submit">登録</button>
            </div>
        </form>
    </div>
</x-app-layout>