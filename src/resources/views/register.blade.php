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
                <label class="register__group">
                    <div class="register__text">お名前</div>
                    <input class="register__input" type="text" name="name" placeholder="例: 山田　太郎" value="{{ old('name') }}" />
                    @error('name')
                    <div class="register__validation-alert">
                        {{ $message }}
                    </div>
                    @enderror
                </label>

                <label class="register__group">
                    <div class="register__text">メールアドレス</div>
                    <input class="register__input" type="email" name="email" placeholder="例: test@example.com" value="{{ old('email') }}" />
                    @error('email')
                    <div class="register__validation-alert">
                        {{ $message }}
                    </div>
                    @enderror
                </label>

                <label class="register__group">
                    <div class="register__text">パスワード</div>
                    <input class="register__input" type="password" name="password" placeholder="例: coachtech1106" />
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