<x-app-layout>
    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('css/contact.css') }}" />
    </x-slot>
    <x-slot name="subtitle">Contact</x-slot>

    <div class="contact">
        <form class="contact__form" action="/confirm" method="post" novalidate>
            @csrf
            <div class="contact__layout">
                <div class="contact__group">
                    <p class="contact__label contact__label--required">お名前</p>
                    <div class="contact__input-unit contact__name-unit">
                        <div class="contact__input">
                            <input class="contact__last-name" type="text" name="last_name" placeholder="例: 山田" value="{{ old('last_name', $last_name ?? '') }}" />
                            @error('last_name')
                            <div class="contact__validation-alert">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="contact__input">
                            <input class="contact__first-name" type="text" name="first_name" placeholder="例: 太郎" value="{{ old('first_name', $first_name ?? '') }}" />
                            @error('first_name')
                            <div class="contact__validation-alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="contact__group contact__gender-group">
                    <p class="contact__label contact__label--required">性別</p>
                    <div class="contact__input-unit contact__gender-unit">
                        <div class="contact__input">
                            <div class="contact__gender-radio">
                                <label class="contact__gender-item">
                                    <input class="contact__gender" type="radio" name="gender" value="1" @if(old('gender', $gender ?? '1' )==='1' ) checked @endif />
                                    男性
                                </label>
                                <label class="contact__gender-item">
                                    <input class="contact__gender" type="radio" name="gender" value="2" @if(old('gender', $gender ?? '' )==='2' ) checked @endif />
                                    女性
                                </label>
                                <label class="contact__gender-item">
                                    <input class="contact__gender" type="radio" name="gender" value="3" @if(old('gender', $gender ?? '' )==='3' ) checked @endif />
                                    その他
                                </label>
                            </div>
                            @error('gender')
                            <div class="contact__validation-alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="contact__group">
                    <p class="contact__label contact__label--required">メールアドレス</p>
                    <div class="contact__input-unit">
                        <div class="contact__input">
                            <input class="contact__email" type="email" name="email" placeholder="例: test@example.com" value="{{ old('email', $email ?? '') }}" />
                            @error('email')
                            <div class="contact__validation-alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="contact__group">
                    <p class="contact__label contact__label--required">電話番号</p>
                    <div class="contact__input-unit contact__tel-unit">
                        <div class="contact__input">
                            <input class="contact__tel" type="tel" name="area_code" placeholder="080" value="{{ old('area_code', $area_code ?? '') }}" />
                            @error('area_code')
                            <div class="contact__validation-alert">{{ $message }}</div>
                            @enderror
                        </div>
                        <span class="contact__tel-hyphen">-</span>
                        <div class="contact__input">
                            <input class="contact__tel" type="tel" name="city_code" placeholder="1234" value="{{ old('city_code', $city_code ?? '') }}" />
                            @error('city_code')
                            <div class="contact__validation-alert">{{ $message }}</div>
                            @enderror
                        </div>
                        <span class="contact__tel-hyphen">-</span>
                        <div class="contact__input">
                            <input class="contact__tel" type="tel" name="subscriber_code" placeholder="5678" value="{{ old('subscriber_code', $subscriber_code ?? '') }}" />
                            @error('subscriber_code')
                            <div class="contact__validation-alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="contact__group">
                    <p class="contact__label contact__label--required">住所</p>
                    <div class="contact__input-unit">
                        <div class="contact__input">
                            <input class="contact__address" type="text" name="address" placeholder="例: 東京都渋谷区千駄ヶ谷1-2-3" value="{{ old('address', $address ?? '') }}" />
                            @error('address')
                            <div class="contact__validation-alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="contact__group">
                    <p class="contact__label">建物名</p>
                    <div class="contact__input-unit">
                        <div class="contact__input">
                            <input class="contact__building" type="text" name="building" placeholder="例: 千駄ヶ谷マンション101" value="{{ old('building', $building ?? '') }}" />
                        </div>
                    </div>
                </div>

                <div class="contact__group">
                    <p class="contact__label contact__label--required">お問い合わせの種類</p>
                    <div class="contact__input-unit">
                        <div class="contact__input">
                            <div class="contact__category-select">
                                <select class="contact__category" name="category_id">
                                    <option value="">選択してください</option>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @if(old('category_id', $category_id ?? '' )==="{$category->id}" ) selected @endif>{{ $category->content }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                            <div class="contact__validation-alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="contact__group">
                    <p class="contact__label contact__label--required">お問い合わせ内容</p>
                    <div class="contact__input-unit contact__detail-unit">
                        <div class="contact__input">
                            <textarea class="contact__detail" name="detail" placeholder="お問い合わせ内容をご記載ください">{{ old('detail', $detail ?? '') }}</textarea>
                            @error('detail')
                            <div class="contact__validation-alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact__button-layout">
                <button class="contact__submit-button" type="submit">確認画面</button>
            </div>
        </form>
    </div>
</x-app-layout>