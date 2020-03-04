<template>
    <form @submit.prevent="login">
        <label for="email">メールアドレス</label>
        <input type="text" id="email" v-model="loginData.email">

        <label for="password">パスワード</label>
        <input type="text" id="password" v-model="loginData.password">

        <button type="submit">ログイン</button>
    </form>
</template>

<script>
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
        methods: {
            async login () {
                // authストアのregisterアクション呼び出し
                await this.$store.dispatch('auth/login', this.loginData)
            }
        }
    }
</script>

<style scoped>

</style>
