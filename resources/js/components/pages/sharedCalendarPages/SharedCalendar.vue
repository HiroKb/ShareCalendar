<template>
    <v-container class="content-wrap py-12">
        <v-row class="fill-height">
            <!--            カレンダー-->
            <v-col cols="7">
                <calendar
                    :selected-month="selectedMonth"
                    :selected-date-label="selectedDateLabel"
                    :weeks="weeksNum"
                    :calendar-data="calendarData"
                    @changeSelectedMonthRequest="changeSelectedMonth"
                    @changeSelectedDateRequest="changeSelectedDateLabel"
                />
            </v-col>
            <!--            カレンダーメニュー-->
            <v-col cols="5" class="fill-height">
                <v-card class="fill-height d-flex flex-column" style="overflow: hidden">
                    <div class="d-flex justify-space-between align-center">
                        <v-card-title class="flex-grow-0 flex-shrink-0">{{ selectedDateLabel}}</v-card-title>
                        <v-btn
                            class="mr-4" small fab :color="mixinThemeColor" outlined
                            @click="createScheduleModal = true"
                        >
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </div>
                    <v-divider class="mx-4"></v-divider>
                    <v-card-text
                        style="overflow-y: scroll;"
                        class="flex-grow-1 flex-shrink-1 custom-scrollbar pt-0"
                    >
                        <schedule-list
                            :schedules="selectDateSchedules"
                            :is-personal="false"
                            @showEditModalRequest="showEditModal"
                            @showDeleteModalRequest="showDeleteModal"
                        >
                        </schedule-list>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!--        スケジュール作成モーダル-->
        <v-dialog
            v-model="createScheduleModal"
            max-width="500px"
        >
            <create-schedule-form-card
                ref="createForm"
                :selected-date-label="selectedDateLabel"
                :error-messages="createError"
                @createScheduleRequest="createSchedule"
            >
            </create-schedule-form-card>
        </v-dialog>

        <!--        スケジュール更新モーダル-->
        <v-dialog
            v-model="editScheduleModal"
            max-width="500px"
        >
            <edit-schedule-form-card
                ref="editForm"
                :schedule-data="scheduleDataToBeUpdated"
                :error-messages="editError"
                @updateScheduleRequest="updateSchedule"
            >
            </edit-schedule-form-card>
        </v-dialog>

        <!--        スケジュール削除モーダル-->
        <v-dialog
            v-model="deleteScheduleModal"
            max-width="500px"
        >
            <delete-schedule-form-card
                ref="deleteForm"
                :schedule-data="scheduleDataToBeDeleted"
                @deleteScheduleRequest="deleteSchedule"
            ></delete-schedule-form-card>
        </v-dialog>
    </v-container>
</template>

<script>
    import Calendar from "../../modules/Calendar"
    import CreateScheduleFormCard from "../../modules/CreateScheduleFormCard"
    import EditScheduleFormCard from "../../modules/EditScheduleFormCard"
    import DeleteScheduleFormCard from "../../modules/DeleteScheduleFormCard"
    import ScheduleList from "../../modules/ScheduleList"

    import colorsMixin from "../../../mixins/colorsMixin"
    import schedulesAndCalendarMethodsMixin from "../../../mixins/schedulesAndCalendarMethodsMixin"

    import {CREATED, SUCCESS, VALIDATION_ERROR} from "../../../util";
    export default {
        name: "SharedCalendar",
        mixins: [
            colorsMixin,
            schedulesAndCalendarMethodsMixin
        ],
        components:{
            Calendar,
            ScheduleList,
            CreateScheduleFormCard,
            EditScheduleFormCard,
            DeleteScheduleFormCard
        },
        data () {
            return {
                calendarData: [], //カレンダーデータ配列
                selectedMonth: {}, // 選択中の月(momentオブジェクト)
                selectedMonthLabel: '', // 選択中の年月(YYYY年MM月)
                selectedDateLabel: '', // 選択中の日付(YYYY-MM-DD)
                weeksNum: 0, // 選択月が跨ぐ週数
                createScheduleModal: false, // スケジュール作成モーダルの表示フラグ
                editScheduleModal: false, // スケジュール更新モーダルの表示フラグ
                deleteScheduleModal: false, // スケジュール削除モーダルの表示フラグ
                scheduleDataToBeUpdated: {}, // 更新するスケジュールデータ
                scheduleDataToBeDeleted: {}, // 削除するスケジュールデータ
                createError: {}, // スケジュール作成APIバリデーションエラー
                editError: {}, // スケジュール更新APIバリデーションエラー
            }
        },
        computed: {
            /**
             * 選択日のスケジュールデータ
             * @return {Array}
             */
            selectDateSchedules: function () {
                for (let i = 0; i < this.calendarData.length; i++){
                    if (this.selectedDateLabel === this.calendarData[i].date){
                        return this.calendarData[i].schedules
                    }
                }
            },
        },
        props: {
            // 共有カレンダーID
            sharedCalendarId: {
                type: String,
                required: true,
                default: ''
            },
            // 共有スケジュールデータ
            sharedSchedulesData: {
                type: Object,
                required: true,
                default: () => ({
                    schedulesYear: null,
                    schedules: [],
                })
            },
            // 親コンポーネントのスケジュール取得メソッド
            fetchSharedSchedules: {
                type: Function,
                required: true,
            }
        },
        methods: {
            /**
             * 選択月の変更
             * @param {Number} num
             */
            changeSelectedMonth(num) {
                if (num === -1) {
                    this.selectedMonth = moment(this.selectedMonth).subtract(1, 'month')
                } else if (num === 1) {
                    this.selectedMonth = moment(this.selectedMonth).add(1, 'month')
                }
            },
            /**
             * 選択日の変更
             * @param {String} date 'YYYY-MM-DD'
             */
            changeSelectedDateLabel(date) {
                this.selectedDateLabel = date
            },
            /**
             * カレンダー関連のデータを更新
             */
            changeCalendarRelatedData() {
                const newData = this.mixinGenerateCalendarRelatedData(this.selectedMonth, this.sharedSchedulesData.schedules)

                this.selectedDateLabel = newData.selectedDateLabel
                this.weeksNum = newData.weeksNum
                this.calendarData = newData.calendarData
            },
            /**
             * スケジュールの作成
             * @param {Object}schedule 作成するスケジュール
             * @return {Promise<boolean>}
             */
            async createSchedule (schedule){
                this.createError = {}

                // 共有スケジュール作成APIに作成するスケジュールデータとともにpostリクエスト
                // レスポンス待ちの間ローディング画面を表示
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.post('/api/shared-calendars/' + this.sharedCalendarId + '/schedules', schedule)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === CREATED) {
                    // カレンダーデータとスケジュールリストデータを更新
                    const newData = this.mixinAddScheduleToSchedulesListAndCalendarData(response.data, this.sharedSchedulesData.schedules, this.calendarData)
                    this.$emit('changeSchedulesData',{schedules: newData.newScheduleList})
                    this.calendarData = newData.newCalendarData

                    // モーダルを非表示にしフラッシュメッセージを表示
                    this.createScheduleModal = false
                    this.$store.commit('flashMessage/setMessage', '共有スケジュールを追加しました')
                    return false
                }

                if (response.status === VALIDATION_ERROR) {
                    this.createError.errors = response.data.errors
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * スケジュールの更新
             * @param {Object} schedule 更新するスケジュール
             * @return {Promise<boolean>}
             */
            async updateSchedule(schedule){
                this.editError = {}

                // スケジュールが選択されていない場合
                if (!this.scheduleDataToBeUpdated.id) {
                    this.$store.commit('flashMessage/setMessage', 'スケジュールを選択してください')
                    return false
                }

                // 共有スケジュール更新APIに更新するスケジュールデータとともにpatchリクエスト
                // レスポンス待ちの間ローディング画面を表示
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.patch('/api/shared-calendars/' + this.sharedCalendarId + '/schedules/' + this.scheduleDataToBeUpdated.id, schedule)
                this.$store.commit('loading/setLoadingFlg', false)

                if(response.status === SUCCESS) {
                    // スケジュールリストとカレンダーデータのスケジュールを更新
                    const newData = this.mixinUpdateScheduleInSchedulesListAndCalendarData(this.scheduleDataToBeUpdated, response.data, this.sharedSchedulesData.schedules, this.calendarData)
                    this.$emit('changeSchedulesData',{schedules: newData.newScheduleList})
                    this.calendarData = newData.newCalendarData

                    // モーダルを非表示にしフラッシュメッセージを表示
                    this.editScheduleModal = false
                    this.$store.commit('flashMessage/setMessage', '共有スケジュールを更新しました')
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * スケジュール削除
             * @return {Promise<boolean>}
             */
            async deleteSchedule(){
                // スケジュールが選択されていない場合
                if (!this.scheduleDataToBeDeleted.id){
                    this.$store.commit('flashMessage/setMessage', 'スケジュールを選択してください')
                    return false
                }

                // ルートパラメータに削除するスケジュールIDを加えスケジュール削除APIにdeleteリクエスト
                // レスポンス待ちの間ローディング画面を表示
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarId + '/schedules/' + this.scheduleDataToBeDeleted.id)
                this.$store.commit('loading/setLoadingFlg', false)

                if(response.status === SUCCESS) {
                    // カレンダーデータとスケジュールリストデータからスケジュールを削除
                    const newData = this.mixinRemoveScheduleFromSchedulesListAndCalendarData(this.scheduleDataToBeDeleted, this.sharedSchedulesData.schedules, this.calendarData)
                    this.$emit('changeSchedulesData',{schedules: newData.newScheduleList})
                    this.calendarData = newData.newCalendarData

                    // モーダルを非表示にしフラッシュメッセージを表示
                    this.deleteScheduleModal = false
                    this.$store.commit('flashMessage/setMessage', '共有スケジュールを削除しました')
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * スケジュール更新モーダルの表示処理
             * @param {Object} schedule 更新するスケジュールデータ
             */
            showEditModal(schedule){
                this.scheduleDataToBeUpdated = schedule
                this.editScheduleModal = true
            },
            /**
             * スケジュール削除モーダルの表示処理
             * @param {Object} schedule 削除するスケジュールデータ
             */
            showDeleteModal(schedule) {
                this.scheduleDataToBeDeleted = schedule
                this.deleteScheduleModal = true
            },
        },
        created() {
            this.selectedMonth = moment()
            this.selectedDateLabel = moment(this.selectedMonth).format('YYYY-MM-DD')
        },
        watch: {
            /**
             * スケジュールデータが更新された場合カレンダーデータも更新
             */
            sharedSchedulesData:{
                handler: function (val) {
                    this.changeCalendarRelatedData()
                },
                deep: true
            },
            selectedMonth: async function () {
                const selectedYear = moment(this.selectedMonth).format('YYYY')
                this.changeCalendarRelatedData()

                if(this.sharedSchedulesData.schedulesYear !== selectedYear){
                    // 親のスケジュール取得メソッドを呼ぶ
                    this.fetchSharedSchedules(selectedYear, true)
                }
            },
            /**
             *  モーダルが非表示になった場合モーダル内のフォームをリセット
             * @param {Boolean} val モーダルの表示フラグ
             */
            createScheduleModal: function (val) {
                !val && this.$refs.createForm.resetForm()
            },
            editScheduleModal: function (val) {
                if (!val) {
                    this.scheduleDataToBeUpdated = {}
                    this.$refs.editForm.resetForm()
                }
            }
        }
    }
</script>

<style scoped>
    .content-wrap{
        height: calc(100vh - 64px);
    }
</style>
