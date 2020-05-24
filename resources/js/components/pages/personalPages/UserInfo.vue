<template>
    <v-container class="py-12">

<!--        アカウント情報-->
        <v-card>
            <v-card-text class="pt-4">
                <div class="py-4 d-flex align-end justify-space-between">
                    <div>
                        <p class="info-label">ユーザー名</p>
                        <p class="info-data">{{ userName }}</p>
                    </div>
                    <v-btn
                        :color="mixinThemeColor" dark
                        @click.stop="editNameModal.show = !editNameModal.show"
                    >変更</v-btn>
                </div>
                <v-divider></v-divider>
                <div class="py-4 d-flex align-center justify-space-between">
                    <div>
                        <p class="info-label">メールアドレス</p>
                        <p class="info-data">{{ userEmail }}</p>
                    </div>
                    <v-btn
                        :color="mixinThemeColor" dark
                        @click.stop="editEmailModal.show = !editEmailModal.show"
                    >変更</v-btn>
                </div>
                <v-divider></v-divider>
                <div class="py-4 mb-8 d-flex align-end justify-space-between">
                    <div>
                        <p class="info-label">パスワード</p>
                        <p class="info-data">********</p>
                    </div>
                    <v-btn
                        :color="mixinThemeColor" dark
                        @click.stop="editPasswordModal.show = !editPasswordModal.show"
                    >変更</v-btn>
                </div>
                <div class="text-right">
                    <router-link :to="{name: 'deleteAccount'}">アカウント削除</router-link>
                </div>
            </v-card-text>
        </v-card>

<!--        ユーザー名変更モーダル-->
        <v-dialog
            v-model="editNameModal.show"
            max-width="500px"
        >
            <v-card>
                <v-card-title>ユーザー名変更</v-card-title>
                <v-card-text>
                    <v-form ref="editNameForm" @submit.prevent="updateName">

                        <v-text-field
                            v-model="editNameModal.form.name"
                            label="新しいユーザー名"
                            :rules="[mixinValidationRules.required, mixinValidationRules.max255]"
                            :error="errorMessages ? !!errorMessages.name : false"
                            :error-messages="errorMessages ? errorMessages.name ? errorMessages.name : [] : []"
                            outlined
                        ></v-text-field>
                        <v-btn block :color="mixinThemeColor" dark type="submit">変更</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

<!--        メールアドレス変更モーダル-->
        <v-dialog
            v-model="editEmailModal.show"
            max-width="500px"
        >
            <v-card>
                <v-card-title>メールアドレス変更</v-card-title>
                <v-card-text>
                    <v-form ref="editEmailForm" @submit.prevent="updateEmail">

                        <v-text-field
                            v-model="editEmailModal.form.email"
                            label="新しいメールアドレス"
                            :rules="[mixinValidationRules.required, mixinValidationRules.email, mixinValidationRules.max255]"
                            :error="errorMessages ? !!errorMessages.email : false"
                            :error-messages="errorMessages ? errorMessages.email ? errorMessages.email : [] : []"
                            outlined
                        ></v-text-field>

                        <v-text-field
                            v-model="editEmailModal.form.password"
                            label="パスワード"
                            @click:append="editEmailModal.showPassword = !editEmailModal.showPassword"
                            :append-icon="editEmailModal.showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            :type="editEmailModal.showPassword ? 'text' : 'password'"
                            :rules="[mixinValidationRules.required, mixinValidationRules.min8]"
                            :error="errorMessages ? !!errorMessages.password : false"
                            :error-messages="errorMessages ? errorMessages.password ? errorMessages.password : [] : []"
                            outlined
                        ></v-text-field>
                        <v-btn block :color="mixinThemeColor" dark type="submit">変更</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

<!--        パスワード変更モーダル-->
        <v-dialog
            v-model="editPasswordModal.show"
            max-width="500px"
        >
            <v-card>
                <v-card-title>パスワード変更</v-card-title>
                <v-card-text>
                    <v-form ref="editPasswordForm" @submit.prevent="updatePassword">

                        <v-text-field
                            v-model="editPasswordModal.form.current_password"
                            label="現在のパスワード"
                            @click:append="editPasswordModal.showCurrentPassword = !editPasswordModal.showCurrentPassword"
                            :append-icon="editPasswordModal.showCurrentPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            :type="editPasswordModal.showCurrentPassword ? 'text' : 'password'"
                            :rules="[mixinValidationRules.required]"
                            :error="errorMessages ? !!errorMessages.current_password : false"
                            :error-messages="errorMessages ? errorMessages.current_password ? errorMessages.current_password : [] : []"
                            outlined
                        ></v-text-field>

                        <v-text-field
                            v-model="editPasswordModal.form.new_password"
                            label="新しいパスワード"
                            @click:append="editPasswordModal.showNewPassword = !editPasswordModal.showNewPassword"
                            :append-icon="editPasswordModal.showNewPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            :type="editPasswordModal.showNewPassword ? 'text' : 'password'"
                            :rules="[mixinValidationRules.required, mixinValidationRules.min8]"
                            :error="errorMessages ? !!errorMessages.new_password : false"
                            :error-messages="errorMessages ? errorMessages.new_password ? errorMessages.new_password : [] : []"
                            outlined
                        ></v-text-field>
                        <v-btn block :color="mixinThemeColor" dark type="submit">変更</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
    import {mapGetters, mapMutations, mapState} from "vuex";
    import colorsMixin from "../../../mixins/colorsMixin"
    import validationRulesMixin from "../../../mixins/validationRulesMixin";
    export default {
        name: "UserInfo",
        mixins: [validationRulesMixin, colorsMixin],
        data() {
            return {
                /**
                 * アカウント情報編集モーダル
                 * show: モーダル表示フラグ
                 * form: モーダル内フォーム
                 * show~Password: フォーム内のパスワード表示フラグ
                 */
                editNameModal: {
                    show: false,
                    form: {name: ''}
                },
                editEmailModal: {
                    show: false,
                    form: {
                        email: '',
                        password: ''
                    },
                    showPassword: false
                },
                editPasswordModal: {
                    show: false,
                    form: {
                        current_password: '',
                        new_password: '',
                    },
                    showCurrentPassword: false,
                    showNewPassword: false
                }
            }
        },
        computed : {
            ...mapGetters({
                userName: 'user/userName',
                userEmail: 'user/userEmail'
            }),
            ...mapState({
                apiStatus: state => state.user.apiStatus, // API通信成否
                errorMessages: state => state.user.errorMessages // バリデーションエラーメッセージ
            }),
        },
        methods: {
            ...mapMutations({
                setErrorMessages: 'user/setErrorMessages'
            }),
            /**
             * ユーザー名変更処理
             * @return {Promise<boolean>}
             */
            async updateName () {
                if (!this.$refs.editNameForm.validate()){
                    return false
                }

                await this.$store.dispatch('user/updateName', this.editNameModal.form)

                if (this.apiStatus) {
                    this.editNameModal.show = false
                    this.$store.commit('flashMessage/setMessage', 'ユーザー名を変更しました。')
                }
            },
            /**
             * メールアドレス変更処理
             * @return {Promise<boolean>}
             */
            async updateEmail () {
                if (!this.$refs.editEmailForm.validate()){
                    return false
                }

                await this.$store.dispatch('user/updateEmail', this.editEmailModal.form)

                if (this.apiStatus) {
                    this.editEmailModal.show = false
                    this.$store.commit('flashMessage/setMessage', 'メールアドレスを変更しました。')
                }
            },
            /**
             * パスワード変更処理
             * @return {Promise<boolean>}
             */
            async updatePassword () {
                if (!this.$refs.editPasswordForm.validate()){
                    return false
                }

                await this.$store.dispatch('user/updatePassword', this.editPasswordModal.form)

                if (this.apiStatus) {
                    this.editPasswordModal.show = false
                    this.$store.commit('flashMessage/setMessage', 'パスワードを変更しました。')
                }
            },
        },
        watch: {
            /**
             * モーダルが消えた場合モーダル内のフォームをリセット
             * @param {Boolean} val モーダル表示フラグ
             */
            'editNameModal.show': function (val) {
                if (val === false) {
                    this.editNameModal.form.name = ''
                    this.$refs.editNameForm.resetValidation()
                    this.setErrorMessages(null)
                }
            },
            'editEmailModal.show': function (val) {
                if (val === false) {
                    this.editEmailModal.form.email = ''
                    this.editEmailModal.form.password = ''
                    this.editEmailModal.showPassword = false
                    this.$refs.editEmailForm.resetValidation()
                    this.setErrorMessages(null)
                }
            },
            'editPasswordModal.show': function (val) {
                if (val === false) {
                    this.editPasswordModal.form.current_password = ''
                    this.editPasswordModal.form.new_password = ''
                    this.editPasswordModal.showCurrentPassword = false
                    this.editPasswordModal.showNewPassword = false
                    this.$refs.editPasswordForm.resetValidation()
                    this.setErrorMessages(null)
                }
            },
        }
    }
</script>

<style scoped>
    .container{
        max-width: 600px;
    }
    .info-label{
        margin-bottom: 0;
    }
    .info-data{
        margin-bottom: 0;
        font-size: 20px;
        word-wrap: break-word;
        color: rgba(0, 0, 0 ,0.87);
    }
</style>
