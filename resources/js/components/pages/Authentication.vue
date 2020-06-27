<template>
    <v-container class="container-mt medium-container">
        <v-tabs
            fixed-tabs
            class="elevation-1"
            v-model="tab"
        >
            <v-tab href="#login-tab">ログイン</v-tab>
            <v-tab href="#register-tab">新規登録</v-tab>
            <v-tabs-slider :color="mixinThemeColor"></v-tabs-slider>

            <v-tabs-items v-model="tab">
                <v-divider></v-divider>
                <v-tab-item value="login-tab">
                    <v-card flat tile>
                        <v-card-text>
                            <div class="d-block d-sm-flex justify-space-between ">
                                <v-btn
                                    href="/o-auth/google"
                                    dark color="#ea4335"
                                    class="font-weight-bold social-button"
                                    style="text-transform: none"
                                >
                                    <v-icon class="mr-2">mdi-google</v-icon>Googleでログイン
                                </v-btn>
                                <v-btn
                                    href="/o-auth/twitter"
                                    dark color="#1da1f2"
                                    class="font-weight-bold social-button mt-4 mt-sm-0"
                                    style="text-transform: none"
                                >
                                    <v-icon class="mr-2">mdi-twitter</v-icon>Twitterでログイン
                                </v-btn>
                            </div>
                            <v-btn
                                href="/o-auth/github"
                                dark color="#333"
                                class="font-weight-bold social-button my-4"
                                style="text-transform: none"
                            >
                                <v-icon class="mr-2">mdi-github</v-icon>GitHubでログイン
                            </v-btn>
                            <v-divider></v-divider>
                            <v-form
                                class="mt-4"
                                ref="loginForm"
                                @submit.prevent="login"
                            >
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
                                <v-btn class="font-weight-bold"
                                       block  :color="mixinThemeColor" dark
                                       type="submit">ログイン</v-btn>
                            </v-form>
                            <p class="mt-4">登録又はログインを行うことで、<br><router-link :to="{name: 'termsOfUse'}">利用規約</router-link>と<router-link :to="{name: 'privacyPolicy'}">プライバシーポリシー</router-link>に同意したものとみなします。</p>
                            <router-link
                                :to="{name: 'sendResetPasswordLink'}"
                                class="d-inline-block mb-4"
                            >パスワードをお忘れの方</router-link>
                            <v-divider></v-divider>
                            <v-btn
                                class="font-weight-bold my-4"
                                block  :color="mixinThemeColor" dark
                                @click.prevent="testUserLogin"
                            >テストユーザーでログイン</v-btn>
                            <p>テストユーザーは誰でもログイン・操作できるアカウントです。<br>個人情報や他の利用者が不快に感じる内容等を投稿しないでください。</p>
                            <p>テストユーザーはアカウントや投稿したスケジュール、カレンダー等が削除できないように制限されています。</p>
                        </v-card-text>
                    </v-card>
                </v-tab-item>

                <v-tab-item value="register-tab">
                    <v-card flat tile>
                        <v-card-text>
                            <div class="d-block d-sm-flex justify-space-between">
                                <v-btn
                                    href="/o-auth/google"
                                    dark color="#ea4335"
                                    class="font-weight-bold social-button"
                                    style="text-transform: none"
                                >
                                    <v-icon class="mr-2">mdi-google</v-icon>Googleで登録
                                </v-btn>
                                <v-btn
                                    href="/o-auth/twitter"
                                    dark color="#1da1f2"
                                    class="font-weight-bold social-button mt-4 mt-sm-0"
                                    style="text-transform: none"
                                >
                                    <v-icon class="mr-2">mdi-twitter</v-icon>Twitterで登録
                                </v-btn>
                            </div>
                            <v-btn
                                href="/o-auth/github"
                                dark color="#333"
                                class="font-weight-bold social-button my-4"
                                style="text-transform: none"
                            >
                                <v-icon class="mr-2">mdi-github</v-icon>GitHubで登録
                            </v-btn>
                            <v-divider></v-divider>
                            <v-form
                                class="mt-4"
                                ref="registerForm"
                                @submit.prevent="register"
                            >
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
                                <v-btn
                                    class="font-weight-bold"
                                    block :color="mixinThemeColor" dark
                                    type="submit"
                                >登録</v-btn>
                            </v-form>
                            <p class="mt-4">登録又はログインを行うことで、<br><router-link :to="{name: 'termsOfUse'}">利用規約</router-link>と<router-link :to="{name: 'privacyPolicy'}">プライバシーポリシー</router-link>に同意したものとみなします。</p>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs-items>
        </v-tabs>
    </v-container>

</template>

<script>
    import validationRulesMixin from "../../mixins/validationRulesMixin"
    import utilDataMixin from "../../mixins/utilDataMixin"
    import {mapState} from 'vuex'
    export default {
        name: "Authentication",
        mixins: [validationRulesMixin, utilDataMixin],
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
                },
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
                    this.$router.push({name: 'emailVerification'})
                }
            },
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
                    this.$router.push({name: 'personalCalendar'})
                }
            },
            async testUserLogin(){
                await this.$store.dispatch('user/login', null)
                if (this.authApiStatus) {
                    this.$router.push({name: 'personalCalendar'})
                }
            },
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
    .social-button{
        width: 48%;
    }
    @media screen and (max-width: 599px){
        .social-button{
            width: 100%;
        }
    }
</style>
