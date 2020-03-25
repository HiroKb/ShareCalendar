<template>
    <div>
        <SideBar />
        <div class="my-calendar">
            <h1>MyCalendar</h1>
            <div class="calendar">
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
                                :data-date="datesData[(row - 1) * 7 + column - 1].date"
                                @click="changeSelectDate"
                            >
                                <p>{{ datesData[(row - 1) * 7  + column - 1 ].dateNum }}</p>
                                <p>{{ datesData[(row - 1) * 7 + column - 1 ].schedules.length}}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal">
            <form @submit.prevent="createSchedule">
                <p>{{ selectedDate }}</p>

                <label for="hour">時間</label>
                <select name="hour"
                        id="hour"
                        v-model="createScheduleData.hour">
                    <option value="unspecified">指定なし</option>
                    <option value="0">0</option>
                    <option :value="hour" v-for="hour in 23">{{ hour }}</option>
                </select>
                <span>時</span>

                <select name="minute"
                        id="minute"
                        v-model="createScheduleData.minute">
                    <option value="unspecified">指定なし</option>
                    <option value="0">0</option>
                    <option :value="minute * 5" v-for="minute in 11">{{ minute * 5 }}</option>
                </select>

                <label for="title">スケジュール名 *必須</label>
                <input id="title" type="text" v-model="createScheduleData.title">

                <label for="description">詳細</label>
                <input id="description" type="text" v-model="createScheduleData.description">


                <button type="submit">スケジュール追加</button>

            </form>

            <ul class="schedules">
                <li class="schedule" v-for="schedule in selectDateSchedules">
                    <p>{{ schedule.date}}</p>
                    <p>{{ schedule.time}}</p>
                    <p>{{ schedule.title }}</p>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import SideBar from "../components/SideBar"
    import moment from "moment"
    import {CREATED} from "../util";
    export default {
        name: "MyCalendar",
        components: {SideBar},
        data() {
            return {
                dates: [], // 日付データ配列(選択月前後35日分)
                dateLabel: '', // 選択中の年月表示用(YYYY年MM月)
                selectedMonth: null, // 選択中の月(momentオブジェクト)
                selectedDate: null, // 選択中の日付
                weeks: 0, // 選択月が何週を跨ぐか
                createScheduleData: {
                    hour: 'unspecified',
                    minute: 'unspecified',
                    title: '',
                    description: ''
                }
            }
        },
        computed: {
            // 選択日のスケジュールデータ
            selectDateSchedules: function() {
                const selectedDate = this.selectedDate
                for (let i = 0; i < this.dates.length; i++){
                    if (selectedDate === this.dates[i].date){
                        return this.dates[i].schedules
                    }
                }
            },
            datesData: function() {
                return this.dates
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
            // スケジュール登録
            async createSchedule(){
                // 入力値が不正な場合
                if (!this.selectedDate) {
                    return false
                }
                if ((this.createScheduleData.hour === 'unspecified' && this.createScheduleData.minute !== 'unspecified') ||
                    (this.createScheduleData.hour !== 'unspecified' && this.createScheduleData.minute === 'unspecified')) {
                    return false
                }
                if(!this.createScheduleData.title){
                    return false
                }


                // postするデータを作成
                const time = this.createScheduleData.hour === 'unspecified'
                             ? null
                             : ('0' + this.createScheduleData.hour).slice(-2) + ':' + ('0' + this.createScheduleData.minute).slice(-2) + ':00'
                const description = !!this.createScheduleData.description ? this.createScheduleData.description : null
                const data = {
                    date: this.selectedDate,
                    time: time,
                    title: this.createScheduleData.title,
                    description: description
                }

                const response = await axios.post('/api/schedule', data)

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
                        }
                    }
                    return false
                }

            }
        },
        created() {
            // 現在月を設定
            this.selectedMonth = moment()
        },
        watch: {
            selectedMonth: async function() {
                // 日付、スケジュール配列の初期化、選択中の年月設定
                this.dates = []
                this.schedules = []
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

                // カレンダーの初日の日付
                const from = this.dates[0].date
                // カレンダーの最終日の日付
                const until = this.dates[this.dates.length - 1].date

                // 登録スケジュール取得API
                const schedules = await axios.get('/api/schedule/' + from + '/' + until)

                // 日付データ配列にスケジュールデータを追加
                this.dates.forEach(function(dateData){
                    schedules.data.forEach(function (scheduleData) {
                        if (dateData.date === scheduleData.date){
                            dateData.schedules.push(scheduleData)
                        }
                    })
                })

            },
        },
    }
</script>

<style scoped>
.my-calendar{
    max-width: 500px;
    margin: 0 auto;
}
table{
    border-collapse: collapse;
}
th,td{
    width: 60px;
    border: solid 1px black;
    text-align: center;
}

</style>
