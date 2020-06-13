<template>
    <v-container class="mt-12">

        <v-card>
            <v-card-text>
                <v-form ref="form" @submit.prevent="sendLink" v-if="!isSent">

                    <p>ご登録いただいているメールアドレスに<br/>パスワード再設定用のURLを送信いたします。</p>
                    <v-text-field
                        outlined
                        v-model="form.email"
                        label="メールアドレス"
                        :rules="[mixinValidationRules.required]"
                    >
                    </v-text-field>
                    <v-btn
                        class="font-weight-bold"
                        block :color="mixinThemeColor"
                        dark type="submit"
                    >送信</v-btn>
                </v-form>
                <p v-else>パスワード再設定用のURLを送信いたしました。<br/>メールをご確認ください。</p>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import validationRulesMixin from "../../mixins/validationRulesMixin"
    import utilDataMixin from "../../mixins/utilDataMixin"
    export default {
        name: "SendResetPasswordLink",
        mixins: [validationRulesMixin, utilDataMixin],
        data() {
            return {
                form: {
                    email: ''
                },
                isSent: false
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

                this.isSent = true
            }
        }
    }
</script>

<style scoped>
    .container{
        max-width: 600px;
    }
</style>
