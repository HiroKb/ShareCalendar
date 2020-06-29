# ShareCalendar
シンプルに使えるスケジュール共有SPAです。

![demo](https://github.com/HiroKb/ShareCalendar/blob/images/demo.gif)

url: https://share-calendar.link  
ログイン画面下部のボタンからテストアカウントでログインできます。  
## 使用技術
#### フロントエンド
- JavaScript
    - axios
    - Lodash.js
    - Moment.js
- Vue.js 2.6.11
    - Vuetify 2.2.28
    - Vue Router 3.1.6
    - Vuex 3.1.2
#### バックエンド
- PHP 7.4.x
    - intervention/image
- Laravel 6.x
    - socialite
- PHPUnit 8.x
#### インフラ
- AWS
    - Route53
    - CloudFront
    - S3
    - VPC
    - ALB
    - EC2
    - RDS
- Nginx
- MySQL
## インフラ構成
![demo](https://github.com/HiroKb/ShareCalendar/blob/images/infra.png)
## 機能一覧
- ユーザー認証・編集・削除
- メールアドレスの認証・パスワードリセット
- 個人スケジュールの投稿(CRUD)
- グループの管理(CRUD)
- グループの検索・参加申請・許可・拒否
- グループ内チャット
- グループスケジュールの投稿(CRUD)
- 個人スケジュール・参加グループスケジュールの一括表示(カレンダー形式)
- 定時バッチ処理
## ER図
![demo](https://github.com/HiroKb/ShareCalendar/blob/images/er.png)