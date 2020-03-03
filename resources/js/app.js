require('./bootstrap')
import Vue from 'vue'
// ルーティング定義
import router from "./router"
// ルートコンポーネント
import App from './App.vue'

new Vue({
    el:'#app',
    router,
    components: { App },
    template: '<App />'
})
