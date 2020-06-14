<template>
    <v-container class="container-mt medium-container">
        <v-card>
            <v-card-text>
                <p>メールアドレス認証用のメールを送信しました。<br>メール内のボタンからアカウント登録を完了させてください。</p>
                <p>メールが届いていない場合は<span
                    style="color: #1976d2;"
                    @click="resendVerifyEmail"
                >こちら</span>をクリックしてください。</p>
            </v-card-text>
        </v-card>

    </v-container>
</template>

<script>
    import {SUCCESS} from "../../util";

    export default {
        name: "EmailVerification",
        methods: {
            /**
             * 認証メール再送処理
             * @return {Promise<boolean>}
             */
            async resendVerifyEmail(){

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.post('/api/email/resend')
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            }
        }

    }
</script>

<style scoped>
</style>
