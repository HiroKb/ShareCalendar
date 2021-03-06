require('./bootstrap')
import Vue from 'vue'
// ルーター
import router from "./router"
// ストア
import store from "./store";
// ルートコンポーネント
import App from './App.vue'
// Vuetify
import Vuetify from "vuetify"
import 'vuetify/dist/vuetify.css'
import '@mdi/font/css/materialdesignicons.css'
import 'material-design-icons-iconfont/dist/material-design-icons.css'
Vue.use(Vuetify)

const createApp = async () => {
    await store.dispatch('user/getLoginUser')

    new Vue({
        el:'#app',
        vuetify: new Vuetify(),
        router,
        store,
        components: { App },
        template: '<App />'
    })
}

createApp()
