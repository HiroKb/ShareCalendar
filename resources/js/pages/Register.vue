<template>
    <form @submit.prevent="register">
        <label for="name">表示名</label>
        <input type="text" id="name" v-model="registerData.name">

        <label for="email">メールアドレス</label>
        <input type="text" id="email" v-model="registerData.email">

        <label for="password">パスワード</label>
        <input type="text" id="password" v-model="registerData.password">

        <button type="submit">登録</button>
    </form>
</template>

<script>
    export default {
        name: "Register",
        data() {
            return {
                registerData: {
                    name: '',
                    email: '',
                    password: ''
                }
            }
        },
        methods: {
            async register () {
                // authストアのregisterアクション呼び出し
                await this.$store.dispatch('auth/register', this.registerData)

                this.$router.push('/my-calendar')
            }
        }
    }
</script>

<style scoped>

</style>
