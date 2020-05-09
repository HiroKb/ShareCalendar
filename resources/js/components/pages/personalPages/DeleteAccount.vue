<template>
    <div class="contents">
        <form @submit.prevent="deleteAccount">
            <p>アカウントを削除しますか？</p>
            <button>削除</button>
        </form>
    </div>
</template>

<script>
    import {mapState} from "vuex";

    export default {
        name: "DeleteAccount",
        computed: {
            ...mapState({
                apiStatus: state => state.user.apiStatus, // API通信成否
            })
        },
        methods: {
            async deleteAccount() {

                await this.$store.dispatch('user/deleteAccount')

                // 通信成功時
                if (this.apiStatus) {
                    this.$router.push({name: 'welcome'})
                }
            }
        }
    }
</script>

<style scoped>

</style>
