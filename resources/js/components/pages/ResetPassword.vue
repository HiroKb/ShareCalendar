<template>
    <v-container class="container-mt medium-container">

        <v-card>
            <v-card-text>
                <v-form ref="form" @submit.prevent="resetPassword" v-if="!isUpdated">

                    <v-text-field
                        outlined
                        v-model="form.password"
                        label="新しいパスワード"
                        @click:append="showPassword = !showPassword"
                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        :type="showPassword ? 'text' : 'password'"
                        :rules="[mixinValidationRules.required, mixinValidationRules.min8]"
                    >
                    </v-text-field>
                    <v-btn
                        class="font-weight-bold"
                        block :color="mixinThemeColor"
                        dark type="submit"
                    >変更</v-btn>
                </v-form>
                <template v-else>
                    <p>パスワードを変更しました。<br/><router-link :to="{name: 'personalCalendar'}">こちら</router-link>からマイページへアクセスしてください。</p>
                </template>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import {SUCCESS} from "../../util";
    import validationRulesMixin from "../../mixins/validationRulesMixin"
    import utilDataMixin from "../../mixins/utilDataMixin"

    export default {
        name: "ResetPassword",
        mixins: [validationRulesMixin, utilDataMixin],
        data() {
            return {
                form:{
                    password: '',
                    // パスワード変更用のトークンとメールアドレスをクエリパラメータから取得
                    token: this.$route.query.token,
                    email: this.$route.query.email
                },
                isUpdated: false,
                showPassword: false
            }
        },
        methods: {
            /**
             * パスワード変更処理
             * @returns {Promise<boolean>}
             */
            async resetPassword(){
                // バリデーションチェック
                if (!this.$refs.form.validate()){
                    return false
                }
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.post('/api/password/reset', this.form)

                if (response.status === SUCCESS) {
                    // 変更処理が成功した場合ログインされるためユーザー情報を取得する
                    await this.$store.dispatch('user/getLoginUser')
                    this.isUpdated = true
                    this.$store.commit('loading/setLoadingFlg', false)
                    return false
                }
                this.$store.commit('loading/setLoadingFlg', false)

                this.$store.commit('error/setCode', response.status)
            }
        }
    }
</script>

<style scoped>
</style>
