<template>
    <v-container class="container-mt medium-container">
<!--        アカウント情報-->
        <v-card>
            <v-card-text class="pt-4">
                <div class="py-4 d-flex flex-column align-center">
                    <v-img
                        :src="userImage"
                        height="200px"
                        width="200px"
                        aspect-ratio="1"
                        style="border-radius: 50%"
                    ></v-img>
                    <v-btn
                        class="mt-4 font-weight-bold"
                        :color="mixinThemeColor" dark
                        @click.stop="editImageModal.show = !editImageModal.show"
                    >変更</v-btn>
                </div>
                <v-divider></v-divider>
                <div class="py-4 d-flex align-end justify-space-between">
                    <div class="mr-4">
                        <p class="info-label">ユーザー名</p>
                        <p class="info-data">{{ userName }}</p>
                    </div>
                    <v-btn
                        class="font-weight-bold"
                        :color="mixinThemeColor" dark
                        @click.stop="editNameModal.show = !editNameModal.show"
                    >変更</v-btn>
                </div>
                <template v-if="!isOAuth">
                    <v-divider></v-divider>
                    <div class="py-4 d-flex align-center justify-space-between">
                        <div class="mr-4">
                            <p class="info-label">メールアドレス</p>
                            <p class="info-data">{{ userEmail }}</p>
                        </div>
                        <v-btn
                            class="font-weight-bold"
                            :color="mixinThemeColor" dark
                            @click.stop="editEmailModal.show = !editEmailModal.show"
                        >変更</v-btn>
                    </div>
                    <v-divider></v-divider>
                    <div
                        class="pt-4 mb-8 d-flex align-end justify-space-between"
                    >
                        <div>
                            <p class="info-label">パスワード</p>
                            <p class="info-data">********</p>
                        </div>
                        <v-btn
                            class="font-weight-bold"
                            :color="mixinThemeColor" dark
                            @click.stop="editPasswordModal.show = !editPasswordModal.show"
                        >変更</v-btn>
                    </div>
                </template>
                <div class="text-left pt-4">
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
                        <v-btn
                            class="font-weight-bold"
                            block :color="mixinThemeColor"
                            dark type="submit"
                        >変更</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

<!--        ユーザー画像変更モーダル-->
        <v-dialog
            v-model="editImageModal.show"
            max-width="500px"
        >
            <v-card>
                <v-card-title>ユーザー画像変更</v-card-title>
                <v-card-text>
                    <v-form
                        ref="editImageForm" @submit.prevent="updateImage"
                    >
                        <div class="mb-1 d-flex justify-center">
                                <v-img
                                    class="d-block"
                                    :src="previewImageURL"
                                    height="200px"
                                    max-width="200px"
                                    aspect-ratio="1"
                                    style="border-radius: 50%"
                                ></v-img>
                        </div>
                        <v-file-input
                            v-model="editImageModal.form.image"
                            accept="image/jpeg, image/jpg, image/png"
                            :rules="[mixinValidationRules.required, mixinValidationRules.image]"
                            :error="errorMessages ? !!errorMessages.image : false"
                            :error-messages="errorMessages ? errorMessages.image ? errorMessages.image : [] : []"
                            show-size
                            prepend-icon="mdi-image"
                            @change="onFileChange"
                        ></v-file-input>
                        <v-btn
                            class="mt-3 font-weight-bold"
                            block :color="mixinThemeColor"
                            dark type="submit"
                        >変更</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>


<!--        メールアドレス変更モーダル-->
        <v-dialog
            v-model="editEmailModal.show"
            max-width="500px"
            v-if="!isOAuth"
        >
            <v-card>
                <v-card-title>メールアドレス変更</v-card-title>
                <v-card-text v-if="!editEmailModal.isDoneRequest">
                    <v-form ref="editEmailForm" @submit.prevent="updateEmail">

                        <v-text-field
                            v-model="editEmailModal.form.new_email"
                            label="新しいメールアドレス"
                            :rules="[mixinValidationRules.required, mixinValidationRules.email, mixinValidationRules.max255]"
                            :error="errorMessages ? !!errorMessages.new_email : false"
                            :error-messages="errorMessages ? errorMessages.new_email ? errorMessages.new_email : [] : []"
                            outlined
                        ></v-text-field>
                        <v-btn
                            class="font-weight-bold"
                            block :color="mixinThemeColor"
                            dark type="submit"
                        >変更</v-btn>
                    </v-form>
                </v-card-text>
                <v-card-text v-else>
                    <p>メールアドレス確認用のメールを送信しました。</p>
                    <p>メール内のボタンをクリックして確認を完了してください。</p>
                </v-card-text>
            </v-card>
        </v-dialog>

<!--        パスワード変更モーダル-->
        <v-dialog
            v-model="editPasswordModal.show"
            max-width="500px"
            v-if="!isOAuth"
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
                        <v-btn
                            class="font-weight-bold"
                            block :color="mixinThemeColor"
                            dark type="submit"
                        >変更</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
    import {mapGetters, mapMutations, mapState} from "vuex";
    import utilDataMixin from "../../../mixins/utilDataMixin"
    import validationRulesMixin from "../../../mixins/validationRulesMixin";
    export default {
        name: "UserInfo",
        mixins: [validationRulesMixin, utilDataMixin],
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
                editImageModal: {
                    show: false,
                    previewImg: '',
                    form: {
                        image: null
                    }
                },
                editEmailModal: {
                    show: false,
                    form: {
                        new_email: '',
                    },
                    isDoneRequest: false
                },
                editPasswordModal: {
                    show: false,
                    form: {
                        current_password: '',
                        new_password: '',
                    },
                    showCurrentPassword: false,
                    showNewPassword: false
                },
                registrationPasswordModal: {
                    show: false,
                    form: {
                        password: ''
                    },
                    showPassword: false
                }
            }
        },
        computed : {
            ...mapGetters({
                userName: 'user/userName',
                userImage: 'user/userImage',
                userEmail: 'user/userEmail',
                isOAuth: 'user/isOAuth',
            }),
            ...mapState({
                apiStatus: state => state.user.apiStatus, // API通信成否
                errorMessages: state => state.user.errorMessages // バリデーションエラーメッセージ
            }),
            // 画像更新モーダルプレビュー表示用URL
            previewImageURL: function () {
                if (this.editImageModal.previewImg.length){
                    return this.editImageModal.previewImg
                }else {
                    return this.userImage
                }
            }
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
             * ユーザー画像変更処理
             */
            async updateImage(){
                if (!this.$refs.editImageForm.validate()){
                    return false
                }

                const formData = new FormData()
                formData.append('image', this.editImageModal.form.image)
                await this.$store.dispatch('user/updateImage', formData)

                if (this.apiStatus) {
                    this.editImageModal.show = false
                    this.$store.commit('flashMessage/setMessage', 'ユーザー画像を変更しました。')
                }
            },
            /**
             * メールアドレス変更リクエスト
             * @return {Promise<boolean>}
             */
            async updateEmail () {
                if (!this.$refs.editEmailForm.validate()){
                    return false
                }
                await this.$store.dispatch('user/updateEmail', this.editEmailModal.form)

                if (this.apiStatus) {
                    this.editEmailModal.isDoneRequest = true
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
            /**
             * 画像ファイルプレビュー
             */
            onFileChange(img){
                if (typeof img === 'undefined' || !img){
                    this.editImageModal.previewImg = ''
                    return false
                }
                if (!img.type.match('image.*')){
                    return false
                }

                const fileReader = new FileReader()

                fileReader.onload = e => {
                    this.editImageModal.previewImg = e.target.result
                }

                fileReader.readAsDataURL(img)
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
            'editImageModal.show': function (val) {
                if (val === false) {
                    this.editImageModal.previewImg = ''
                    this.editImageModal.form.image = null
                    this.$refs.editImageForm.resetValidation()
                    this.setErrorMessages(null)
                }
            },
            'editEmailModal.show': function (val) {
                if (val === false) {
                    this.editEmailModal.form.new_email = ''
                    this.editEmailModal.isDoneRequest = false
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
            'registrationPasswordModal.show': function (val) {
                if (val === false) {
                    this.registrationPasswordModal.form.password = ''
                    this.registrationPasswordModal.showPassword = false
                    this.$refs.registrationPasswordForm.resetValidation()
                    this.setErrorMessages(null)
                }
            },
        }
    }
</script>

<style scoped>
    .info-label{
        margin-bottom: 0;
    }
    .info-data{
        margin-bottom: 0;
        font-size: 20px;
        word-break: break-all;
        color: rgba(0, 0, 0 ,0.87);
    }
</style>
