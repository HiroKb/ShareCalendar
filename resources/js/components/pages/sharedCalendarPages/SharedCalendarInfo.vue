<template>
    <v-container class="py-12">
        <!--        共有カレンダー情報-->
        <v-card>
            <v-card-title>カレンダー情報</v-card-title>
            <v-card-text>
                <div class="pb-4 d-flex align-end justify-space-between">
                    <div>
                        <p class="info-label">共有カレンダー名</p>
                        <p class="info-data">{{ sharedCalendarData.calendar_name }}</p>
                    </div>
                    <v-btn
                        :color="mixinThemeColor" dark
                        @click.stop="editCalendarNameModal.show = !editCalendarNameModal.show"
                    >変更</v-btn>
                </div>
                <v-divider></v-divider>
                <div class="pt-4 d-flex align-center justify-space-between">
                    <div>
                        <p class="info-label">検索ID</p>
                        <p class="info-data">{{ sharedCalendarData.search_id }}</p>
                        <p class="info-label mt-4">招待URL</p>
                        <p class="info-data">{{ invitationUrl }}</p>
                    </div>
                    <v-btn
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
                        <v-btn block :color="mixinThemeColor" dark type="submit">変更</v-btn>
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
                        <v-btn block :color="mixinThemeColor" dark type="submit">変更</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
    import {SUCCESS, VALIDATION_ERROR} from "../../../util"
    import colorsMixin from "../../../mixins/colorsMixin"
    import validationRulesMixin from "../../../mixins/validationRulesMixin"
    export default {
        name: "SharedCalendarInfo.vue",
        mixins: [validationRulesMixin, colorsMixin],
        data () {
            return {
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
        },
        watch: {
            'editCalendarNameModal.show': function (val) {
                if (!val) {
                    this.editCalendarNameModal.form.calendar_name = ''
                    this.$refs.editCalendarNameForm.resetValidation()
                }
            }
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
