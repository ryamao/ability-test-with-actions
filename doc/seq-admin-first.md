# シーケンス図

## お問い合わせ管理ページ

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
