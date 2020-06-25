<template>
    <v-app>
        <v-app-bar :color="mixinThemeColor" dark app clipped-left>
            <v-app-bar-nav-icon @click="drawer = !drawer" v-if="isLogin && isPageShowNavigation"></v-app-bar-nav-icon>
            <v-toolbar-title
                class="pl-1 pl-md-5"
            >
                <router-link
                    :to="{name: 'personalCalendar'}"
                    class="toolbar-title font-weight-bold"
                >
                    ShareCalendar
                </router-link>
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items>
                <v-menu bottom :offset-y="true" v-if="isLogin && isPageShowNavigation">
                    <template v-slot:activator="{ on }">
                        <v-btn
                            v-on="on" text style="text-transform: none">
                            <div class="d-flex align-center">
                                <v-img
                                    :src="userImage"
                                    height="50px"
                                    width="50px"
                                    aspect-ratio="1"
                                    style="border-radius: 50%"
                                ></v-img>
                                <p class="d-none d-sm-block  ma-0 ml-3 text-truncate user-name">{{ userName }}</p>
                                <v-icon>mdi-menu-down</v-icon>
                            </div>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item :to="{name: 'personalCalendar'}">
                            <v-list-item-content>マイページ</v-list-item-content>
                        </v-list-item>
                        <v-list-item :to="{name: 'userInfo'}">
                            <v-list-item-content>アカウント情報/変更</v-list-item-content>
                        </v-list-item>
                        <v-list-item @click="logout">
                            <v-list-item-content>ログアウト</v-list-item-content>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-toolbar-items>
        </v-app-bar>
        <v-content>
            <flash-message :message="message"/>
            <router-view :drawer="drawer"
                         @changeDrawer="drawer = $event"
                         ref="test"
            />
        </v-content>
        <loading-screen :loading-flg="loadingFlg"/>
    </v-app>
</template>

<script>
    import LoadingScreen from "./components/modules/LoadingScreen.vue"
    import FlashMessage from "./components/modules/FlashMessage"
    import utilDataMixin from "./mixins/utilDataMixin"
    import {mapGetters, mapState} from 'vuex'
    import {
        AUTHENTICATION_REQUIRED,
        CSRF_NOT_EXIST, EMAIL_NOT_VERIFIED,
        INTERNAL_SERVER_ERROR,
        NOT_FOUND,
        ONLY_UNAUTHENTICATED, VALIDATION_ERROR,
    } from './util'

    export default {
        components: {
            LoadingScreen,
            FlashMessage
        },
        mixins: [utilDataMixin],
        data() {
            return {
                drawer: null
            }
        },
        computed: {
            ...mapState({
                errorCode: state => state.error.code,
                loadingFlg: state => state.loading.loadingFlg,
                message: state => state.flashMessage.message,
                authApiStatus: state => state.user.apiStatus
            }),
            ...mapGetters({
                userName: 'user/userName',
                isLogin: 'user/loginCheck',
                userImage: 'user/userImage'
            }),
            // ヘッダーナビゲーションを表示するか
            isPageShowNavigation: function () {
                return !['emailNotVerified', 'systemError', 'notFound'].includes(this.$route.name)
            }
        },
        methods: {
            async logout () {
                // userストアのlogoutアクション呼び出し
                await this.$store.dispatch('user/logout')

                // 通信成功時
                if (this.authApiStatus){
                    this.$router.push({name: 'authentication'})
                }
            }
        },
        watch: {
             errorCode: { // エラーコードに変化があった場合
                async handler (val) {
                    if (val === INTERNAL_SERVER_ERROR) {
                        const path = '/500'
                        this.$route.path !== path && this.$router.push(path)
                    }else if (val === NOT_FOUND || val === VALIDATION_ERROR) {
                        const path = '/404'
                        this.$route.path !== path && this.$router.push(path)
                    } else if (val === EMAIL_NOT_VERIFIED){
                        const path = '/443'
                        this.$route.path !== path && this.$router.push(path)
                    } else if (val === AUTHENTICATION_REQUIRED || val === CSRF_NOT_EXIST) {
                        await axios.get('/api/refresh-token')
                        this.$store.commit('user/setUser', null)
                        this.$router.push({name: 'authentication'})
                    } else if (val === ONLY_UNAUTHENTICATED) {
                        this.$router.push({name: 'personalCalendar'})
                    }
                },
                immediate: true
            },
            $route () { // ルーティングに変化があった場合
                this.$store.commit('error/setCode', null)
                scrollTo(0,0)
            }
        }
    }
</script>
<style scoped>
    .toolbar-title{
        color: inherit;
        text-decoration: inherit;
    }
    .user-name{
        max-width: 500px;
    }
    @media screen and (max-width: 959px){
        .user-name{
            max-width: 230px;
        }
    }
    @media screen and (max-width: 599px){
        .user-name{
            max-width: 100px;
        }
    }
</style>
<style>
    .custom-scrollbar::-webkit-scrollbar
    {
        width:2px;
        background:#fafafa;
    }
    .custom-scrollbar::-webkit-scrollbar-button
    {
        display:none;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb
    {
        background: #919191;
    }

    .fixed-content{
        height: calc(100vh - 64px);
        padding-top: 48px !important;
        padding-bottom: 48px !important;
    }
    .container-mt{
        margin-top: 48px;
    }
    .medium-container{
        max-width: 600px !important;
    }
    .fill-height-card{
        height: 100%;
    }
    @media screen and (max-width: 959px){
        .fixed-content{
            height: calc(100vh - 56px);
            padding-top: 12px !important;
            padding-bottom: 12px !important;
        }
    }
    @media screen and (max-width: 599px){
        .container-mt{
            margin-top: 0;
        }
        .fill-height-card{
            height: 85%;
        }
    }
</style>
