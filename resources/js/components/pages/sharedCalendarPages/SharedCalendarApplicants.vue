<template>
    <v-container class="fixed-content py-12">
        <v-card class="fill-height d-flex flex-column">
            <div class="flex-grow-0 flex-shrink-0 d-flex align-center">
                <v-card-title>共有申請者</v-card-title>
<!--                申請者が存在する場合一括許可拒否ボタン-->
                <div v-if="!sharedCalendarApplicants.loadingFlg && allApplicantsData.length">
                    <v-btn
                        small fab text
                        :color="mixinThemeColor"
                        @click="allowAllApplicationModal = !allowAllApplicationModal"
                    >
                        <v-icon>mdi-account-multiple-plus-outline</v-icon>
                    </v-btn>
                    <v-btn
                        small fab text
                        :color="mixinThemeColor"
                        @click="rejectAllApplicationModal = !rejectAllApplicationModal"
                    >
                        <v-icon>mdi-account-multiple-remove-outline</v-icon>
                    </v-btn>
                </div>
            </div>

            <v-card-subtitle
                v-if="!sharedCalendarApplicants.loadingFlg && !allApplicantsData.length"
            >現在共有申請者はいません。</v-card-subtitle>

            <v-card-text
                class="mb-4 flex-grow-1 flex-shrink-1 custom-scrollbar"
                style="overflow-y: scroll"
                v-if="!sharedCalendarApplicants.loadingFlg && allApplicantsData.length"
            >
<!--                申請者リスト-->
                <v-list>
                    <v-list-item
                        v-for="(applicant, index) in allApplicantsData"
                        :key="index"
                    >

                        <v-list-item-avatar>
                            <v-img :src=" applicant.image_url || mixinNoImagePath"></v-img>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title class="title">{{applicant.name}}</v-list-item-title>
                        </v-list-item-content>

                        <v-list-item-action class="d-flex flex-row">
                            <v-btn
                                small fab text
                                :color="mixinThemeColor"
                                @click="showAllowApplicationModal(applicant)"
                            >
                                <v-icon>mdi-account-plus-outline</v-icon>
                            </v-btn>
                            <v-btn
                                class="ml-2"
                                small fab text
                                :color="mixinThemeColor"
                                @click="showRejectApplicationModal(applicant)"
                            >
                                <v-icon>mdi-account-remove-outline</v-icon>
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>
            </v-card-text>
        </v-card>

<!--        申請許可モーダル-->
        <v-dialog
            v-model="allowApplicationModal"
            max-width="500px"
        >
            <v-card>
                <div
                    class="d-flex flex-column align-center pt-5"
                >
                    <v-img
                        :src="selectedApplicantImageUrl"
                        height="200px"
                        width="200px"
                        aspect-ratio="1"
                        style="border-radius: 50%"
                    ></v-img>
                    <v-card-title class="pt-2">{{ selectedApplicantName }}</v-card-title>
                </div>
                <v-card-text>
                    <v-form @submit.prevent="allowApplication([selectedApplicantData])">
                        <p class="subtitle-1">共有申請を許可しますか？</p>
                        <v-btn
                            class="font-weight-bold"
                            block :color="mixinThemeColor"
                            dark type="submit"
                        >許可</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

<!--        申請一括許可モーダル-->
        <v-dialog
            v-model="allowAllApplicationModal"
            max-width="500px"
        >
            <v-card>
                <v-card-text>
                    <p class="pt-4 subtitle-1">全ての共有申請を許可しますか？</p>
                    <v-form @submit.prevent="allowApplication(allApplicantsData)">
                        <v-btn
                            class="font-weight-bold"
                            block :color="mixinThemeColor"
                            dark type="submit"
                        >許可</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

<!--        申請拒否モーダル-->
        <v-dialog
            v-model="rejectApplicationModal"
            max-width="500px"
        >
            <v-card>
                <div
                    class="d-flex flex-column align-center pt-5"
                >
                    <v-img
                        :src="selectedApplicantImageUrl"
                        height="200px"
                        width="200px"
                        aspect-ratio="1"
                        style="border-radius: 50%"
                    ></v-img>
                    <v-card-title class="pt-2">{{ selectedApplicantName }}</v-card-title>
                </div>
                <v-card-text>
                    <v-form @submit.prevent="rejectApplication([selectedApplicantData])">
                        <p class="subtitle-1">共有申請を拒否しますか？</p>
                        <v-btn
                            class="font-weight-bold"
                            block :color="mixinThemeColor"
                            dark type="submit"
                        >拒否</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>

<!--        申請一括拒否モーダル-->
        <v-dialog
            v-model="rejectAllApplicationModal"
            max-width="500px"
        >
            <v-card>
                <v-card-text>
                    <p class="pt-4 subtitle-1">全ての共有申請を拒否しますか？</p>
                    <v-form @submit.prevent="rejectApplication(allApplicantsData)">
                        <v-btn
                            class="font-weight-bold"
                            block :color="mixinThemeColor"
                            dark type="submit"
                        >拒否</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
    import utilDataMixin from "../../../mixins/utilDataMixin"
    import {CREATED, SUCCESS} from "../../../util"
    export default {
        name: "SharedCalendarApplicants",
        mixins: [utilDataMixin],
        data () {
            return {
                selectedApplicantData: null, //選択された申請者データ
                // モーダルフラグ
                allowApplicationModal: false,
                allowAllApplicationModal: false,
                rejectApplicationModal: false,
                rejectAllApplicationModal: false,
            }
        },
        computed: {
            allApplicantsData: function () {
                return _.cloneDeep(this.sharedCalendarApplicants.data)
            },
            selectedApplicantName: function () {
                return this.selectedApplicantData ? this.selectedApplicantData.name : ''
            },
            selectedApplicantImageUrl: function () {
                return this.selectedApplicantData ? this.selectedApplicantData.image_url ? this.selectedApplicantData.image_url : this.mixinNoImagePath : ''
            }
        },
        props: {
            sharedCalendarData: {
                type: Object,
                required: true,
            },
            sharedCalendarApplicants: {
                type: Object,
                required: true,
                default: () => ({
                    data: [],
                    loadingFlg: false
                })
            },
        },
        methods: {
            /**
             * 申請許可処理
             * @param {Array} applicantsList 申請を許可するユーザーデータ配列
             * @return {Promise<boolean>}
             */
            async allowApplication(applicantsList = []) {
                if (!this.sharedCalendarData.id || !applicantsList.length) {
                    return false
                }

                const idList = applicantsList.map(function (applicant) {
                    return applicant.id
                })

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.put('/api/shared-calendars/' + this.sharedCalendarData.id + '/members', {
                    'id_list': idList
                })
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === CREATED) {

                    this.$emit('allowApplication', applicantsList)

                    if (applicantsList.length === 1) {
                        this.$store.commit('flashMessage/setMessage', applicantsList[0].name + 'さんの共有申請を許可しました。')
                    } else {
                        this.$store.commit('flashMessage/setMessage', '全ての共有申請を許可しました。')
                    }

                    this.allowApplicationModal = false
                    this.allowAllApplicationModal = false
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * 申請拒否処理
             * @param {Array} applicantsList 申請を拒否するユーザーデータ配列
             * @return {Promise<boolean>}
             */
            async rejectApplication(applicantsList = []) {
                if (!this.sharedCalendarData.id || !applicantsList.length) {
                    return false
                }

                const idList = {'id_list': applicantsList.map(function (applicant) {
                    return applicant.id
                })}

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarData.id + '/applications', {data: idList})
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {

                    this.$emit('rejectApplication', applicantsList)

                    if (applicantsList.length === 1) {
                        this.$store.commit('flashMessage/setMessage', applicantsList[0].name + 'さんの共有申請を拒否しました。')
                    } else {
                        this.$store.commit('flashMessage/setMessage', '全ての共有申請を拒否しました。')
                    }
                    this.rejectApplicationModal = false
                    this.rejectAllApplicationModal = false
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * 申請許可モーダル表示処理
             * @param {Object} applicant ユーザーデータ
             */
            showAllowApplicationModal(applicant) {
                this.selectedApplicantData = applicant
                this.allowApplicationModal = true
            },
            /**
             * 申請拒否モーダル表示処理
             * @param {Object} applicant ユーザーデータ
             */
            showRejectApplicationModal(applicant) {
                this.selectedApplicantData = applicant
                this.rejectApplicationModal = true
            },
        },
        watch:{
            /**
             * モーダルが消えた場合選択した申請者を初期化
             * @param {Boolean} val モーダル表示フラグ
             */
            'allowApplicationModal.show': function (val) {
                if (!val) {
                    this.selectedApplicantData = null
                }
            },
            'rejectApplicationModal.show': function (val) {
                if (!val) {
                    this.selectedApplicantData = null
                }
            }

        }
    }
</script>

<style scoped>
    .container{
        max-width: 600px;
    }
</style>
