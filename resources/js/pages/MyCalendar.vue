<template>
    <div>
        <button @click="logout">Logout</button>
        <h1>MyCalendar</h1>
    </div>
</template>

<script>
    import {mapState} from 'vuex'
    export default {
        name: "MyCalendar",
        computed: {
            ...mapState({
                apiStatus: state => state.auth.apiStatus, // API通信成否
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

</style>
