# お問い合わせフォーム

## 環境構築

### はじめに

このアプリでは localhost の 80 番と 8080 番を使用しますので、他で使用されていないことを確認してください。

開発および動作確認は MacOS 14 上で行いました。他の環境では動作確認していませんので、不具合があるかもしれませんがご了承ください。

### アプリ起動までの手順

1. このリポジトリの複製をローカルに作成してください。

```
$ git clone https://github.com/ryamao/ability-test.git
```

2. 作成したリポジトリ内で Docker コンテナを起動してください。

```
$ cd ability-test
$ docker compose up -d --build
```

3. Docker コンテナが起動したら php コンテナ内で `composer install` を実行してください。

```
$ docker compose exec php composer install
```

4. `src/.env.example` をコピーして `src/.env` を作成してください。

```
$ cp src/.env.example src/.env
```

5. 作成した `src/.env` の環境変数を変更してください。

```
...
- DB_HOST=127.0.0.1
+ DB_HOST=mysql
...
- DB_DATABASE=laravel
- DB_USERNAME=root
- DB_PASSWORD=
+ DB_DATABASE=laravel_db
+ DB_USERNAME=laravel_user
+ DB_PASSWORD=laravel_pass
...
```

6. 暗号化キーを生成してください。

```
$ docker compose exec php artisan key:generate
```

7. データベースの初期化を行ってください。

```
$ docker compose exec php artisan migrate --seed
```

8. 環境構築後、以下の URL からアクセス可能になります。

- お問い合わせフォームのトップページ
  - http://localhost/
- 管理者ユーザの登録ページ
  - http://localhost/register
- お問い合わせ管理ページ
  - http://localhost/admin
- phpMyAdmin
  - http://localhost:8080/

### テスト環境構築

PHPUnit を実行する場合は以下の手順が必要になります。

1. mysql コンテナ内で MySQL クライアントを起動してください。
   - ユーザ : `root`
   - パスワード : `root`

```
$ docker compose exec mysql mysql -u root -p
Enter password: root
```

2. MySQL ユーザを作成してください。
   - ユーザ : `test_user`
   - パスワード : `test_pass`

```
mysql> CREATE USER test_user IDENTIFIED BY 'test_pass';
```

3. データベースを作成し、新規ユーザに権限を付与してください。

```
mysql> CREATE DATABASE test_db;
mysql> GRANT ALL PRIVILEGES ON test_db.* TO test_user;
mysql> exit
```

4. テスト環境でマイグレーションを行ってください。

```
$ docker compose exec php artisan migrate --env=testing
```

5. テスト環境構築後、PHPUnit が実行可能になります。

```
$ docker compose exec php artisan test --testdox
```

## 使用技術

- PHP : 8.3.1
  - Laravel : 10.39.0
  - Laravel Fortify : 1.19.1
- MySQL : 8.0
- Nginx : 1.24

## ER図

![ER図](er.drawio.svg)

## CRUD図

| ルートパス | メソッド | users | categories | contacts
| ------------------ | ------ | ---- | ---- | ---- |
| `/`                | GET    |      |  R   |      |
| `/`                | POST   |      |  R   |      |
| `/confirm`         | POST   |      |  R   |      |
| `/contact`         | POST   |      |  R   | C    |
| `/thanks`          | GET    |      |      |      |
| `/register`        | GET    |      |      |      |
| `/register`        | POST   | CR   |      |      |
| `/login`           | GET    |      |      |      |
| `/login`           | POST   |  R   |      |      |
| `/logout`          | POST   |      |      |      |
| `/admin`           | GET    |      |  R   |  R   |
| `/admin/{contact}` | DELETE |      |      |    D |
| `/admin/export`    | GET    |      |  R   |  R   |

## シーケンス図

### お問い合わせ入力（バリデーションエラーが発生しなかった場合）

```mermaid
sequenceDiagram
    actor       U as ユーザ
    participant B as ブラウザ
    participant A as アプリ
    participant D as データベース

    U ->>   B : http://localhost/
    B ->>+  A : GET /
    A ->>+  D : categories を全取得
    D -->>- A : categories のデータ
    A ->>   A : view(contact) + categories<br />→ HTML
    A -->>- B : HTML
    B -->>  U : お問い合わせ入力ページを表示

    U ->>   B : フォームに入力
    U ->>   B : 確認画面ボタンを押下
    B ->>+  A : POST /confirm
    A ->>   A : フォームデータのバリデーション<br />→ OK
    A ->>+  D : categories を全取得
    D -->>- A : categories のデータ
    A ->>   A : view(confirm) + categories + フォームデータ<br />→ HTML
    A -->>- B : HTML
    B -->>  U : お問い合わせ確認ページを表示

    U ->>   B : 送信ボタンを押下
    B ->>+  A : POST /contact
    A ->>   A : フォームデータのバリデーション<br />→ OK
    A ->>+  D : フォームデータを contacts に保存
    D -->>- A : OK
    A -->>- B : リダイレクト → /thanks

    B ->>+  A : GET /thanks
    A ->>   A : view(thanks) → HTML
    A -->>- B : HTML
    B -->>  U : サンクスページを表示
```

### お問い合わせ入力（入力ページでバリデーションエラーが発生した場合）

```mermaid
sequenceDiagram
    actor       U as ユーザ
    participant B as ブラウザ
    participant A as アプリ
    participant D as データベース

    U ->>   B : http://localhost/
    B ->>+  A : GET /
    A ->>+  D : categories を全取得
    D -->>- A : categories のデータ
    A ->>   A : view(contact) + categories<br />→ HTML
    A -->>- B : HTML
    B -->>  U : お問い合わせ入力ページを表示

    U ->>   B : フォームに入力
    U ->>   B : 確認画面ボタンを押下
    B ->>+  A : POST /confirm
    A ->>   A : フォームデータのバリデーション<br />→ エラー
    A ->>   A : フォームデータをセッションに保存
    A -->>- B : リダイレクト → /

    B ->>+  A : GET /
    A ->>   A : view(contact) + セッションデータ<br />→ HTML
    A -->>- B : HTML
    B -->>  U : お問い合わせ入力ページを表示
```

### お問い合わせ入力（確認ページで修正ボタンを押した場合）

```mermaid
sequenceDiagram
    actor       U as ユーザ
    participant B as ブラウザ
    participant A as アプリ
    participant D as データベース

    Note over U, D: 省略
    B -->>  U : お問い合わせ確認ページを表示

    U ->>   B : 修正ボタンを押下
    B ->>+  A : POST /
    A ->>   A : フォームデータのバリデーション<br />→ OK
    A ->>+  D : categories を全取得
    D -->>- A : categories のデータ
    A ->>   A : view(contact) + categories + フォームデータ<br />→ HTML
    A -->>- B : HTML
    B -->>  U : お問い合わせ入力ページを表示
```

### ユーザ登録から管理ページ表示まで

1. 未認証の状態で http://localhost/admin にアクセスする。
2. ログインページにリダイレクトする。
3. ログインページからユーザ登録ページにアクセスする。
4. ユーザ登録ページでフォームに入力し登録ボタンを押す。
5. ログインページにリダイレクトする。
6. ログインページでフォームに入力しログインボタンを押す。
7. お問い合わせ管理ページが表示される。

```mermaid
sequenceDiagram
    actor       U as ユーザ
    participant B as ブラウザ
    participant A as アプリ
    participant D as データベース

    U ->>   B : http://localhost/admin
    B ->>+  A : GET /admin
    A ->>   A : Cookieとセッションで認証<br />→ 未認証
    A -->>- B : リダイレクト → /login

    B ->>+  A : GET /login
    A ->>   A : Cookieとセッションで認証<br />→ 未認証
    A ->>   A : view(login) → HTML
    A -->>- B : HTML
    B -->>  U : ログインページを表示

    U ->>   B : registerボタンを押下
    B ->>+  A : GET /register
    A ->>   A : Cookieとセッションで認証<br />→ 未認証
    A ->>   A : view(register) → HTML
    A -->>- B : HTML
    B -->>  U : ユーザ登録ページを表示

    U ->>   B : フォームに入力
    U ->>   B : 登録ボタンを押下
    B ->>+  A : POST /register
    A ->>   A : フォームデータのバリデーション<br />→ OK
    A ->>+  D : フォームデータを users に保存
    D -->>- A : OK
    A ->>   A : 現在のユーザの認証を解除
    A -->>- B : リダイレクト → /login

    B ->>+  A : GET /login
    A ->>   A : Cookieとセッションで認証<br />→ 未認証
    A ->>   A : view(login) → HTML
    A -->>- B : HTML
    B -->>  U : ログインページを表示

    U ->>   B : フォームに入力
    U ->>   B : ログインボタンを押下
    B ->>+  A : POST /login
    A ->>   A : フォームデータのバリデーション<br />→ OK
    A ->>+  D : フォームデータで users を検索
    D -->>- A : users に保存済み
    A ->>   A : 現在のユーザの認証を設定
    A -->>- B : リダイレクト → /admin

    B ->>+  A : GET /admin
    A ->>   A : Cookieとセッションで認証<br />→ 認証済み
    A ->>+  D : categories を全取得
    D -->>- A : categories の全データ
    A ->>+  D : contacts を作成日の昇順でソート<br />最初の 10 件を取得
    D -->>- A : contacts のデータ
    A ->>+  D : contacts のデータに関連する<br />categories を取得
    D -->>- A : categories のデータ
    A ->>   A : view(admin) + categories + contacts<br />→ HTML
    A -->>- B : HTML
    B -->>  U : お問い合わせ管理ページを表示
```

### 管理ページでのお問い合わせの検索

```mermaid
sequenceDiagram
    actor       U as ユーザ
    participant B as ブラウザ
    participant A as アプリ
    participant D as データベース

    Note over U, D: 省略
    B -->>  U : お問い合わせ管理ページを表示

    U ->>   B : 検索フォームに「foo」を入力
    U ->>   B : 検索ボタンを押下
    B ->>+  A : GET /admin<br />?search=foo<br />&gender=<br />&category=<br />&date=
    A ->>   A : Cookieとセッションで認証<br />→ 認証済み
    A ->>+  D : categories を全取得
    D -->>- A : categories の全データ
    A ->>   A : クエリストリングから検索クエリを作成
    A ->>+  D : 検索クエリで contacts を検索<br />作成日の昇順でソート<br />最初の 10 件を取得
    D -->>- A : contacts のデータ
    A ->>+  D : contacts のデータに関連する<br />categories を取得
    D -->>- A : categories のデータ
    A ->>   A : view(admin) + categories + contacts<br />→ HTML
    A -->>- B : HTML
    B -->>  U : お問い合わせ管理ページを表示

    U ->>   B : 性別リストで「男性」を選択
    U ->>   B : 検索ボタンを押下
    B ->>+  A : GET /admin<br />?search=foo<br />&gender=1<br />&category=<br />&date=
    Note over A, D: 省略
    A -->>- B : HTML
    B -->>  U : お問い合わせ管理ページを表示

    U ->>   B : お問い合わせ種類リストで<br />「商品のお届けについて」を選択
    U ->>   B : 検索ボタンを押下
    B ->>+  A : GET /admin<br />?search=foo<br />&gender=1<br />&category=1<br />&date=
    Note over A, D: 省略
    A -->>- B : HTML
    B -->>  U : お問い合わせ管理ページを表示

    U ->>   B : お問い合わせ種類リストで<br />「2024/01/10」を選択
    U ->>   B : 検索ボタンを押下
    B ->>+  A : GET /admin<br />?search=foo<br />&gender=1<br />&category=1<br />&date=2024-01-10
    Note over A, D: 省略
    A -->>- B : HTML
    B -->>  U : お問い合わせ管理ページを表示

    U ->>   B : ページリンクの「>」を押下
    B ->>+  A : GET /admin<br />?search=foo<br />&gender=1<br />&category=1<br />&date=2024-01-10<br />&page=2
    A ->>   A : Cookieとセッションで認証<br />→ 認証済み
    A ->>+  D : categories を全取得
    D -->>- A : categories の全データ
    A ->>   A : クエリストリングから検索クエリを作成
    A ->>+  D : 検索クエリで contacts を検索<br />作成日の昇順でソート<br />次の 10 件を取得
    D -->>- A : contacts のデータ
    A ->>+  D : contacts のデータに関連する<br />categories を取得
    D -->>- A : categories のデータ
    A ->>   A : view(admin) + categories + contacts<br />→ HTML
    A -->>- B : HTML
    B -->>  U : お問い合わせ管理ページを表示

    U ->>   B : リセットボタンを押下
    B ->>+  A : GET /admin
    A ->>   A : Cookieとセッションで認証<br />→ 認証済み
    A ->>+  D : categories を全取得
    D -->>- A : categories の全データ
    A ->>+  D : contacts を作成日の昇順でソート<br />最初の 10 件を取得
    D -->>- A : contacts のデータ
    A ->>+  D : contacts のデータに関連する<br />categories を取得
    D -->>- A : categories のデータ
    A ->>   A : view(admin) + categories + contacts<br />→ HTML
    A -->>- B : HTML
    B -->>  U : お問い合わせ管理ページを表示
```

### 管理ページでのお問い合わせの削除

この説明で削除するお問い合わせは contacts テーブルでの id が 123 であるとします。

```mermaid
sequenceDiagram
    actor       U as ユーザ
    participant B as ブラウザ
    participant A as アプリ
    participant D as データベース

    Note over U, D: 省略
    B -->>  U : お問い合わせ管理ページを表示

    U ->>   B : 詳細ボタンを押下
    B -->>  U : モーダルウィンドウを表示
    U ->>   B : 削除ボタンを押下
    B ->>+  A : DELETE /admin/123<br />?search=foo<br />&gender=1<br />&category=1<br />&date=2024-01-10<br />&page=2
    A ->>   A : Cookieとセッションで認証<br />→ 認証済み
    A ->>+  D : contacts を id = 123 で検索
    D -->>- A : contacts のデータ
    A ->>+  D : contacts から id = 123 を削除
    D -->>- A : OK
    A -->>- B : リダイレクト → /admin<br />?search=foo<br />&gender=1<br />&category=1<br />&date=2024-01-10<br />&page=2

    B ->>+  A : GET /admin<br />?search=foo<br />&gender=1<br />&category=1<br />&date=2024-01-10<br />&page=2
    Note over A, D: 省略
    A -->>- B : HTML
    B -->>  U : お問い合わせ管理ページを表示
```

### 管理ページでのお問い合わせのエクスポート

```mermaid
sequenceDiagram
    actor       U as ユーザ
    participant B as ブラウザ
    participant A as アプリ
    participant D as データベース

    Note over U, D: 省略
    B -->>  U : お問い合わせ管理ページを表示

    U ->>   B : エクスポートボタンを押下
    B ->>+  A : GET /admin/export<br />?search=foo<br />&gender=1<br />&category=1<br />&date=2024-01-10<br />&page=2
    A ->>   A : Cookieとセッションで認証<br />→ 認証済み
    A ->>   A : クエリストリングから検索クエリを作成
    A ->>+  D : 検索クエリで contacts を検索<br />作成日の昇順でソート
    D -->>- A : contacts のデータ
    A ->>+  D : contacts のデータに関連する<br />categories を取得
    D -->>- A : categories のデータ
    A ->>   A : contacts + categories → CSVデータ
    A -->>- B : CSVデータをストリーミング
    B -->>  U : CSVファイルをダウンロード
```

## URL

- 開発環境
  - お問い合わせフォーム入力ページ : http://localhost/
  - 管理者ユーザ登録ページ : http://localhost/register
  - 管理者ユーザログインページ : http://localhost/login
  - お問い合わせ管理ページ : http://localhost/admin
- phpMyAdmin : http://localhost:8080/

## 検索の仕様

お問い合わせ管理ページでの「名前・メールアドレス」検索について説明します。

- デフォルトでは入力文字列を使って部分一致で検索します。
- スペースで区切ることで複数語による絞り込み検索ができます。
- 文字列を `[` `]` で囲むと完全一致で検索します。
- 検索対象は「姓」「名」「メールアドレス」です。

例として以下のデータを使って説明します。

| 姓 | 名 | メールアドレス |
| --- | --- | --- |
| 山田 | 一郎 | test1@example.com |
| 三村 | 一 | test2@example.com |
| 田村 | 三郎 | test3@example.com |

### `一` で検索した場合

| 姓 | 名 | メールアドレス |
| --- | --- | --- |
| 山田 | 一郎 | test1@example.com |
| 三村 | 一 | test2@example.com |

デフォルトでは部分一致で検索します。

### `[一]` で検索した場合

| 姓 | 名 | メールアドレス |
| --- | --- | --- |
| 三村 | 一 | test2@example.com |

`[` `]` で囲むと完全一致で検索します。

### `一 test1` で検索した場合

| 姓 | 名 | メールアドレス |
| --- | --- | --- |
| 山田 | 一郎 | test1@example.com |

スペースで区切ると絞り込み検索します。

### `三` で検索した場合

| 姓 | 名 | メールアドレス |
| --- | --- | --- |
| 三村 | 一 | test2@example.com |
| 田村 | 三郎 | test3@example.com |

各カラムをそれぞれ検索し、結果の和集合を表示します。
