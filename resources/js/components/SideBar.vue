<template>
    <div class="sidebar">
        <p>{{ userName }}</p>

        <router-link tag="p"to="/user-info">ユーザー情報/変更</router-link>
        <button @click="logout">Logout</button>
    </div>
</template>

<script>
    import {mapGetters, mapState} from 'vuex'
    export default {
        name: "SideBar",
        computed: {
            ...mapState({
                apiStatus: state => state.auth.apiStatus, // API通信成否
            }),
            ...mapGetters({
                userName: 'auth/userName'
            })
        },
        methods: {
            async logout () {
                // authストアのlogoutアクション呼び出し
                await this.$store.dispatch('auth/logout')

                // 通信成功時
                if (this.apiStatus){
                    this.$router.push('/')
                }
            }
        }
    }
</script>

<style scoped>
.sidebar{
    position: fixed;
    z-index: 1;
}
</style>
