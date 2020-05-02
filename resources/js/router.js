import Vue from 'vue'
import VueRouter from "vue-router"
import store from "./store";

// ページコンポーネント
import Welcome from "./components/pages/Welcome.vue"
import Register from "./components/pages/Register.vue"
import Login from "./components/pages/Login.vue"

import PersonalPages from "./components/pages/personalPages/PersonalPages.vue"
import UserInfo from "./components/pages/personalPages/UserInfo.vue"
import MyCalendar from "./components/pages/personalPages/MyCalendar.vue"
import SharedCalendarList from "./components/pages/personalPages/SharedCalendarList.vue"
import CreateSharedCalendar from "./components/pages/personalPages/CreateSharedCalendar.vue"
import SearchSharedCalendar from "./components/pages/personalPages/SearchSharedCalendar.vue"
import ApplicationSharedCalendar from "./components/pages/personalPages/ApplicationSharedCalendar.vue"
import EditUserPassword from "./components/pages/EditUserPassword.vue"
import EditUserEmail from "./components/pages/EditUserEmail.vue"

import SharedCalendar from "./components/pages/sharedCalendarPages/SharedCalendar.vue"
import SharedCalendarIndex from "./components/pages/sharedCalendarPages/SharedCalendarIndex.vue"
import SharedCalendarMembers from "./components/pages/sharedCalendarPages/SharedCalendarMembers.vue"
import SharedCalendarApplicants from "./components/pages/sharedCalendarPages/SharedCalendarApplicants.vue"
import SharedCalendarInfo from "./components/pages/sharedCalendarPages/SharedCalendarInfo.vue"
import UnShareCalendar from "./components/pages/sharedCalendarPages/UnShareCalendar.vue"


import SystemError from "./components/pages/errors/SystemError.vue"
import NotFound from "./components/pages/errors/NotFound.vue"

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
                path: 'shared-calendar/:searchId/application',
                name: 'applicationSharedCalendar',
                props:true,
                component: ApplicationSharedCalendar,
            },
            {
                path: 'user-info',
                name: 'userInfo',
                component: UserInfo,
            },
        ]
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
    // {
    //     path: '/shared-calendar/:searchId/application',
    //     props:true,
    //     component: ApplicationSharedCalendar,
    //     beforeEnter (to, from, next) {
    //         if (store.getters['user/loginCheck']){
    //             next()
    //         } else {
    //             next('/')
    //         }
    //     }
    // },
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

