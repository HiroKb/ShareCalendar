<template>
    <v-container class="content-wrap py-12">
        <v-row class="fill-height">
<!--            カレンダー-->
            <v-col cols="7">
                <calendar
                    :date-label="selectedMonthLabel"
                    :weeks="weeksNum"
                    :calendar-data="calendarData"
                    :schedule-number-data="scheduleNumberData"
                    @changeSelectedMonthRequest="changeSelectedMonth"
                    @changeSelectedDateRequest="changeSelectedDate"
                />
            </v-col>
<!--            カレンダーメニュー-->
            <v-col cols="5" class="fill-height">
                <v-card class="fill-height d-flex flex-column" style="overflow: hidden">
                    <div class="d-flex justify-space-between align-center">
                        <v-card-title class="flex-grow-0 flex-shrink-0">{{ selectedDateLabel}}</v-card-title>
                        <v-btn
                            class="mr-4" small fab :color="colors.themeColor" outlined
                            @click="createScheduleModal = true"
                        >
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>

                    </div>
                    <v-card-text class="pb-0 flex-grow-0 flex-shrink-0" >
                        <v-radio-group class="my-0" v-model="showSchedules">
                            <v-radio label="全て" value="all"></v-radio>
                            <v-radio label="個人スケジュールのみ" value="personal"></v-radio>
                            <v-radio label="共有スケジュールのみ" value="shared"></v-radio>
                        </v-radio-group>
                        <v-divider></v-divider>
                    </v-card-text>
                    <v-card-text
                        style="overflow-y: scroll;"
                        class="flex-grow-1 flex-shrink-1 custom-scrollbar pt-0"
                    >
                        <schedule-list
                            :schedules="selectDateSchedules"
                            :is-personal="true"
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
                :selected-date="selectedDateLabel"
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
    import moment from "moment"

    import Calendar from "../../modules/Calendar"
    import CreateScheduleFormCard from "../../modules/CreateScheduleFormCard"
    import EditScheduleFormCard from "../../modules/EditScheduleFormCard"
    import DeleteScheduleFormCard from "../../modules/DeleteScheduleFormCard"
    import ScheduleList from "../../modules/ScheduleList"

    import colorsMixin from "../../../mixins/colorsMixin"
    import schedulesAndCalendarMethodsMixin from "../../../mixins/schedulesAndCalendarMethodsMixin"

    import {CREATED, SUCCESS, VALIDATION_ERROR} from "../../../util";
    export default {
        name: "PersonalCalendar",
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
        data() {
            return {
                calendarData: [], // カレンダーデータ配列
                selectedMonth: {}, // 選択中の月(momentオブジェクト)
                selectedMonthLabel: '', // 選択中の年月(YYYY年MM月)
                selectedDateLabel: '', // 選択中の日付(YYYY-MM-DD)
                weeksNum: 0, // 選択月が跨ぐ週数
                showSchedules: 'all', // 表示するスケジュールの種類
                createScheduleModal: false, // スケジュール作成モーダルの表示フラグ
                editScheduleModal: false, // スケジュール更新モーダルの表示フラグ
                deleteScheduleModal: false, // スケジュール削除モーダルの表示フラグ
                scheduleDataToBeUpdated: {}, // 更新するスケジュールデータ
                scheduleDataToBeDeleted: {}, // 削除するスケジュールデータ
                createError: {}, // スケジュール作成APIバリデーションエラー
                editError: {}, // スケジュール更新APIバリデーションエラー
            }
        },
        props: {
            // スケジュールデータ
            schedulesData: {
                type: Object,
                required: true,
                default: {
                    // 取得したスケジュール年
                    schedulesYear: null,
                    // スケジュールデータオブジェクト {'YYYY-MM-DD': Array, ...}
                    schedules: {},
                }
            }
        },
        computed: {
            // スケジュール数表示用の配列
            scheduleNumberData: function() {
                if(this.showSchedules === 'personal'){
                    return this.calendarData.map((dateData) => {
                        return {schedules: dateData.schedules.filter((schedule) => {
                                return schedule.user_id
                            })}
                    })
                } else if(this.showSchedules === 'shared') {
                    return this.calendarData.map((dateData) => {
                        return {schedules: dateData.schedules.filter((schedule) => {
                                return schedule.calendar_name
                            })}
                    })
                } else {
                    return this.calendarData
                }
            },
            // 選択日のスケジュールデータ
            selectDateSchedules: function() {
                if(this.showSchedules === 'personal'){
                    for (let i = 0; i < this.calendarData.length; i++){
                        if (this.selectedDateLabel === this.calendarData[i].date){
                            return this.calendarData[i].schedules.filter((schedule) =>{
                                return schedule.user_id
                            })
                        }
                    }
                } else if(this.showSchedules === 'shared') {
                    for (let i = 0; i < this.calendarData.length; i++){
                        if (this.selectedDateLabel === this.calendarData[i].date){
                            return this.calendarData[i].schedules.filter((schedule) =>{
                                return schedule.calendar_name
                            })
                        }
                    }
                } else {
                    for (let i = 0; i < this.calendarData.length; i++){
                        if (this.selectedDateLabel === this.calendarData[i].date){
                            return this.calendarData[i].schedules
                        }
                    }
                }
            },
        },
        methods: {
            // 選択月の変更
            changeSelectedMonth(num) {
                if (num === -1) {
                    this.selectedMonth = moment(this.selectedMonth).subtract(1, 'month')
                } else if (num === 1) {
                    this.selectedMonth = moment(this.selectedMonth).add(1, 'month')
                }
            },
            // 選択日の変更
            changeSelectedDate(date) {
                this.selectedDateLabel = date
            },
            // カレンダー関連のデータを変更
            changeCalendarRelatedData() {
                const newData = this.schedulesAndCalendarMethods.generateCalendarRelatedData(this.selectedMonth, this.schedulesData.schedules)

               this.selectedDateLabel = newData.selectedDateLabel
               this.selectedMonthLabel = newData.selectedMonthLabel
               this.weeksNum = newData.weeksNum
               this.calendarData = newData.calendarData
            },
            // スケジュールの取得
            async fetchSchedules(year){
                const firstDay = moment().year(year).startOf('year')
                const firstDayWeek = firstDay.day()
                const lastDay = moment().year(year).endOf('year')
                const lastDayWeek = lastDay.day()
                const from = firstDay.subtract(firstDayWeek, 'day').format('YYYY-MM-DD')
                const until = lastDay.add(6 - lastDayWeek, 'day').format('YYYY-MM-D')

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.get('/api/schedules/' + from + '/' + until)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    this.$emit('changeSchedulesData',{
                        schedulesYear: year,
                        schedules: response.data
                    })
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * スケジュールの作成
             * @param {Object}schedule 作成するスケジュール
             * @return {Promise<boolean>}
             */
            async createSchedule(schedule){
                this.createError = {}

                // スケジュール作成APIに作成するスケジュールデータとともにpostリクエスト
                // レスポンス待ちの間ローディング画面を表示
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.post('/api/schedules', schedule)
                this.$store.commit('loading/setLoadingFlg', false)

                // リクエスト成功の場合
                if (response.status === CREATED) {
                    // スケジュールリストとカレンダーデータにスケジュールを追加
                    const newData = this.schedulesAndCalendarMethods.addScheduleToSchedulesListAndCalendarData(response.data, this.schedulesData.schedules, this.calendarData)
                    this.$emit('changeSchedulesData',{schedules: newData.newScheduleList})
                    this.calendarData = newData.newCalendarData

                    // モーダルを非表示にしフラッシュメッセージを表示
                    this.createScheduleModal = false
                    this.$store.commit('flashMessage/setMessage', 'スケジュールを追加しました')
                    return false
                }

                // バリデーションエラーの場合エラーメッセージを代入
                if (response.status === VALIDATION_ERROR) {
                    this.createError = response.data.errors
                    return false
                }

                // 上記以外の場合エラーコードを代入
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

                // スケジュール更新APIに更新するスケジュールデータとともにpatchリクエスト
                // レスポンス待ちの間ローディング画面を表示
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.patch('/api/schedules/' + this.scheduleDataToBeUpdated.id, schedule)
                this.$store.commit('loading/setLoadingFlg', false)

                // 通信成功の場合
                if (response.status === SUCCESS) {
                    // スケジュールリストとカレンダーデータのスケジュールを更新
                    const newData = this.schedulesAndCalendarMethods.updateScheduleInSchedulesListAndCalendarData(this.scheduleDataToBeUpdated, response.data, this.schedulesData.schedules, this.calendarData)
                    this.$emit('changeSchedulesData',{schedules: newData.newScheduleList})
                    this.calendarData = newData.newCalendarData

                    // モーダルを非表示にしフラッシュメッセージを表示
                    this.editScheduleModal = false
                    this.$store.commit('flashMessage/setMessage', 'スケジュールを更新しました')
                    return false
                }

                // バリデーションエラーの場合エラーメッセージを代入
                if (response.status === VALIDATION_ERROR) {
                    this.editError = response.data.errors
                    return false
                }

                // 上記以外の場合エラーコードを代入
                this.$store.commit('error/setCode', response.status)

            },
            /**
             * スケジュールの削除
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
                const response = await axios.delete('/api/schedules/' + this.scheduleDataToBeDeleted.id)
                this.$store.commit('loading/setLoadingFlg', false)

                // 通信成功の場合
                if (response.status === SUCCESS) {
                    // カレンダーデータとスケジュールリストデータからスケジュールを削除
                    const newData = this.schedulesAndCalendarMethods.removeScheduleFromSchedulesListAndCalendarData(this.scheduleDataToBeDeleted, this.schedulesData.schedules, this.calendarData)
                    this.$emit('changeSchedulesData',{schedules: newData.newScheduleList})
                    this.calendarData = newData.newCalendarData

                    // モーダルを非表示にしフラッシュメッセージを表示
                    this.deleteScheduleModal = false
                    this.$store.commit('flashMessage/setMessage', 'スケジュールを削除しました')
                    return false
                }

                // 通信エラーの場合エラーコードを代入
                this.$store.commit('error/setCode', response.status)
            },
            showEditModal(schedule){
                this.scheduleDataToBeUpdated = schedule
                this.editScheduleModal = true
            },
            showDeleteModal(schedule){
                this.scheduleDataToBeDeleted = schedule
                this.deleteScheduleModal = true
            },
        },
        created() {
            // 現在月を設定
            this.selectedMonth = moment()
            this.selectedDateLabel = moment().format('YYYY-MM-DD')
        },
        watch: {
            selectedMonth: async function() {
                const selectedYear = moment(this.selectedMonth).format('YYYY')
                if(this.schedulesData.schedulesYear !== selectedYear){
                    await this.fetchSchedules(selectedYear)
                }
                this.changeCalendarRelatedData()
            },
            // モーダルが消えたときにモーダル内のフォームをリセット
            createScheduleModal:  function (val) {
                !val && this.$refs.createForm.resetForm()
            },
            editScheduleModal: function (val) {
                 if (!val) {
                     this.scheduleDataToBeUpdated = {}
                     this.$refs.editForm.resetForm()
                 }
            }
        },
    }
</script>

<style scoped>
    .content-wrap{
        height: calc(100vh - 64px);
    }
</style>
