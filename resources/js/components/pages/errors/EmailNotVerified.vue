<template>
    <v-container class="py-12">
        <v-card v-if = !isMailSent>
            <v-card-title>403</v-card-title>
            <v-card-text>
                <p>メールアドレスが認証されていません。メールをご確認ください。</p>
                <p>メールが届いていない場合は<span
                    style="color: #1976d2;"
                    @click="resendVerifyEmail"
                >こちら</span>をクリックしてください。</p>
            </v-card-text>
        </v-card>

        <v-card v-else>
            <v-card-text>
                <p>メールアドレス認証用のメールを送信しました。<br>メール内のボタンからアカウント登録を完了させてください。</p>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import {SUCCESS} from "../../../util";

    export default {
        name: "EmailNotVerified",
        data (){
            return {
                isMailSent: false
            }
        },
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
                    this.isMailSent = true
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            }
        }
    }
</script>

<style scoped>
    .container{
        max-width: 600px;
    }
</style>

