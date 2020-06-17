<template>
    <v-container class="container-mt medium-container">
        <!--        共有カレンダー情報-->
        <v-card>
            <v-card-title>カレンダー情報</v-card-title>
            <v-card-text>
                <div class="py-4 d-flex flex-column align-center">
                    <v-img
                        :src="imageUrl"
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
                <div class="py-4 d-flex align-center justify-space-between">
                    <div class="mr-4">
                        <p class="info-label">共有カレンダー名</p>
                        <p class="info-data">{{ sharedCalendarData.calendar_name }}</p>
                    </div>
                    <v-btn
                        class="font-weight-bold"
                        :color="mixinThemeColor" dark
                        @click.stop="editCalendarNameModal.show = !editCalendarNameModal.show"
                    >変更</v-btn>
                </div>
                <v-divider></v-divider>
                <div class="pt-4 d-flex align-center justify-space-between ">
                    <div class="mr-4">
                        <p class="info-label">検索ID</p>
                        <p class="info-data">{{ sharedCalendarData.search_id }}</p>
                        <p class="info-label mt-4">招待URL</p>
                        <p class="info-data">{{ invitationUrl }}</p>
                    </div>
                    <v-btn
                        class="font-weight-bold"
                        :color="mixinThemeColor" dark
                        @click.stop="updateSearchIdModal.show = !updateSearchIdModal.show"
                    >変更</v-btn>
                </div>
            </v-card-text>
        </v-card>

<!--        カレンダー名変更モーダル-->
        <v-dialog
            v-model="editCalendarNameModal.show"
            max-width="500px"
        >
            <v-card>
                <v-card-title>カレンダー名変更</v-card-title>
                <v-card-text>
                    <v-form ref="editCalendarNameForm" @submit.prevent="updateCalendarName">
                        <v-text-field
                            v-model="editCalendarNameModal.form.calendar_name"
                            label="新しいカレンダー名"
                            :rules="[mixinValidationRules.required, mixinValidationRules.max100]"
                            :error="editCalendarNameModal.errors ? !!editCalendarNameModal.errors.calendar_name : false"
                            :error-messages="editCalendarNameModal.errors ? editCalendarNameModal.errors.calendar_name ? editCalendarNameModal.errors.calendar_name : [] : []"
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

        <!--        カレンダー画像変更モーダル-->
        <v-dialog
            v-model="editImageModal.show"
            max-width="500px"
        >
            <v-card>
                <v-card-title>カレンダー画像変更</v-card-title>
                <v-card-text>
                    <v-form
                        ref="editImageForm" @submit.prevent="updateImage"
                    >
                        <div class="mb-1 d-flex justify-center">
                            <v-img
                                class="d-block"
                                :src="previewImageUrl"
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
                            :error="editImageModal.errors ? !!editImageModal.errors.image : false"
                            :error-messages="editImageModal.errors ? editImageModal.errors.image ? editImageModal.errors.image : [] : []"
                            show-size
                            prepend-icon="mdi-image"
                            @change="onFileChange"
                        ></v-file-input>
                        <v-btn
                            class="mt-3 font-weight-bold"
                            block :color="mixinThemeColor" dark
                            type="submit"
                        >変更</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

        <!--        検索ID変更モーダル-->
        <v-dialog
            v-model="updateSearchIdModal.show"
            max-width="500px"
        >
            <v-card>
                <v-card-title>カレンダー検索ID変更</v-card-title>
                <v-card-text>
                    <p>検索IDと招待URLがランダムに変更され、<br>現在の検索ID、招待URLは使用できなくなります。<p/>
                    <v-form @submit.prevent="updateSearchId">
                        <v-btn
                            class="font-weight-bold"
                            block :color="mixinThemeColor" dark
                            type="submit"
                        >変更</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
    import {CREATED, SUCCESS, VALIDATION_ERROR} from "../../../util"
    import utilDataMixin from "../../../mixins/utilDataMixin"
    import validationRulesMixin from "../../../mixins/validationRulesMixin"
    export default {
        name: "SharedCalendarInfo.vue",
        mixins: [validationRulesMixin, utilDataMixin],
        data () {
            return {
                editImageModal: {
                    show: false,
                    previewImg: '',
                    form: {
                        image: null
                    },
                    errors: {}
                },
                editCalendarNameModal: {
                    show: false,
                    form: {
                        calendar_name: ''
                    },
                    errors: {}
                } ,
                updateSearchIdModal: {
                    show: false
                }
            }
        },
        props: {
            sharedCalendarData: {
                type: Object,
                required: true,
                default: {}
            }
        },
        computed: {
            /**
             * @return {String} 招待URL
             */
            invitationUrl: function () {
                return this.sharedCalendarData.search_id ?
                    location.protocol + '//' + location.host + '/personal/shared-calendar/' + this.sharedCalendarData.search_id + '/application'
                    : ''
            },
            // カレンダー画像URL
            imageUrl: function () {
                if (this.sharedCalendarData.image_url){
                    return  this.sharedCalendarData.image_url
                }else{
                    return this.mixinNoImagePath
                }
            },
            // プレビュー画像URL
            previewImageUrl: function () {
                if (this.editImageModal.previewImg.length){
                    return this.editImageModal.previewImg
                } else {
                    return this.imageUrl
                }
            }
        },
        methods: {
            /**
             * カレンダー名変更処理
             * @return {Promise<boolean>}
             */
            async updateCalendarName() {
                if (!this.$refs.editCalendarNameForm.validate()) {
                    return false
                }

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.patch('/api/shared-calendars/' + this.sharedCalendarData.id + '/name', this.editCalendarNameModal.form)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    this.$emit('changeCalendarData', response.data)
                    this.editCalendarNameModal.show = false
                    this.$store.commit('flashMessage/setMessage', '共有カレンダー名を変更しました')
                    return false
                }

                if (response.status === VALIDATION_ERROR) {
                    this.editCalendarNameModal.errors = response.data.errors
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * カレンダー画像更新処理
             */
            async updateImage() {
                if (!this.$refs.editImageForm.validate()) {
                    return false
                }

                const formData = new FormData()
                formData.append('image', this.editImageModal.form.image)
                this.$store.commit('loading/setLoadingFlg', true)

                const response = await axios.post('/api/shared-calendars/' + this.sharedCalendarData.id + '/image', formData)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === CREATED) {
                    this.$emit('changeCalendarData', response.data)
                    this.editImageModal.show = false
                    this.$store.commit('flashMessage/setMessage', '共有カレンダー画像を変更しました')
                    return false
                }

                if (response.status === VALIDATION_ERROR) {
                    this.editCalendarNameModal.errors = response.data.errors
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * 検索ID変更処理
             * @return {Promise<boolean>}
             */
            async updateSearchId(){
                if (!this.sharedCalendarData.id) {
                    return false
                }

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.patch('/api/shared-calendars/' + this.sharedCalendarData.id + '/search-id')
                this.$store.commit('loading/setLoadingFlg', false)

                if(response.status === SUCCESS) {
                    this.$emit('changeCalendarData', response.data)
                    this.updateSearchIdModal.show = false
                    this.$store.commit('flashMessage/setMessage', 'カレンダー検索ID/招待URLを変更しました')
                    return false
                }

                this.$store.commit('error/setCode', response.status)
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
            'editCalendarNameModal.show': function (val) {
                if (!val) {
                    this.editCalendarNameModal.form.calendar_name = ''
                    this.$refs.editCalendarNameForm.resetValidation()
                }
            },
            'editImageModal.show': function (val) {
                if (val === false) {
                    this.editImageModal.previewImg = ''
                    this.editImageModal.form.image = null
                    this.$refs.editImageForm.resetValidation()
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
