import Vue from 'vue'
import VueRouter from "vue-router"
import store from "./store";

// ページコンポーネント
import Welcome from "./pages/Welcome.vue"
import Register from "./pages/Register.vue"
import Login from "./pages/Login.vue"
import UserInfo from "./pages/UserInfo"
import MyCalendar from "./pages/MyCalendar.vue"
import SharedCalendarList from "./pages/SharedCalendarList.vue"
import CreateSharedCalendar from "./pages/CreateSharedCalendar.vue"
import SearchSharedCalendar from "./pages/SearchSharedCalendar.vue"
import SharedCalendar from "./pages/SharedCalendar.vue"
import EditUserName from "./pages/EditUserName"
import EditUserPassword from "./pages/EditUserPassword"
import EditUserEmail from "./pages/EditUserEmail"

import SystemError from "./pages/errors/SystemError.vue"
import NotFound from "./pages/errors/NotFound.vue";
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
        path: '/edit-user-name',
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
        path: '/edit-user-email',
        component: EditUserEmail,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next('/')
            }
        }
    },
    {
        path: '/edit-user-password',
        component: EditUserPassword,
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
        path: '/shared-calendar/create',
        component: CreateSharedCalendar,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next('/')
            }
        }
    },
    {
        path: '/shared-calendar/list',
        component: SharedCalendarList,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next('/')
            }
        }
    },
    {
        path: '/shared-calendar/search',
        component: SearchSharedCalendar,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next('/')
            }
        }
    },
    {
        path: '/shared-calendar/:sharedCalendarId',
        name: 'sharedCalendar',
        component: SharedCalendar,
        props: true,
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
    },
    {
        path: '/404',
        component: NotFound
    }
]

// VueRouterインスタンスを作成
const router = new VueRouter({
    mode: 'history',
    routes
})

export default router

