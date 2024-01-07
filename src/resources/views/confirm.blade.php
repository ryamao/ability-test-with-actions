<!DOCTYPE html>
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

            <table class="confirm__table">
                <tr class="confirm__row">
                    <th class="confirm__row-header">お名前</th>
                    <td class="confirm__row-data">
                        <p class="confirm__text">山田　太郎</p>
                    </td>
                </tr>

                <tr class="confirm__row">
                    <th class="confirm__row-header">性別</th>
                    <td class="confirm__row-data">
                        <p class="confirm__text">男性</p>
                    </td>
                </tr>

                <tr class="confirm__row">
                    <th class="confirm__row-header">メールアドレス</th>
                    <td class="confirm__row-data">
                        <p class="confirm__text">test@example.com</p>
                    </td>
                </tr>

                <tr class="confirm__row">
                    <th class="confirm__row-header">電話番号</th>
                    <td class="confirm__row-data">
                        <p class="confirm__text">08012345678</p>
                    </td>
                </tr>

                <tr class="confirm__row">
                    <th class="confirm__row-header">住所</th>
                    <td class="confirm__row-data">
                        <p class="confirm__text">東京都渋谷区千駄ヶ谷1-2-3</p>
                    </td>
                </tr>

                <tr class="confirm__row">
                    <th class="confirm__row-header">建物名</th>
                    <td class="confirm__row-data">
                        <p class="confirm__text">千駄ヶ谷マンション101</p>
                    </td>
                </tr>

                <tr class="confirm__row">
                    <th class="confirm__row-header">お問い合わせの種類</th>
                    <td class="confirm__row-data">
                        <p class="confirm__text">商品の交換について</p>
                    </td>
                </tr>

                <tr class="confirm__row confirm__detail-row">
                    <th class="confirm__row-header">お問い合わせ内容</th>
                    <td class="confirm__row-data">
                        <p class="confirm__text">届いた商品が注文した商品ではありませんでした。<br />商品の取り替えをお願いします。</p>
                    </td>
                </tr>
            </table>

            <form class="confirm__form" method="post">
                @csrf
                <input type="hidden" name="last_name" value="山田" />
                <input type="hidden" name="first_name" value="太郎" />
                <input type="hidden" name="gender" value="1" />
                <input type="hidden" name="email" value="test@example.com" />
                <input type="hidden" name="area_code" value="080" />
                <input type="hidden" name="city_code" value="1234" />
                <input type="hidden" name="subscriber_code" value="5678" />
                <input type="hidden" name="address" value="東京都渋谷区千駄ヶ谷1-2-3" />
                <input type="hidden" name="building" value="千駄ヶ谷マンション101" />
                <input type="hidden" name="gender" value="2" />
                <textarea class="display-none" name="detail">届いた商品が注文した商品ではありませんでした。
商品の取り替えをお願いします。</textarea>
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

</html>