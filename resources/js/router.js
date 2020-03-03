import Vue from 'vue'
import VueRouter from "vue-router"

// ページコンポーネント
import Welcome from "./pages/Welcome.vue"
import Register from "./pages/Register.vue"
import Login from "./pages/Login.vue"
import MyCalendar from "./pages/MyCalendar.vue"
import ShareCalendar from "./pages/ShareCalendar.vue"

// VueRouterの使用を宣言
Vue.use(VueRouter)

// ルーティング定義
const routes = [
    {
        path: '/',
        component: Welcome
    },
    {
        path: '/register',
        component: Register
    },
    {
        path: '/login',
        component: Login
    },
    {
        path: '/my-calendar',
        component: MyCalendar
    },
    {
        path: '/share-calendar',
        component: ShareCalendar
    },

]

// VueRouterインスタンスを作成
const router = new VueRouter({
    mode: 'history',
    routes
})

export default router

