<template>
    <div>
        <div class="contents">
            <div class="shared-calendar">

                <div class="calendar">
                    <p>{{ sharedCalendarData.calendar_name }}</p>
                    <p>{{ invitationUrl }}</p>
                    <table>
                        <caption><span @click="changeSelectedMonth(-1)"><  </span><span>{{ dateLabel }}</span><span @click="changeSelectedMonth(1)">  ></span></caption>
                        <thead>
                        <tr>
                            <th>日</th>
                            <th>月</th>
                            <th>火</th>
                            <th>水</th>
                            <th>木</th>
                            <th>金</th>
                            <th>土</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="row in weeks">
                            <td
                                v-for="column in 7"
                                :data-date="dates[(row - 1) * 7 + column - 1].date"
                                @click="changeSelectDate"
                            >
                                <p>{{ dates[(row - 1) * 7  + column - 1 ].dateNum }}</p>
                                <p>{{ dates[(row - 1) * 7 + column - 1 ].schedules.length}}</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="calendar-menu">
                    <form @submit.prevent="createSchedule">
                        <p>{{ selectedDate }}</p>

                        <p v-if="createError.errors.date">{{ createError.errors.date [0]}}</p>

                        <label for="hour">時間</label>
                        <select name="hour"
                                id="hour"
                                v-model="createScheduleData.hour">
                            <option value="unspecified">指定なし</option>
                            <option value="00">00</option>
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                        </select>
                        <span> : </span>

                        <select name="minute"
                                id="minute"
                                v-model="createScheduleData.minute">
                            <option value="unspecified">指定なし</option>
                            <option value="00">00</option>
                            <option value="05">05</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="25">25</option>
                            <option value="30">30</option>
                            <option value="35">35</option>
                            <option value="40">40</option>
                            <option value="45">45</option>
                            <option value="50">50</option>
                            <option value="55">55</option>
                        </select>
                        <p v-if="createError.errors.time">{{ createError.errors.time[0] }}</p>

                        <label for="title">スケジュール名 *必須</label>
                        <input id="title" type="text" v-model="createScheduleData.title">
                        <p v-if="createError.errors.title">{{ createError.errors.title [0]}}</p>

                        <label for="description">詳細</label>
                        <textarea id="description" v-model="createScheduleData.description"></textarea>
                        <p v-if="createError.errors.description">{{ createError.errors.description[0] }}</p>


                        <button type="submit">スケジュール追加</button>

                    </form>

                    <ul class="schedules">
                        <li class="schedule" v-for="schedule in selectDateSchedules" :key="schedule.id">
                            <p>{{ schedule.time}}</p>
                            <p>{{ schedule.title }}</p>
<!--                            <button @click="showEditModal(schedule)"><i class="far fa-file-alt"></i></button>-->
                            <button @click="showDeleteModal(schedule)"><i class="far fa-trash-alt"></i></button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="modal-background" v-show="modalFlg" @click="hideModal">
            <div class="modal" >
                <div class="modal-inner" @click.stop>

                    <i class="fas fa-times" @click="hideModal"></i>
                    <form @submit.prevent="deleteSchedule" v-show="deleteForm.showFlg">
                        <p>このスケジュールを削除しますか？</p>
                        <p>日付</p>
                        <p>{{ deleteData.date}}</p>
                        <p>時間</p>
                        <p>{{ deleteData.time }}</p>
                        <p>スケジュール名</p>
                        <p>{{ deleteData.title }}</p>
                        <p>詳細</p>
                        <p>{{ deleteData.description }}</p>
                        <button>削除</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from "moment"
    import {CREATED, SUCCESS, VALIDATION_ERROR} from "../util";
    export default {
        name: "SharedCalendarIndex",
        data () {
            return {
                dates: [],
                selectedMonth: null,
                selectedDate: null,
                dateLabel: null,
                weeks: 0,
                createScheduleData: {
                    hour: 'unspecified',
                    minute: 'unspecified',
                    title: '',
                    description: ''
                },
                createError: {
                    errorFlg: false,
                    errors: {}
                },
                modalFlg: false,
                deleteForm: {
                    showFlg: false,
                    scheduleData: null
            }
            }
        },
        computed: {
            invitationUrl: function () {
                return this.sharedCalendarData.search_id ?
                        location.protocol + '//' + location.host + '/shared-calendar/' + this.sharedCalendarData.search_id + '/application'
                        : ''
            },
            selectDateSchedules: function () {
                for (let i = 0; i < this.dates.length; i++){
                    if (this.selectedDate === this.dates[i].date){
                        return this.dates[i].schedules
                    }
                }
            },
            deleteData: function () {
                return {
                    date: this.deleteForm.scheduleData ? this.deleteForm.scheduleData.date : '',
                    time: this.deleteForm.scheduleData ? this.deleteForm.scheduleData.time ? this.deleteForm.scheduleData.time : '指定なし' : '',
                    title: this.deleteForm.scheduleData ? this.deleteForm.scheduleData.title : '',
                    description: this.deleteForm.scheduleData ? this.deleteForm.scheduleData.description ? this.deleteForm.scheduleData.description : 'なし' : '',
                }
            }
        },
        props: {
            sharedCalendarId: {
                type: String,
                required: true,
                default: ''
            },
            sharedCalendarData: {
                type: Object,
                required: true,
                default: {}
            },
            sharedSchedulesData: {
                type: Object,
                required: true,
                default: {
                    schedulesYear: null,
                    schedules: [],
                }
            }
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
            changeSelectDate(e) {
                this.selectedDate = e.currentTarget.dataset.date
            },
            changeDatesData() {
                this.dates = []
                this.dateLabel = moment(this.selectedMonth).format('YYYY年MM月')

                // 選択月の日数
                const monthDays = moment(this.selectedMonth).daysInMonth()
                // 選択月初日の曜日
                const firstDay = moment(this.selectedMonth).startOf('month').day()

                this.weeks = Math.ceil((monthDays + firstDay) / 7)
                // 選択月初日より前の日付データ(選択月前月)を配列へ追加
                for(let i = 0; i < firstDay; i++){
                    const day = moment(this.selectedMonth).startOf('month').subtract(i + 1, 'days')
                    this.dates.unshift({
                        date: day.format('YYYY-MM-DD'),
                        dateNum: day.date(),
                        schedules: []
                    })
                }


                // 選択月の日付データを配列へ追加
                for (let i = 0; i < monthDays; i++) {
                    const day = moment(this.selectedMonth).startOf('month').add(i, 'days')
                    this.dates.push({
                        date: day.format('YYYY-MM-DD'),
                        dateNum: day.date(),
                        schedules: []
                    })
                }


                // 選択月末日より後の日付データを配列へ追加
                for (let length = this.dates.length, i = 1; length < this.weeks * 7; length++, i++){
                    const day = moment(this.selectedMonth).endOf('month').add(i, 'days')
                    this.dates.push({
                        date: day.format('YYYY-MM-DD'),
                        dateNum : day.date(),
                        schedules: []
                    })
                }

                this.dates.forEach((dateData) => {
                    if (this.sharedSchedulesData.schedules[dateData.date]){
                        dateData.schedules = _.cloneDeep(this.sharedSchedulesData.schedules[dateData.date])
                    }
                })
            },
            async fetchSharedSchedules(year){
                const firstDay = moment().year(year).startOf('year')
                const firstDayWeek = firstDay.day()
                const lastDay = moment().year(year).endOf('year')
                const lastDayWeek = lastDay.day()
                const from = firstDay.subtract(firstDayWeek, 'day').format('YYYY-MM-DD')
                const until = lastDay.add(6 - lastDayWeek, 'day').format('YYYY-MM-D')

                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/schedules/' + from + '/' + until)

                if (response.status === SUCCESS) {
                    return response.data
                }

                this.$store.commit('error/setCode', response.status)
                return []
            },
            async createSchedule (){
                this.createError.errorFlg = false
                this.createError.errors = {}

                if (!this.selectedDate) {
                    this.createError.errors.date = ['日付を選択してください。']
                    this.createError.errorFlg = true
                }
                if ((this.createScheduleData.hour === 'unspecified' && this.createScheduleData.minute !== 'unspecified') ||
                    (this.createScheduleData.hour !== 'unspecified' && this.createScheduleData.minute === 'unspecified')) {
                    this.createError.errors.time = ['時間の形式を確認してください。']
                    this.createError.errorFlg = true
                }
                if(!this.createScheduleData.title){
                    this.createError.errors.title = ['スケジュール名は必須です。']
                    this.createError.errorFlg = true
                }

                if (this.createError.errorFlg) {
                    return false
                }

                // postするデータを作成
                const time = this.createScheduleData.hour === 'unspecified'
                    ? null
                    : this.createScheduleData.hour + ':' + this.createScheduleData.minute + ':00'
                const description = !!this.createScheduleData.description ? this.createScheduleData.description : null
                const data = {
                    date: this.selectedDate,
                    time: time,
                    title: this.createScheduleData.title,
                    description: description
                }
                const response = await axios.post('/api/shared-calendars/' + this.sharedCalendarId + '/schedules', data)
                //
                if (response.status === CREATED) {

                    // 登録したスケジュールデータを追加
                    outer: for (let i = 0; i < this.dates.length; i++) {
                        if (response.data.date === this.dates[i].date){
                            // スケジュール登録数0の日付か
                            // 登録したスケジュールに時間情報がない場合
                            // 配列の先頭にデータを追加
                            if (!this.dates[i].schedules.length || response.data.time === null) {
                                this.dates[i].schedules.unshift(response.data)
                                break
                            }

                            // 前後スケジュールが登録されている場合
                            // 時間順に並ぶようにデータを追加
                            for (let t = 0; t < this.dates[i].schedules.length; t++) {

                                if (this.dates[i].schedules[t].time === null) {
                                    continue
                                }

                                if(response.data.time <= this.dates[i].schedules[t].time) {
                                    this.dates[i].schedules.splice(t, 0, response.data)
                                    break outer
                                }
                            }

                            // 上記以外の場合最後にデータを追加
                            this.dates[i].schedules.push(response.data)
                            break
                        }
                    }

                    const newSchedules =  (() => {
                        // スケジュールデータオブジェクトを複製
                        const schedules = _.cloneDeep(this.sharedSchedulesData.schedules)
                        const data = response.data
                        // 登録した日にスケジュールがない場合
                        if (!schedules[data.date]){
                            schedules[data.date] = [data]
                            return schedules
                        }

                        // 登録したしたスケジュールに時間指定がない場合先頭に追加
                        if(data.time === null) {
                            schedules[data.date].unshift[data]
                            return schedules
                        }

                        // 前後スケジュールが登録されている場合
                        // 時間順に並ぶようにデータを追加
                        for (let i = 0; i < schedules[data.date].length; i++) {

                            if (schedules[data.date][i].time === null) {
                                continue
                            }

                            if(schedules[data.date][i].time >= data.time) {
                                schedules[data.date].splice(i, 0, data)
                                return  schedules
                            }
                        }


                        // 上記以外の場合最後にデータを追加
                        schedules[data.date].push(data)
                        return schedules
                    })()
                    this.$emit('changeSchedulesData', {schedules: newSchedules})
                    this.createScheduleData = {
                        hour: 'unspecified',
                        minute: 'unspecified',
                        title: '',
                        description: ''
                    }
                    return false
                }
                if (response.status === VALIDATION_ERROR) {
                    this.createError.errors = response.data.errors
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            async deleteSchedule(){
                console.log(this.deleteForm.scheduleData)
                if (!this.deleteForm.scheduleData){
                    return false
                }
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarId + '/schedules/' + this.deleteForm.scheduleData.id)
                if(response.status === SUCCESS) {
                    // カレンダーデータからスケジュールを削除
                    outer: for (let i = 0; i < this.dates.length; i++) {
                        if (this.dates[i].date === this.deleteForm.scheduleData.date) {
                            for (let t = 0 ; t < this.dates[i].schedules.length; t++) {
                                if (this.deleteForm.scheduleData.id === this.dates[i].schedules[t].id) {
                                    this.dates[i].schedules.splice(t, 1)
                                    break outer
                                }
                            }
                        }
                    }

                    const newSchedules = (() => {
                        const schedules = _.cloneDeep(this.sharedSchedulesData.schedules)
                        const data = this.deleteForm.scheduleData
                        for(let i = 0; i < schedules[data.date].length; i++) {
                            if (schedules[data.date][i].id === data.id){
                                schedules[data.date].splice(i, 1)
                                break
                            }
                        }
                        return schedules
                    })()
                    this.$emit('changeSchedulesData', {schedules: newSchedules})
                    this.hideModal()
                    return false
                }
                this.$store.commit('error/setCode', response.status)
            },
            showDeleteModal(schedule) {
                this.modalFlg = true
                this.deleteForm.showFlg = true
                this.deleteForm.scheduleData = schedule
            },
            hideModal() {
                this.modalFlg = false

                this.deleteForm.showFlg = false
                this.deleteForm.scheduleData = null
            }
        },
        created() {
            this.selectedMonth = moment()
            this.selectedDate = moment(this.selectedMonth).format('YYYY-MM-DD')
        },
        watch: {
            selectedMonth: async function () {
                const selectedYear = moment(this.selectedMonth).format('YYYY')
                if (this.sharedSchedulesData.schedulesYear !== moment(this.selectedMonth).format('YYYY')){
                    const schedules= await this.fetchSharedSchedules(selectedYear)
                    this.$emit('changeSchedulesData', {
                        schedulesYear: selectedYear,
                        schedules: schedules
                    })
                }
                this.changeDatesData()
            },
        }
    }
</script>

<style scoped>
    .sidebar-wrap{
        display: flex;
    }
    .shared-calendar{
        margin: 0 auto;
        display: flex;
    }
    table{
        border-collapse: collapse;
    }
    th,td{
        width: 80px;
        height: 80px;
        border: solid 1px black;
        text-align: center;
    }
    label{
        display: block;
        margin-top: 16px;
    }
    input{
        width: 100%;
    }
    textarea{
        width: 100%;
    }
    button{
        margin-top: 16px;
    }
    .calendar-menu{
        margin-left: 20px;
    }
    .schedules{
        margin-top: 20px;
    }
    .schedule{
        padding: 8px 0;
        border-top: 1px solid black;
    }

    .modal-background{
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 100vw ;
        z-index: 10;
        background: rgba(0, 0, 0, .1);
    }

    .modal{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        width: 100vw ;
    }
    .modal-inner{
        background: #ffffff;
    }
</style>
