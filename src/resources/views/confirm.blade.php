<x-app-layout>
    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('css/confirm.css') }}" />
    </x-slot>
    <x-slot name="subtitle">Confirm</x-slot>

    <div class="confirm">
        <div class="confirm__layout">
            <div class="confirm__group">
                <div class="confirm__group-title">お名前</div>
                <div class="confirm__group-text">
                    {{ $last_name }}　{{ $first_name }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">性別</div>
                <div class="confirm__group-text">
                    {{ $gender_name }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">メールアドレス</div>
                <div class="confirm__group-text">
                    {{ $email }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">電話番号</div>
                <div class="confirm__group-text">
                    {{ $area_code }}{{ $city_code }}{{ $subscriber_code }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">住所</div>
                <div class="confirm__group-text">
                    {{ $address }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">建物名</div>
                <div class="confirm__group-text">
                    {{ $building }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">お問い合わせの種類</div>
                <div class="confirm__group-text">
                    {{ $category_content }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">お問い合わせ内容</div>
                <div class="confirm__group-text">
                    {!! nl2br(e($detail)) !!}
                </div>
            </div>
        </div>

        <form class="confirm__form" method="post">
            @csrf
            <input type="hidden" name="last_name" value="{{ $last_name }}" />
            <input type="hidden" name="first_name" value="{{ $first_name }}" />
            <input type="hidden" name="gender" value="{{ $gender }}" />
            <input type="hidden" name="email" value="{{ $email }}" />
            <input type="hidden" name="area_code" value="{{ $area_code }}" />
            <input type="hidden" name="city_code" value="{{ $city_code }}" />
            <input type="hidden" name="subscriber_code" value="{{ $subscriber_code }}" />
            <input type="hidden" name="address" value="{{ $address }}" />
            <input type="hidden" name="building" value="{{ $building }}" />
            <input type="hidden" name="category_id" value="{{ $category_id }}" />
            <textarea class="display-none" name="detail">{{ $detail }}</textarea>
            <div class="confirm__button-group">
                <div class="confirm__submit-button">
                    <button type="submit" formaction="/thanks">送信</button>
                </div>
                <div class="confirm__edit-button">
                    <button type="submit" formaction="/">修正</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<!-- <!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FashionablyLate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inika&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/confirm.css') }}" />
</head>

<body>
    <header>
        <div class="header">
            <h1 class="header__title">FashionablyLate</h1>
        </div>
    </header>

    <main>
        <div class="confirm">
            <h2 class="confirm__title">Confirm</h2>

            <div class="confirm__layout">
                <div class="confirm__group">
                    <div class="confirm__group-title">お名前</div>
                    <div class="confirm__group-text">
                        {{ $last_name }}　{{ $first_name }}
                    </div>
                </div>

                <div class="confirm__group">
                    <div class="confirm__group-title">性別</div>
                    <div class="confirm__group-text">
                        {{ $gender_name }}
                    </div>
                </div>

                <div class="confirm__group">
                    <div class="confirm__group-title">メールアドレス</div>
                    <div class="confirm__group-text">
                        {{ $email }}
                    </div>
                </div>

                <div class="confirm__group">
                    <div class="confirm__group-title">電話番号</div>
                    <div class="confirm__group-text">
                        {{ $area_code }}{{ $city_code }}{{ $subscriber_code }}
                    </div>
                </div>

                <div class="confirm__group">
                    <div class="confirm__group-title">住所</div>
                    <div class="confirm__group-text">
                        {{ $address }}
                    </div>
                </div>

                <div class="confirm__group">
                    <div class="confirm__group-title">建物名</div>
                    <div class="confirm__group-text">
                        {{ $building }}
                    </div>
                </div>

                <div class="confirm__group">
                    <div class="confirm__group-title">お問い合わせの種類</div>
                    <div class="confirm__group-text">
                        {{ $category_content }}
                    </div>
                </div>

                <div class="confirm__group">
                    <div class="confirm__group-title">お問い合わせ内容</div>
                    <div class="confirm__group-text">
                        {!! nl2br(e($detail)) !!}
                    </div>
                </div>
            </div>

            <form class="confirm__form" method="post">
                @csrf
                <input type="hidden" name="last_name" value="{{ $last_name }}" />
                <input type="hidden" name="first_name" value="{{ $first_name }}" />
                <input type="hidden" name="gender" value="{{ $gender }}" />
                <input type="hidden" name="email" value="{{ $email }}" />
                <input type="hidden" name="area_code" value="{{ $area_code }}" />
                <input type="hidden" name="city_code" value="{{ $city_code }}" />
                <input type="hidden" name="subscriber_code" value="{{ $subscriber_code }}" />
                <input type="hidden" name="address" value="{{ $address }}" />
                <input type="hidden" name="building" value="{{ $building }}" />
                <input type="hidden" name="category_id" value="{{ $category_id }}" />
                <textarea class="display-none" name="detail">{{ $detail }}</textarea>
                <div class="confirm__button-group">
                    <div class="confirm__submit-button">
                        <button type="submit" formaction="/thanks">送信</button>
                    </div>
                    <div class="confirm__edit-button">
                        <button type="submit" formaction="/">修正</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>

</html> -->