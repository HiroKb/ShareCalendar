<template>
    <div>
        <SideBar />
        <div class="edit-user-password">
            <h1>EditUserPassword</h1>
            <form @submit.prevent="updatePassword">
                <label for="current-password">現在のパスワード</label>
                <input type="text" id="current-password" v-model="current_password">
                <p v-if="errorMessages && errorMessages.current_password">{{ errorMessages.current_password }}</p>

                <label for="new-password">新しいパスワード</label>
                <input type="text" id="new-password" v-model="new_password">
                <p v-if="errorMessages && errorMessages.new_password">{{ errorMessages.new_password }}</p>

                <button type="submit">更新</button>

            </form>
        </div>
    </div>

</template>

<script>
    import SideBar from "../components/SideBar";
    import {mapState} from "vuex";
    export default {
        name: "EditUserPassword",
        components: {SideBar},
        data(){
            return {
                current_password: '',
                new_password: ''
            }

        },
        computed: {
            ...mapState({
                apiStatus: state => state.user.apiStatus, // API通信成否
                errorMessages: state => state.user.errorMessages // バリデーションエラーメッセージ
            })
        },
        methods: {
            async updatePassword () {
                // userストアのupdatePasswordアクション呼び出し
                await this.$store.dispatch('user/updatePassword', {
                    current_password: this.current_password,
                    new_password: this.new_password,
                })

                // 通信成功時
                if (this.apiStatus) {
                    this.$router.push('/user-info')
                }
            }
        },
        created() {
            this.$store.commit('user/setErrorMessages', null)
        }
    }
</script>

<style scoped>
.edit-user-password{
    max-width: 500px;
    margin: 0 auto;
}
</style>
