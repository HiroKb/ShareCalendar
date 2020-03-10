<template>
    <div>
        <SideBar />
        <div class="edit-user-name">
            <h1>EditUserName</h1>
            <form @submit.prevent="updateName">
                <label for="name">ユーザー名</label>
                <input type="text" id="name" v-model="name">
                <p v-if="errorMessages && errorMessages.name">{{ errorMessages.name }}</p>

                <button type="submit">更新</button>

            </form>
        </div>
    </div>
</template>

<script>
    import SideBar from "../components/SideBar";
    import {mapState} from "vuex";
    export default {
        name: "EditUserName",
        components: {SideBar},
        data(){
            return {
                name: this.$store.getters['user/userName']
            }
        },
        computed: {
            ...mapState({
                apiStatus: state => state.user.apiStatus, // API通信成否
                errorMessages: state => state.user.errorMessages // バリデーションエラーメッセージ
            })
        },
        methods: {
            async updateName () {
                // userストアのupdateNameアクション呼び出し
                await this.$store.dispatch('user/updateName', {name: this.name})

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
.edit-user-name{
    max-width: 500px;
    margin: 0 auto;
}
</style>
