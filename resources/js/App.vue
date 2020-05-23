<template>
    <v-app>
        <v-app-bar :color="mixinThemeColor" dark app clipped-left>
            <v-app-bar-nav-icon @click="drawer = !drawer" v-if="isLogin"></v-app-bar-nav-icon>
            <v-toolbar-title>
                <router-link :to="{name: 'personalCalendar'}" class="toolbar-title">
                    SHARE CALENDAR
                </router-link>
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items>
                <v-menu bottom :offset-y="true" v-if="isLogin">
                    <template v-slot:activator="{ on }">
                        <v-btn v-on="on" text style="text-transform: none">
                            {{ userName }}<v-icon>mdi-menu-down</v-icon>
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
            />
        </v-content>
        <loading-screen :loading-flg="loadingFlg"/>
    </v-app>
</template>

<script>
    import LoadingScreen from "./components/modules/LoadingScreen.vue"
    import FlashMessage from "./components/modules/FlashMessage"
    import colorsMixin from "./mixins/colorsMixin"
    import {mapGetters, mapState} from 'vuex'
    import {INTERNAL_SERVER_ERROR, NOT_FOUND} from './util'

    export default {
        components: {
            LoadingScreen,
            FlashMessage
        },
        mixins: [colorsMixin],
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
                isLogin: 'user/loginCheck'
            })
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
                handler (val) {
                    if (val === INTERNAL_SERVER_ERROR) {
                        this.$router.push('/500')
                    }else if (val === NOT_FOUND) {
                        this.$router.push('/404')
                    }
                },
                immediate: true
            },
            $route () { // ルーティングに変化があった場合
                this.$store.commit('error/setCode', null)
            }
        }
    }
</script>
<style scoped>
    .toolbar-title{
        color: inherit;
        text-decoration: inherit;
    }
</style>
<style>
    .content-wrap{
        height: calc(100vh - 64px);
    }
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
    .transparent-black{
        color: rgba(0, 0, 0, 0.87)
    }
</style>
