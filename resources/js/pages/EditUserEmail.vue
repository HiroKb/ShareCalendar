<template>

    <div>
        <div class="sidebar-wrap">
            <SideBar />
        </div>
        <div class="contents">
            <h1>EditUserEmail</h1>
            <form @submit.prevent="updateEmail">
                <label for="email">新しいメールアドレス</label>
                <input type="text" id="email" v-model="email">
                <p v-if="errorMessages && errorMessages.email">{{ errorMessages.email [0]}}</p>

                <label for="password">パスワード</label>
                <input type="text" id="password" v-model="password">
                <p v-if="errorMessages && errorMessages.password">{{ errorMessages.password[0] }}</p>

                <button type="submit">更新</button>

            </form>
        </div>
    </div>
</template>

<script>
    import SideBar from "../components/SideBar";
    import {mapState} from "vuex";
    export default {
        name: "EditUserEmail",
        components: {SideBar},
        data(){
            return {
                email: '',
                password: ''
            }

        },
        computed: {
            ...mapState({
                apiStatus: state => state.user.apiStatus, // API通信成否
                errorMessages: state => state.user.errorMessages // バリデーションエラーメッセージ
            })
        },
        methods: {
            async updateEmail () {
                // userストアのupdateEmailアクション呼び出し
                await this.$store.dispatch('user/updateEmail', {
                    email: this.email,
                    password: this.password,
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
</style>
