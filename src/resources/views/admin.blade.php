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
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
</head>

<body>
    <header>
        <div class="header">
            <h1 class="header__title">FashionablyLate</h1>

            <nav class="header__nav">
                <form class="header__logout-form" action="/logout" method="post">
                    @csrf
                    <button class="header__logout-button" type="submit">logout</button>
                </form>
            </nav>
        </div>
    </header>

    <main>
        <div class="admin">
            <h2 class="admin__title">Admin</h2>

            <div class="admin__container">
                <div class="admin__search-form-wrapper">
                    <form class="admin__search-form" action="/admin" method="get">
                        <div class="admin__search-input-unit">
                            <input class="admin__search-input" type="search" name="search" placeholder="名前やメールアドレスを入力してください" />
                            <button class="admin__search-button" type="submit">
                                <img src="{{ asset('img/glass_search_icon.svg') }}" />
                            </button>
                        </div>
                        <div class="admin__search-form-item admin__gender-select-unit">
                            <select class="admin__search-form-control admin__gender-select" name="gender">
                                <option value="">性別</option>
                                <option value="">全て</option>
                                <option value="1">男性</option>
                                <option value="2">女性</option>
                                <option value="3">その他</option>
                            </select>
                        </div>
                        <div class="admin__search-form-item admin__category-select-unit">
                            <select class="admin__search-form-control admin__category-select" name="category">
                                <option value="">お問い合わせの種類</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->content }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admin__search-form-item admin__date-input-unit">
                            <input class="admin__search-form-control admin__date-input" type="date" name="date" />
                        </div>
                    </form>
                </div>

                <div class="admin__export-form">
                    <button class="admin__export-button">エクスポート</button>
                </div>

                <div class="admin__pagination-layout">
                    <x-page-links :paginator="$contacts" />
                </div>

                <div class="admin__contact-table-wrapper">
                    <div class="admin__contact-table">
                        <div class="admin__contact-header">
                            <div class="admin__contact-header-item">お名前</div>
                            <div class="admin__contact-header-item">性別</div>
                            <div class="admin__contact-header-item">メールアドレス</div>
                            <div class="admin__contact-header-item">お問い合わせの種類</div>
                        </div>

                        <div class="admin__contact-data">
                            @foreach ($contacts as $contact)
                            <div class="admin__contact-data-item">
                                <div class="admin__contact-name">{{ $contact->easternOrderedName() }}</div>
                                <div class="admin__contact-gender">{{ $contact->genderName() }}</div>
                                <div class="admin__contact-email">{{ $contact->email }}</div>
                                <div class="admin__contact-category">{{ $contact->category->content }}</div>
                                <div class="admin__contact-detail">
                                    <a class="admin__contact-detail-link" href="#detail-{{ $contact->id }}">詳細</a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="admin__reset">
                    <a class="admin__reset-link" href="/admin">リセット</a>
                </div>
            </div>

            @foreach ($contacts as $contact)
            <div class="detail" id="detail-{{ $contact->id }}">
                <a class="detail__overlay" href="#!"></a>
                <div class="detail__modal-window">
                    <div class="detail__modal-close">
                        <a class="detail__close-link" href="#!"></a>
                    </div>

                    <div class="detail__main-layout">
                        <div class="detail__item">
                            <div class="detail__item-header">お名前</div>
                            <div class="detail__item-data">{{ $contact->easternOrderedName() }}</div>
                        </div>
                        <div class="detail__item">
                            <div class="detail__item-header">性別</div>
                            <div class="detail__item-data">{{ $contact->genderName() }}</div>
                        </div>
                        <div class="detail__item">
                            <div class="detail__item-header">メールアドレス</div>
                            <div class="detail__item-data">{{ $contact->email }}</div>
                        </div>
                        <div class="detail__item">
                            <div class="detail__item-header">電話番号</div>
                            <div class="detail__item-data">{{ $contact->tel }}</div>
                        </div>
                        <div class="detail__item">
                            <div class="detail__item-header">住所</div>
                            <div class="detail__item-data">{{ $contact->address }}</div>
                        </div>
                        <div class="detail__item">
                            <div class="detail__item-header">建物名</div>
                            <div class="detail__item-data">{{ $contact->building }}</div>
                        </div>
                        <div class="detail__item">
                            <div class="detail__item-header">お問い合わせの種類</div>
                            <div class="detail__item-data">{{ $contact->category->content }}</div>
                        </div>
                        <div class="detail__item">
                            <div class="detail__item-header">お問い合わせ内容</div>
                            <div class="detail__item-data detail__content">{!! nl2br(e($contact->detail)) !!}</div>
                        </div>
                    </div>

                    <div class="detail__delete-layout">
                        <form class="detail__delete-form" action="/admin/{{ $contact->id }}" method="post">
                            @csrf
                            @method('delete')
                            <button class="detail__delete-button" type="submit">削除</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </main>
</body>

</html>