# シーケンス図

## お問い合わせ入力

### 入力ページでバリデーションエラーが発生した場合

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
