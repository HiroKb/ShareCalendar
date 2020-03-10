import Vue from 'vue'
import VueRouter from "vue-router"
import store from "./store";

// ページコンポーネント
import Welcome from "./pages/Welcome.vue"
import Register from "./pages/Register.vue"
import Login from "./pages/Login.vue"
import UserInfo from "./pages/UserInfo"
import MyCalendar from "./pages/MyCalendar.vue"
import ShareCalendar from "./pages/ShareCalendar.vue"
import SystemError from "./pages/errors/SystemError.vue"
import EditUserName from "./pages/EditUserName";

// VueRouterの使用を宣言
Vue.use(VueRouter)

// ルーティング定義
const routes = [
    {
        path: '/',
        component: Welcome,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next('/my-calendar')
            } else {
                next()
            }
        }
    },
    {
        path: '/register',
        component: Register,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next('/my-calendar')
            } else {
                next()
            }
        }
    },
    {
        path: '/login',
        component: Login,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next('/my-calendar')
            } else {
                next()
            }
        }
    },
    {
        path: '/user-info',
        component: UserInfo,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next('/')
            }
        }
    },
    {
        path: '/edit-username',
        component: EditUserName,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next('/')
            }
        }
    },
    {
        path: '/my-calendar',
        component: MyCalendar,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next('/')
            }
        }
    },
    {
        path: '/share-calendar',
        component: ShareCalendar,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next('/')
            }
        }
    },
    {
        path: '/500',
        component: SystemError
    }
]

// VueRouterインスタンスを作成
const router = new VueRouter({
    mode: 'history',
    routes
})

export default router

