<template>
    <div class="sidebar">
        <p>{{ userName }}</p>


        <router-link :to="{name: 'myCalendar'}">マイカレンダー</router-link>

        <router-link :to="{name: 'sharedCalendarList'}">共有カレンダー一覧</router-link>

        <router-link :to="{name: 'createSharedCalendar'}">共有カレンダー作成</router-link>

        <router-link :to="{name: 'searchSharedCalendar'}">共有カレンダー検索</router-link>

        <router-link :to="{name: 'userInfo'}">ユーザー情報/変更</router-link>
        <button @click="logout">Logout</button>
    </div>
</template>

<script>
    import {mapGetters, mapState} from 'vuex'
    export default {
        name: "SideBar",
        computed: {
            ...mapState({
                apiStatus: state => state.user.apiStatus, // API通信成否
            }),
            ...mapGetters({
                userName: 'user/userName'
            })
        },
        methods: {
            async logout () {
                // userストアのlogoutアクション呼び出し
                await this.$store.dispatch('user/logout')

                // 通信成功時
                if (this.apiStatus){
                    this.$router.push({name: 'welcome'})
                }
            }
        }
    }
</script>

<style scoped>
.sidebar{
}
    a{
        display: block;
    }
</style>
