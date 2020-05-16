<template>
    <v-container class="mt-12 elevation-1">

        <v-card flat tile>
            <v-card-text>
                <v-form ref="form" @submit.prevent="resetPassword" v-if="!responseFlg">

                    <v-text-field
                        outlined
                        v-model="form.password"
                        label="新しいパスワード"
                        :rules="[validationRules.required, validationRules.min8]"
                    >
                    </v-text-field>
                    <v-btn block :color="colors.themeColor" dark type="submit">変更</v-btn>
                </v-form>
                <template v-else>
                    <p>パスワードを変更しました。<br/><router-link :to="{name: 'myCalendar'}">こちら</router-link>からマイページへアクセスしてください。</p>
                </template>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import {SUCCESS} from "../../util";
    import validationRulesMixin from "../../mixins/validationRulesMixin"
    import colorsMixin from "../../mixins/colorsMixin"

    export default {
        name: "ResetPassword",
        mixins: [validationRulesMixin, colorsMixin],
        data() {
            return {
                form:{
                    password: '',
                    // パスワード変更用のトークンとメールアドレスをクエリパラメータから取得
                    token: this.$route.query.token,
                    email: this.$route.query.email
                },
                responseFlg: false
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
                    this.responseFlg = true
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
    .container{
        max-width: 600px;
    }
</style>
