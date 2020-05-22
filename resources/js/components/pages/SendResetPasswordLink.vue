<template>
    <v-container class="mt-12 elevation-1">

        <v-card flat tile>
            <v-card-text>
                <v-form ref="form" @submit.prevent="sendLink" v-if="!responseFlg">

                    <p>ご登録いただいているメールアドレスに<br/>パスワード再設定用のURLを送信いたします。</p>
                    <v-text-field
                        outlined
                        v-model="form.email"
                        label="メールアドレス"
                        :rules="[mixinValidationRules.required]"
                    >
                    </v-text-field>
                    <v-btn block :color="mixinThemeColor" dark type="submit">送信</v-btn>
                </v-form>
                <p v-else>パスワード再設定用のURLを送信いたしました。<br/>メールをご確認ください。</p>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import validationRulesMixin from "../../mixins/validationRulesMixin"
    import colorsMixin from "../../mixins/colorsMixin"
    export default {
        name: "SendResetPasswordLink",
        mixins: [validationRulesMixin, colorsMixin],
        data() {
            return {
                form: {
                    email: ''
                },
                responseFlg: false
            }
        },
        methods: {
            /**
             * メール送信処理
             * @returns {Promise<boolean>}
             */
            async sendLink(){
                // バリデーションチェック
                if (!this.$refs.form.validate()){
                    return false
                }
                this.$store.commit('loading/setLoadingFlg', true)
                await axios.post('/api/password/reset/link', this.form)
                this.$store.commit('loading/setLoadingFlg', false)

                this.responseFlg = true
            }
        }
    }
</script>

<style scoped>
    .container{
        max-width: 600px;
    }
</style>
