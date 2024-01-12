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
                    {{ $easternOrderedName }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">性別</div>
                <div class="confirm__group-text">
                    {{ $genderName }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">メールアドレス</div>
                <div class="confirm__group-text">
                    {{ request('email') }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">電話番号</div>
                <div class="confirm__group-text">
                    {{ $phoneNumber }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">住所</div>
                <div class="confirm__group-text">
                    {{ request('address') }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">建物名</div>
                <div class="confirm__group-text">
                    {{ request('building') }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">お問い合わせの種類</div>
                <div class="confirm__group-text">
                    {{ $categoryContent }}
                </div>
            </div>
            <div class="confirm__group">
                <div class="confirm__group-title">お問い合わせ内容</div>
                <div class="confirm__group-text">
                    {!! nl2br(e(request('detail'))) !!}
                </div>
            </div>
        </div>

        <form class="confirm__form" method="post">
            @csrf
            <input type="hidden" name="last_name" value="{{ request('last_name') }}" />
            <input type="hidden" name="first_name" value="{{ request('first_name') }}" />
            <input type="hidden" name="gender" value="{{ request('gender') }}" />
            <input type="hidden" name="email" value="{{ request('email') }}" />
            <input type="hidden" name="area_code" value="{{ request('area_code') }}" />
            <input type="hidden" name="city_code" value="{{ request('city_code') }}" />
            <input type="hidden" name="subscriber_code" value="{{ request('subscriber_code') }}" />
            <input type="hidden" name="address" value="{{ request('address') }}" />
            <input type="hidden" name="building" value="{{ request('building') }}" />
            <input type="hidden" name="category_id" value="{{ request('category_id') }}" />
            <textarea class="display-none" name="detail">{{ request('detail') }}</textarea>
            <div class="confirm__button-group">
                <div class="confirm__submit-button">
                    <button type="submit" formaction="/contact">送信</button>
                </div>
                <div class="confirm__edit-button">
                    <button type="submit" formaction="/">修正</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>