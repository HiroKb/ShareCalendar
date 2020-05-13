<template>
    <div>
        <form @submit.prevent="sendLink" v-if="!responseFlg">
            <label for="email">メールアドレス</label>
            <input type="text" id="email" v-model="form.email">
            <button type="submit">送信</button>
        </form>

        <p v-else>パスワードリセット用のURLを送信しました。メールを確認してください。</p>
    </div>
</template>

<script>
    export default {
        name: "SendResetPasswordLink",
        data() {
            return {
                form: {
                    email: ''
                },
                responseFlg: false
            }
        },
        methods: {
            async sendLink(){
                this.$store.commit('loading/setLoadingFlg', true)
                await axios.post('/api/password/reset/link', this.form)
                this.$store.commit('loading/setLoadingFlg', false)

                this.responseFlg = true
            }
        }
    }
</script>

<style scoped>

</style>
