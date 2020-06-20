import Vue from 'vue'
import VueRouter from "vue-router"
import store from "./store";

// ページコンポーネント
import Top from "./components/pages/Top.vue"
import Authentication from "./components/pages/Authentication.vue"
import EmailVerification from "./components/pages/EmailVerification.vue"
import SendResetPasswordLink from "./components/pages/SendResetPasswordLink.vue"
import ResetPassword from "./components/pages/ResetPassword.vue"

import TermsOfUse from "./components/pages/TermsOfUse.vue"
import PrivacyPolicy from "./components/pages/PrivacyPolicy.vue"

import PersonalPages from "./components/pages/personalPages/PersonalPages.vue"
import UserInfo from "./components/pages/personalPages/UserInfo.vue"
import PersonalCalendar from "./components/pages/personalPages/PersonalCalendar.vue"
import SharedCalendarList from "./components/pages/personalPages/SharedCalendarList.vue"
import CreateSharedCalendar from "./components/pages/personalPages/CreateSharedCalendar.vue"
import SearchSharedCalendar from "./components/pages/personalPages/SearchSharedCalendar.vue"
import ApplicationSharedCalendar from "./components/pages/personalPages/ApplicationSharedCalendar.vue"
import DeleteAccount from "./components/pages/personalPages/DeleteAccount.vue"

import SharedCalendarPages from "./components/pages/sharedCalendarPages/SharedCalendarPages.vue"
import SharedCalendar from "./components/pages/sharedCalendarPages/SharedCalendar.vue"
import SharedCalendarChat from "./components/pages/sharedCalendarPages/SharedCalendarChat.vue"
import SharedCalendarMembers from "./components/pages/sharedCalendarPages/SharedCalendarMembers.vue"
import SharedCalendarApplicants from "./components/pages/sharedCalendarPages/SharedCalendarApplicants.vue"
import SharedCalendarInfo from "./components/pages/sharedCalendarPages/SharedCalendarInfo.vue"
import UnShareCalendar from "./components/pages/sharedCalendarPages/UnShareCalendar.vue"
import DeleteSharedCalendar from "./components/pages/sharedCalendarPages/DeleteSharedCalendar.vue"


import EmailNotVerified from "./components/pages/errors/EmailNotVerified.vue"
import SystemError from "./components/pages/errors/SystemError.vue"
import NotFound from "./components/pages/errors/NotFound.vue"

// VueRouterの使用を宣言
Vue.use(VueRouter)

// ルーティング定義
const routes = [
    {
        path: '/',
        name: 'top',
        component: Top,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next({name: 'personalCalendar'})
            } else {
                next()
            }
        }
    },
    {
        path: '/auth',
        name: 'authentication',
        props: true,
        component: Authentication,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next({name: 'personalCalendar'})
            } else {
                next()
            }
        }
    },
    {
        path: '/terms-of-use',
        name: 'termsOfUse',
        component: TermsOfUse
    },
    {
        path: '/privacy-policy',
        name: 'privacyPolicy',
        component: PrivacyPolicy
    },
    {
        path: '/verify',
        name: 'emailVerification',
        component: EmailVerification,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next({name: 'authentication'})
            }
        }
    },
    {
        path: '/password/reset/link',
        name: 'sendResetPasswordLink',
        component: SendResetPasswordLink,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next({name: 'personalCalendar'})
            } else {
                next()
            }
        }
    },
    {
        path: '/password/reset',
        name: 'resetPassword',
        component: ResetPassword,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next({name: 'personalCalendar'})
            } else {
                next()
            }
        }
    },
    {
        path: '/personal',
        component: PersonalPages,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next({name: 'authentication'})
            }
        },
        children: [
            {
                path: 'calendar',
                name: 'personalCalendar',
                component: PersonalCalendar,
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
                path: 'account/info',
                name: 'userInfo',
                component: UserInfo,
            },
            {
                path: 'account/delete',
                name: 'deleteAccount',
                component: DeleteAccount,
            }
        ]
    },
    {
        path: '/shared-calendar/:sharedCalendarId',
        component: SharedCalendarPages,
        props: true,
        beforeEnter (to, from, next) {
            if (store.getters['user/loginCheck']){
                next()
            } else {
                next({name: 'authentication'})
            }
        },
        children: [
            {
                path: 'calendar',
                name: 'sharedCalendar',
                component: SharedCalendar,
            },
            {
                path: 'chat',
                name: 'sharedCalendarChat',
                component: SharedCalendarChat
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
            {
                path: 'delete',
                name: 'deleteSharedCalendar',
                component: DeleteSharedCalendar,
            },
        ]
    },
    {
        path: '/443',
        name: 'emailNotVerified',
        component: EmailNotVerified
    },
    {
        path: '/500',
        name: 'systemError',
        component: SystemError
    },
    {
        path: '*',
        name: 'notFound',
        component: NotFound
    },

]

// VueRouterインスタンスを作成
const router = new VueRouter({
    mode: 'history',
    routes
})

export default router

