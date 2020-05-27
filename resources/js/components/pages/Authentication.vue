<template>
    <v-container class="mt-12">
        <v-tabs class="elevation-1" v-model="tab">
            <v-tab href="#login-tab">ログイン</v-tab>
            <v-tab href="#register-tab">新規登録</v-tab>
            <v-tabs-slider :color="mixinThemeColor"></v-tabs-slider>

            <v-tabs-items v-model="tab">
                <v-divider ></v-divider>
                <v-tab-item value="login-tab">
                    <v-card flat tile>
                        <v-card-text>
                            <v-form ref="loginForm" @submit.prevent="login">
                                <!--                            rulesでフロントのバリデーション-->
                                <!--                            error,error-messageでバックのバリデーション結果を表示-->
                                <v-text-field
                                    outlined
                                    v-model="loginForm.data.email"
                                    label="メールアドレス"
                                    type="email"
                                    :rules="[mixinValidationRules.required]"
                                    :error="errorMessages ? !!errorMessages.email : false"
                                    :error-messages="errorMessages ? errorMessages.email ? errorMessages.email : [] : []"
                                ></v-text-field>
                                <v-text-field
                                    outlined
                                    v-model="loginForm.data.password"
                                    label="パスワード"
                                    @click:append="loginForm.showPassword = !loginForm.showPassword"
                                    :append-icon="loginForm.showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                    :type="loginForm.showPassword ? 'text' : 'password'"
                                    :rules="[mixinValidationRules.required]"
                                    :error="errorMessages ? !!errorMessages.password : false"
                                    :error-messages="errorMessages ? errorMessages.password ? errorMessages.password : [] : []"
                                ></v-text-field>
                                <v-btn class="mb-4" block  :color="mixinThemeColor" dark type="submit">ログイン</v-btn>
                                <router-link :to="{name: 'sendResetPasswordLink'}">パスワードをお忘れの方</router-link>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-tab-item>

                <v-tab-item value="register-tab">
                    <v-card flat tile>
                        <v-card-text>
                            <v-form ref="registerForm" @submit.prevent="register">
                                <v-text-field
                                    outlined
                                    v-model="registerForm.data.name"
                                    label="ユーザー名"
                                    :rules="[mixinValidationRules.required, mixinValidationRules.max255]"
                                    :error="errorMessages ? !!errorMessages.name : false"
                                    :error-messages="errorMessages ? errorMessages.name ? errorMessages.name : [] : []"
                                >
                                </v-text-field>
                                <v-text-field
                                    outlined
                                    v-model="registerForm.data.email"
                                    label="メールアドレス"
                                    :rules="[mixinValidationRules.required, mixinValidationRules.email, mixinValidationRules.max255]"
                                    :error="errorMessages ? !!errorMessages.email : false"
                                    :error-messages="errorMessages ? errorMessages.email ? errorMessages.email : [] : []"
                                ></v-text-field>
                                <v-text-field
                                    outlined
                                    v-model="registerForm.data.password"
                                    label="パスワード"
                                    @click:append="registerForm.showPassword = !registerForm.showPassword"
                                    :append-icon="registerForm.showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                    :type="registerForm.showPassword ? 'text' : 'password'"
                                    :rules="[mixinValidationRules.required, mixinValidationRules.min8]"
                                    :error="errorMessages ? !!errorMessages.password : false"
                                    :error-messages="errorMessages ? errorMessages.password ? errorMessages.password : [] : []"
                                ></v-text-field>
                                <v-btn block :color="mixinThemeColor" dark type="submit">登録</v-btn>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs-items>
        </v-tabs>
    </v-container>

</template>

<script>
    import validationRulesMixin from "../../mixins/validationRulesMixin"
    import colorsMixin from "../../mixins/colorsMixin"
    import {mapState} from 'vuex'
    export default {
        name: "Authentication",
        mixins: [validationRulesMixin, colorsMixin],
        data() {
            return {
                prevPathName: null,
                tab: 'login-tab',
                loginForm: {
                    data: {
                        email: '',
                        password: ''
                    },
                    showPassword: false
                },
                registerForm: {
                    data: {
                        name: '',
                        email: '',
                        password: ''
                    },
                    showPassword: false
                }
            }
        },
        props: {
            // 要認証ページからリダイレクトされた場合のリダイレクト元のパス(router)
            prevPath: {
                required: false,
                default: null
            }
        },
        computed: {
            ...mapState({
                authApiStatus: state => state.user.apiStatus, // API通信成否
                errorMessages: state => state.user.errorMessages // バックエンドバリデーションエラーメッセージ
            })
        },
        methods: {
            /**
             * ログイン処理
             * @returns {Promise<boolean>}
             */
            async login () {
                // バリデーションチェック
                if (!this.$refs.loginForm.validate()){
                    return false
                }

                await this.$store.dispatch('user/login', this.loginForm.data)

                // 通信成功時
                if (this.authApiStatus) {
                    if (this.prevPath === null) {
                        this.$router.push({name: 'personalCalendar'})
                    }else {
                        this.$router.push(this.prevPath)
                    }
                }
            },
            /**
             * 新規登録処理
             * @returns {Promise<void>}
             */
            async register () {
                // バリデーションチェック
                if (!this.$refs.registerForm.validate()){
                    return false
                }

                await this.$store.dispatch('user/register', this.registerForm.data)


                // 通信成功の場合
                if (this.authApiStatus) {
                    if (this.authApiStatus) {
                        if (this.prevPath === null) {
                            this.$router.push({name: 'personalCalendar'})
                        }else {
                            this.$router.push(this.prevPath)
                        }
                    }
                }
            }
        },
        created() {
            // インスタンス生成時にバックエンドバリデーションエラーメッセージを初期化
            this.$store.commit('user/setErrorMessages', null)
        },
        watch: {
            // タブが変更された場合
            tab: function (newTab, oldTab) {
                // バックエンドバリデーションエラーメッセージを初期化
                this.$store.commit('user/setErrorMessages', null)
                // フォームの初期化
                if (newTab === 'register-tab') {
                    this.loginForm.data = {
                        email: '',
                        password: ''
                    }
                    this.$refs.loginForm.resetValidation()
                    this.loginForm.showPassword = false
                }
                if (newTab === 'login-tab') {
                    this.registerForm.data = {
                        name: '',
                        email: '',
                        password: ''
                    }
                    this.$refs.registerForm.resetValidation()
                    this.registerForm.showPassword = false
                }
            }
        },
    }
</script>

<style scoped>
    .container{
        max-width: 600px;
    }
</style>
