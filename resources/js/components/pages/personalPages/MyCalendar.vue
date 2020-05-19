<template>
    <v-container class="content-wrap py-12">
        <v-row class="fill-height">
<!--            カレンダー-->
            <v-col cols="7">
                <calendar
                    :date-label="dateLabel"
                    :weeks="weeks"
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
                        <v-card-title class="flex-grow-0 flex-shrink-0">{{ selectedDate}}</v-card-title>
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
                :selected-date="selectedDate"
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
    import formTimeMixin from "../../../mixins/formTimeMixin"
    import validationRulesMixin from "../../../mixins/validationRulesMixin"
    import colorsMixin from "../../../mixins/colorsMixin"
    import {CREATED, SUCCESS, VALIDATION_ERROR} from "../../../util";
    export default {
        name: "MyCalendar",
        mixins: [formTimeMixin, validationRulesMixin, colorsMixin],
        components:{
            Calendar,
            ScheduleList,
            CreateScheduleFormCard,
            EditScheduleFormCard,
            DeleteScheduleFormCard
        },
        data() {
            return {
                calendarData: [], // 日付データ配列(選択月前後35日分)
                dateLabel: '', // 選択中の年月表示用(YYYY年MM月)
                selectedMonth: null, // 選択中の月(momentオブジェクト)
                selectedDate: null, // 選択中の日付
                weeks: 0, // 選択月が何週を跨ぐか
                showSchedules: 'all', // 表示するスケジュールの種類
                createScheduleModal: false,
                editScheduleModal: false,
                deleteScheduleModal: false,
                scheduleDataToBeDeleted: {},
                scheduleDataToBeUpdated: {},
                createError: {},
                editError: {},
            }
        },
        props: {
            schedulesData: {
                type: Object,
                required: true,
                default: {
                    schedulesYear: null,
                    schedules: [],
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
                        if (this.selectedDate === this.calendarData[i].date){
                            return this.calendarData[i].schedules.filter((schedule) =>{
                                return schedule.user_id
                            })
                        }
                    }
                } else if(this.showSchedules === 'shared') {
                    for (let i = 0; i < this.calendarData.length; i++){
                        if (this.selectedDate === this.calendarData[i].date){
                            return this.calendarData[i].schedules.filter((schedule) =>{
                                return schedule.calendar_name
                            })
                        }
                    }
                } else {
                    for (let i = 0; i < this.calendarData.length; i++){
                        if (this.selectedDate === this.calendarData[i].date){
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
                this.selectedDate = date
            },
            // カレンダーデータの変更
           changeCalendarData() {
                this.calendarData = []
                this.dateLabel = moment(this.selectedMonth).format('YYYY年MM月')

                // 選択月の日数
                const monthDays = moment(this.selectedMonth).daysInMonth()
                // 選択月初日の曜日
                const firstDay = moment(this.selectedMonth).startOf('month').day()

                this.weeks = Math.ceil((monthDays + firstDay) / 7)



                // 選択月初日より前の日付データ(選択月前月)を配列へ追加
                for(let i = 0; i < firstDay; i++){
                    const day = moment(this.selectedMonth).startOf('month').subtract(i + 1, 'days')
                    this.calendarData.unshift({
                        date: day.format('YYYY-MM-DD'),
                        dateNum: day.date(),
                        schedules: []
                    })
                }


                // 選択月の日付データを配列へ追加
                for (let i = 0; i < monthDays; i++) {
                    const day = moment(this.selectedMonth).startOf('month').add(i, 'days')
                    this.calendarData.push({
                        date: day.format('YYYY-MM-DD'),
                        dateNum: day.date(),
                        schedules: []
                    })
                }


                // 選択月末日より後の日付データを配列へ追加
                for (let length = this.calendarData.length, i = 1; length < this.weeks * 7; length++, i++){
                    const day = moment(this.selectedMonth).endOf('month').add(i, 'days')
                    this.calendarData.push({
                        date: day.format('YYYY-MM-DD'),
                        dateNum : day.date(),
                        schedules: []
                    })
                }

                // 日付データ配列にスケジュールデータを追加
                this.calendarData.forEach((dateData) => {
                    if (this.schedulesData.schedules[dateData.date]){
                        dateData.schedules = _.cloneDeep(this.schedulesData.schedules[dateData.date])
                    }
                })
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
            // スケジュール追加
            async createSchedule(data){
                this.createError = {}

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.post('/api/schedules', data)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === CREATED) {
                    // カレンダーデータとスケジュールリストデータを更新
                    const newSchedules = this.addScheduleData(response.data, this.calendarData, this.schedulesData.schedules)

                    this.$emit('changeSchedulesData',{schedules: newSchedules})

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
            // スケジュールの更新
            async updateSchedule(data){
                this.editError = {}

                if (!this.scheduleDataToBeUpdated.id) {
                    return false
                }

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.patch('/api/schedules/' + this.scheduleDataToBeUpdated.id, data)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {

                    const deletedSchedules = this.removeScheduleData(this.scheduleDataToBeUpdated, this.calendarData, this.schedulesData.schedules)
                    const newSchedules = this.addScheduleData(response.data, this.calendarData, deletedSchedules)

                    this.$emit('changeSchedulesData',{schedules: newSchedules})

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
            // スケジュールの削除
            async deleteSchedule(){
                if (!this.scheduleDataToBeDeleted.id){
                    return false
                }

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.delete('/api/schedules/' + this.scheduleDataToBeDeleted.id)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    // カレンダーデータとスケジュールリストデータを更新
                    const newSchedules = this.removeScheduleData(this.scheduleDataToBeDeleted, this.calendarData, this.schedulesData.schedules)
                    this.$emit('changeSchedulesData',{schedules: newSchedules})

                    this.deleteScheduleModal = false
                    this.$store.commit('flashMessage/setMessage', 'スケジュールを削除しました')
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            addScheduleData(additionalSchedule, calendarData, schedulesList){
                const additionalScheduleDate = additionalSchedule.date
                const additionalScheduleTime = additionalSchedule.time

                outer: for (let i = 0; i < calendarData.length; i++) {
                    if (additionalScheduleDate === calendarData[i].date){
                        // スケジュール登録数0の日付か
                        // 登録したスケジュールに時間情報がない場合
                        // 配列の先頭にデータを追加
                        if (!calendarData[i].schedules.length || additionalScheduleTime === null) {
                            calendarData[i].schedules.unshift(additionalSchedule)
                            break
                        }

                        // 前後スケジュールが登録されている場合
                        // 時間順に並ぶようにデータを追加
                        for (let t = 0; t < calendarData[i].schedules.length; t++) {

                            if (calendarData[i].schedules[t].time === null) {
                                continue
                            }

                            if(additionalScheduleTime <= calendarData[i].schedules[t].time) {
                                calendarData[i].schedules.splice(t, 0, additionalSchedule)
                                break outer
                            }
                        }

                        // 上記以外の場合最後にデータを追加
                        calendarData[i].schedules.push(additionalSchedule)
                        break
                    }
                }

                // スケジュールをスケジュールリストデータに追加
                const newSchedules = _.cloneDeep(schedulesList)
                // 登録した日にスケジュールがない場合
                if (!newSchedules[additionalScheduleDate]){
                    newSchedules[additionalScheduleDate] = [additionalSchedule]
                    return newSchedules
                }

                // 登録したしたスケジュールに時間指定がない場合先頭に追加
                if(additionalScheduleTime === null) {
                    newSchedules[additionalScheduleDate].unshift(additionalSchedule)
                    return newSchedules
                }

                // 前後スケジュールが登録されている場合
                // 時間順に並ぶようにデータを追加
                for (let i = 0; i < newSchedules[additionalScheduleDate].length; i++) {
                    if (newSchedules[additionalScheduleDate][i].time === null) {
                        continue
                    }
                    if(newSchedules[additionalScheduleDate][i].time >= additionalScheduleTime) {
                        newSchedules[additionalScheduleDate].splice(i, 0, additionalSchedule)
                        return  newSchedules
                    }
                }
                // 上記以外の場合最後にデータを追加
                newSchedules[additionalScheduleDate].push(additionalSchedule)

                // 変更したスケジュールリストデータを返却
                return newSchedules
            },
            /**
             * カレンダーデータとスケジュールリストデータからスケジュールを削除
             * @param removalSchedule 削除するスケジュールデータ
             * @param calendarData カレンダーデータ(破壊的)
             * @param schedulesList スケジュールリストデータ(非破壊的)
             * @returns {*} (変更後のスケジュールリストデータ)
             */
            removeScheduleData(removalSchedule, calendarData, schedulesList){
                // 削除するスケジュールの日にち
                const removalScheduleDate = removalSchedule.date
                // 削除するスケジュールのID
                const removalScheduleId = removalSchedule.id
                // カレンダーデータからスケジュールを削除
                outer: for (let i = 0; i < calendarData.length; i++) {
                    if (removalScheduleDate === calendarData[i].date) {
                        for (let t = 0 ; t < calendarData[i].schedules.length; t++) {
                            if (removalScheduleId === calendarData[i].schedules[t].id) {
                                calendarData[i].schedules.splice(t, 1)
                                break outer
                            }
                        }
                    }
                }

                // スケジュールリストデータからスケジュールを削除
                const newSchedules = _.cloneDeep(schedulesList)
                for(let i = 0; i < newSchedules[removalScheduleDate].length; i++) {
                    if (newSchedules[removalScheduleDate][i].id === removalScheduleId){
                        newSchedules[removalScheduleDate].splice(i, 1)
                        break
                    }
                }
                // 変更したスケジュールリストデータを返却
                return newSchedules
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
            this.selectedDate = moment().format('YYYY-MM-DD')
        },
        watch: {
            selectedMonth: async function() {
                const selectedYear = moment(this.selectedMonth).format('YYYY')
                if(this.schedulesData.schedulesYear !== selectedYear){
                    await this.fetchSchedules(selectedYear)
                }
                this.changeCalendarData()
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
