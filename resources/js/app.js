require('./bootstrap')
import Vue from 'vue'
// ルーター
import router from "./router"
// ストア
import store from "./store";
// ルートコンポーネント
import App from './App.vue'

const createApp = async () => {
    await store.dispatch('auth/getLoginUser')

    new Vue({
        el:'#app',
        router,
        store,
        components: { App },
        template: '<App />'
    })
}

createApp()
