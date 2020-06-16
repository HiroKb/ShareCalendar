<template>
    <v-container class="fixed-content">
        <v-row class="fill-height">
<!--            カレンダー-->
            <v-col cols="12" md="7">
                <calendar
                    :selected-month="selectedMonth"
                    :selected-date-label="selectedDateLabel"
                    :weeks="weeksNum"
                    :calendar-data="filteredCalendarData"
                    @changeSelectedMonthRequest="changeSelectedMonth"
                    @changeSelectedDateRequest="changeSelectedDate"
                />
            </v-col>
<!--            カレンダーメニュー-->
            <v-col cols="12" md="5" class="fill-height">
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

    import utilDataMixin from "../../../mixins/utilDataMixin"
    import schedulesAndCalendarMethodsMixin from "../../../mixins/schedulesAndCalendarMethodsMixin"

    import {CREATED, SUCCESS, VALIDATION_ERROR} from "../../../util";
    export default {
        name: "PersonalCalendar",
        mixins: [
            utilDataMixin,
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
                default: () => ({
                    schedulesYear: null,
                    schedules: {},
                })
            },
            // 親コンポーネントのスケジュール取得メソッド
            fetchSchedules: {
                type: Function,
                required: true,
            }
        },
        computed: {
            /**
             * 絞り込み後のカレンダーデータ
             * @return {Array}
             */
            filteredCalendarData: function() {
                if(this.showSchedules === 'personal'){
                    return this.calendarData.map((dateData) => {
                        return {
                            date: dateData.date,
                            dateNum: dateData.dateNum,
                            schedules: dateData.schedules.filter((schedule) => {
                                return !schedule.calendar_id
                            }),
                            isSelectedMonth: dateData.isSelectedMonth
                        }
                    })
                } else if(this.showSchedules === 'shared') {
                    return this.calendarData.map((dateData) => {
                        return {
                            date: dateData.date,
                            dateNum: dateData.dateNum,
                            schedules: dateData.schedules.filter((schedule) => {
                                return schedule.calendar_id
                            }),
                            isSelectedMonth: dateData.isSelectedMonth
                        }
                    })
                }

                return this.calendarData
            },
            /**
             * 選択日のスケジュールデータ
             * @return {Array}
             */
            selectDateSchedules: function() {
                for (let i = 0; i < this.filteredCalendarData.length; i++){
                    if (this.selectedDateLabel === this.filteredCalendarData[i].date){
                        return this.filteredCalendarData[i].schedules
                    }
                }
            },
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
            changeSelectedDate(date) {
                this.selectedDateLabel = date
            },
            /**
             * カレンダー関連のデータを更新
             * @param {boolean} changeSelectedDate selectedDateLabelを変更するか
             */
            changeCalendarRelatedData(changeSelectedDate = true) {
                const newData = this.mixinGenerateCalendarRelatedData(this.selectedMonth, this.schedulesData.schedules)

                if (changeSelectedDate) {
                    this.selectedDateLabel = newData.selectedDateLabel
                }
               this.weeksNum = newData.weeksNum
               this.calendarData = newData.calendarData
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

                if (response.status === CREATED) {
                    // スケジュールリストとカレンダーデータにスケジュールを追加
                    const newSchedules = this.mixinAddScheduleToSchedulesList(response.data, this.schedulesData.schedules)
                    this.$emit('changeSchedulesData',{schedules: newSchedules})

                    // モーダルを非表示にしフラッシュメッセージを表示
                    this.createScheduleModal = false
                    this.$store.commit('flashMessage/setMessage', 'スケジュールを追加しました')
                    return false
                }

                if (response.status === VALIDATION_ERROR) {
                    this.createError = response.data.errors
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

                // スケジュール更新APIに更新するスケジュールデータとともにpatchリクエスト
                // レスポンス待ちの間ローディング画面を表示
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.patch('/api/schedules/' + this.scheduleDataToBeUpdated.id, schedule)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    // スケジュールリストとカレンダーデータのスケジュールを更新
                    const newSchedules = this.mixinUpdateScheduleInSchedulesList(this.scheduleDataToBeUpdated, response.data, this.schedulesData.schedules)
                    this.$emit('changeSchedulesData',{schedules: newSchedules})

                    // モーダルを非表示にしフラッシュメッセージを表示
                    this.editScheduleModal = false
                    this.$store.commit('flashMessage/setMessage', 'スケジュールを更新しました')
                    return false
                }

                if (response.status === VALIDATION_ERROR) {
                    this.editError = response.data.errors
                    return false
                }

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

                if (response.status === SUCCESS) {
                    // カレンダーデータとスケジュールリストデータからスケジュールを削除
                    const newSchedules = this.mixinRemoveScheduleFromScheduleList(this.scheduleDataToBeDeleted, this.schedulesData.schedules)
                    this.$emit('changeSchedulesData',{schedules: newSchedules})

                    // モーダルを非表示にしフラッシュメッセージを表示
                    this.deleteScheduleModal = false
                    this.$store.commit('flashMessage/setMessage', 'スケジュールを削除しました')
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
            showDeleteModal(schedule){
                this.scheduleDataToBeDeleted = schedule
                this.deleteScheduleModal = true
            },
        },
        created() {
            this.selectedMonth = moment()
            this.selectedDateLabel = moment().format('YYYY-MM-DD')
        },
        watch: {
            /**
             * スケジュールデータが更新された場合カレンダーデータも更新
             */
            schedulesData:{
                handler: function (val) {
                    this.changeCalendarRelatedData(false)
                },
                deep: true
            },
            /**
             * 選択月が変更された場合カレンダーデータも更新
             * 選択月の年と取得しているスケジュール年が違う場合スケジュール取得リクエストをエミット
             */
            selectedMonth: function() {
                const selectedYear = moment(this.selectedMonth).format('YYYY')
                this.changeCalendarRelatedData()
                if(this.schedulesData.schedulesYear !== selectedYear){
                    // 親のスケジュール取得メソッドを呼ぶ
                    this.fetchSchedules(selectedYear, true)
                }
            },
            /**
             *  モーダルが非表示になった場合モーダル内のフォームをリセット
             * @param {Boolean} val モーダルの表示フラグ
             */
            createScheduleModal:  function (val) {
                !val && this.$refs.createForm.resetForm()
            },
            editScheduleModal: function (val) {
                 if (!val) {
                     this.scheduleDataToBeUpdated = {}
                     this.$refs.editForm.resetForm()
                 }
            },
        },
    }
</script>

<style scoped>
</style>
