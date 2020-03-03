require('./bootstrap')
import Vue from 'vue'
// ルーター
import router from "./router"
// ストア
import store from "./pages/store";
// ルートコンポーネント
import App from './App.vue'

new Vue({
    el:'#app',
    router,
    store,
    components: { App },
    template: '<App />'
})
