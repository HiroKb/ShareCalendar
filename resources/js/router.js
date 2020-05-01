import Vue from 'vue'
import VueRouter from "vue-router"
import store from "./store";

// ページコンポーネント
import Welcome from "./pages/Welcome.vue"
import Register from "./pages/Register.vue"
import Login from "./pages/Login.vue"

import PersonalPages from "./pages/PersonalPages.vue"
import UserInfo from "./pages/UserInfo.vue"
import MyCalendar from "./pages/MyCalendar.vue"
import SharedCalendarList from "./pages/SharedCalendarList.vue"
import CreateSharedCalendar from "./pages/CreateSharedCalendar.vue"
import SearchSharedCalendar from "./pages/SearchSharedCalendar.vue"
import ApplicationSharedCalendar from "./pages/ApplicationSharedCalendar.vue"
import EditUserName from "./pages/EditUserName.vue"
import EditUserPassword from "./pages/EditUserPassword.vue"
import EditUserEmail from "./pages/EditUserEmail.vue"

import SharedCalendar from "./pages/SharedCalendar.vue"
import SharedCalendarIndex from "./pages/SharedCalendarIndex.vue"
import SharedCalendarMembers from "./pages/SharedCalendarMembers.vue"
import SharedCalendarApplicants from "./pages/SharedCalendarApplicants.vue"
import SharedCalendarInfo from "./pages/SharedCalendarInfo.vue"
import UnShareCalendar from "./pages/UnShareCalendar.vue"


import SystemError from "./pages/errors/SystemError.vue"
import NotFound from "./pages/errors/NotFound.vue"

// VueRouterの使用を宣言
Vue.use(VueRouter)

// ルーティング定義
const routes = [
    {
        path: '/',
        name: 'welcome',
        component: Welcome,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next({name: 'myCalendar'})
            } else {
                next()
            }
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next({name: 'myCalendar'})
            } else {
                next()
            }
        }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next({name: 'myCalendar'})
            } else {
                next()
            }
        }
    },
    {
        path: '/personal-pages',
        component: PersonalPages,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next({name: 'welcome'})
            }
        },
        children: [
            {
                path: 'my-calendar',
                name: 'myCalendar',
                component: MyCalendar,
            },
            {
                path: 'shared-calendar/list',
                name: 'sharedCalendarList',
                component: SharedCalendarList,
            },
            {
                path: 'shared-calendar/create',
                name: 'createSharedCalendar',
                component: CreateSharedCalendar,
            },
            {
                path: 'shared-calendar/search',
                name: 'searchSharedCalendar',
                component: SearchSharedCalendar,
            },
            {
                path: 'user-info',
                name: 'userInfo',
                component: UserInfo,
            },
        ]
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
        path: '/shared-calendar/:searchId/application',
        props:true,
        component: ApplicationSharedCalendar,
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
        component: SharedCalendar,
        props: true,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next('/')
            }
        },
        children: [
            {
                path: 'calendar',
                name: 'sharedCalendarIndex',
                component: SharedCalendarIndex,
            },
            {
                path: 'members',
                name: 'sharedCalendarMembers',
                component: SharedCalendarMembers
            },
            {
                path: 'applicants',
                name: 'sharedCalendarApplicants',
                component: SharedCalendarApplicants,
            },
            {
                path: 'info',
                name: 'sharedCalendarInfo',
                component: SharedCalendarInfo,
            },
            {
                path: 'unshare',
                name: 'unShareCalendar',
                component: UnShareCalendar,
            },
        ]
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

