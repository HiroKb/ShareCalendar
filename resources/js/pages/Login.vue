<template>
    <form @submit.prevent="login">
        <label for="email">メールアドレス</label>
        <input type="text" id="email" v-model="loginData.email">
        <p v-if="errorMessages && errorMessages.email">{{ errorMessages.email }}</p>

        <label for="password">パスワード</label>
        <input type="text" id="password" v-model="loginData.password">
        <p v-if="errorMessages && errorMessages.password">{{ errorMessages.password }}</p>

        <button type="submit">ログイン</button>
    </form>
</template>

<script>
    import {mapState} from 'vuex'
    export default {
        name: "Login",
        data() {
            return {
                loginData: {
                    email: '',
                    password: ''
                }
            }
        },
        computed: {
            ...mapState({
                apiStatus: state => state.auth.apiStatus, // API通信成否
                errorMessages: state => state.auth.errorMessages // バリデーションエラーメッセージ
            })
        },
        methods: {
            async login () {
                // authストアのloginアクション呼び出し
                await this.$store.dispatch('auth/login', this.loginData)

                // 通信成功時
                if (this.apiStatus) {
                    this.$router.push('/my-calendar')
                }
            }
        },
        created() {
            this.$store.commit('auth/setErrorMessages', null)
        }
    }
</script>

<style scoped>
    label{
        display: inline-block;
        margin-top: 20px;
    }
    input{
        display: block;
    }
</style>
