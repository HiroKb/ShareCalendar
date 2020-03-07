<template>
    <form @submit.prevent="register">
        <label for="name">表示名</label>
        <input type="text" id="name" v-model="registerData.name">
        <p v-if="errorMessages && errorMessages.name">{{ errorMessages.name }}</p>

        <label for="email">メールアドレス</label>
        <input type="text" id="email" v-model="registerData.email">
        <p v-if="errorMessages && errorMessages.email">{{ errorMessages.email }}</p>

        <label for="password">パスワード</label>
        <input type="text" id="password" v-model="registerData.password">
        <p v-if="errorMessages && errorMessages.password">{{ errorMessages.password }}</p>

        <button type="submit">登録</button>

    </form>
</template>

<script>
    import { mapState } from 'vuex'
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
        computed: {
            ...mapState({
                apiStatus: state => state.auth.apiStatus, // API通信成否
                errorMessages: state => state.auth.errorMessages // バリデーションエラーメッセージ
            })
        },
        methods: {
            async register () {
                // authストアのregisterアクション呼び出し
                await this.$store.dispatch('auth/register', this.registerData)


                // 通信成功の場合
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
