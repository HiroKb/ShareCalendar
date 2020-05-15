<template>
</template>

<script>
    import {mapGetters, mapState} from 'vuex'
    export default {
        name: "SideBar",
        props:{
            drawer:{
                type: Boolean,
            }
        },
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
