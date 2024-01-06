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
    <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
</head>

<body>
    <header>
        <div class="header">
            <h1 class="header__title">FashionablyLate</h1>
        </div>
    </header>

    <main>
        <div class="contact">
            <h2 class="contact__title">Contact</h2>

            <form class="form" action="/confirm" method="post" novalidate>
                @csrf
                <div class="form__inner">
                    <div class="form__group">
                        <p class="form__text form__text--required">お名前</p>
                        <div class="form__input-unit form__name-unit">
                            <div class="form__input">
                                <input class="form__last-name" type="text" name="last_name" placeholder="例: 山田" value="{{ old('last_name') }}" />
                                @error('last_name')
                                <div class="form__validation-alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form__input">
                                <input class="form__first-name" type="text" name="first_name" placeholder="例: 太郎" value="{{ old('first_name') }}" />
                                @error('first_name')
                                <div class="form__validation-alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form__group form__gender-group">
                        <p class="form__text form__text--required">性別</p>
                        <div class="form__input-unit form__gender-unit">
                            <div class="form__input">
                                <div class="form__gender-list">
                                    <label class="form__gender-item">
                                        <input class="form__gender" type="radio" name="gender" value="1" @if(old('gender', 1)==1) checked @endif />
                                        男性
                                    </label>
                                    <label class="form__gender-item">
                                        <input class="form__gender" type="radio" name="gender" value="2" @if(old('gender')==2) checked @endif />
                                        女性
                                    </label>
                                    <label class="form__gender-item">
                                        <input class="form__gender" type="radio" name="gender" value="3" @if(old('gender')==3) checked @endif />
                                        その他
                                    </label>
                                </div>
                                @error('gender')
                                <div class="form__validation-alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form__group">
                        <p class="form__text form__text--required">メールアドレス</p>
                        <div class="form__input-unit">
                            <div class="form__input">
                                <input class="form__email" type="email" name="email" placeholder="例: test@example.com" value="{{ old('email') }}" />
                                @error('email')
                                <div class="form__validation-alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form__group">
                        <p class="form__text form__text--required">電話番号</p>
                        <div class="form__input-unit form__tel-unit">
                            <div class="form__input">
                                <input class="form__tel" type="tel" name="area_code" placeholder="080" value="{{ old('area_code') }}" />
                                @error('area_code')
                                <div class="form__validation-alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <span class="form__tel-hyphen">-</span>
                            <div class="form__input">
                                <input class="form__tel" type="tel" name="city_code" placeholder="1234" value="{{ old('city_code') }}" />
                                @error('city_code')
                                <div class="form__validation-alert">{{ $message }}</div>
                                @enderror
                            </div>
                            <span class="form__tel-hyphen">-</span>
                            <div class="form__input">
                                <input class="form__tel" type="tel" name="subscriber_code" placeholder="5678" value="{{ old('subscriber_code') }}" />
                                @error('subscriber_code')
                                <div class="form__validation-alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form__group">
                        <p class="form__text form__text--required">住所</p>
                        <div class="form__input-unit">
                            <div class="form__input">
                                <input class="form__address" type="text" name="address" placeholder="例: 東京都渋谷区千駄ヶ谷1-2-3" value="{{ old('address') }}" />
                                @error('address')
                                <div class="form__validation-alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form__group">
                        <p class="form__text">建物名</p>
                        <div class="form__input-unit">
                            <div class="form__input">
                                <input class="form__building" type="text" name="building" placeholder="例: 千駄ヶ谷マンション101" value="{{ old('building') }}" />
                            </div>
                        </div>
                    </div>

                    <div class="form__group">
                        <p class="form__text form__text--required">お問い合わせの種類</p>
                        <div class="form__input-unit">
                            <div class="form__input">
                                <select class="form__category" name="category_id">
                                    <option value="">選択してください</option>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @if(old('category_id')==$category->id) selected @endif>{{ $category->content }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="form__validation-alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form__group">
                        <p class="form__text form__text--required">お問い合わせ内容</p>
                        <div class="form__input-unit form__detail-unit">
                            <div class="form__input">
                                <textarea class="form__detail" name="detail" placeholder="お問い合わせ内容をご記載ください">{{ old('detail') }}</textarea>
                                @error('detail')
                                <div class="form__validation-alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form__button">
                    <button type="submit">確認画面</button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>