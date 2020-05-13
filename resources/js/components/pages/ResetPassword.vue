<template>
    <div>
        <form @submit.prevent="resetPassword" v-if="!responseFlg">
            <label for="password">新しいパスワード</label>
            <input type="text" id="password" v-model="form.password">
            <button type="submit">送信</button>
        </form>
        <template v-else>
            <p>パスワードを変更しました</p>
            <p><router-link :to="{name: 'login'}">ログインページ</router-link>からログインしてください。</p>
        </template>
    </div>
</template>

<script>
    import {SUCCESS} from "../../util";

    export default {
        name: "ResetPassword",
        data() {
            return {
                form:{
                    token: this.$route.query.token,
                    email: this.$route.query.email,
                    password: ''
                },
                responseFlg: false
            }
        },
        methods: {
            async resetPassword(){
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.post('/api/password/reset', this.form)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    this.responseFlg = true
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            }
        }
    }
</script>

<style scoped>

</style>
