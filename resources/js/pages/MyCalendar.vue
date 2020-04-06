<template>
    <div>
        <div class="sidebar-wrap">
            <SideBar />
        </div>
        <div class="my-calendar">
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
                        <button @click="showEditModal(schedule)"><i class="far fa-file-alt"></i></button>
                        <button @click="showDeleteModal(schedule)"><i class="far fa-trash-alt"></i></button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="modal-background" v-show="modalFlg" @click="hideModal">
            <div class="modal" >
                <div class="modal-inner" @click.stop>


                    <form @submit.prevent="updateSchedule" v-show="editForm.showFlg">
                        <p v-if="editError.errors.schedule">{{ editError.errors.schedule[0] }}</p>
                        <p>{{ selectedDate }}</p>

                        <label for="edit-hour">時間</label>
                        <select name="hour"
                                id="edit-hour"
                                v-model="editForm.scheduleData.hour">
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
                                id="edit-minute"
                                v-model="editForm.scheduleData.minute">
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
                        <p v-if="editError.errors.time">{{ editError.errors.time[0] }}</p>

                        <label for="edit-title">スケジュール名 *必須</label>
                        <input id="edit-title" type="text" v-model="editForm.scheduleData.title">
                        <p v-if="editError.errors.title">{{ editError.errors.title[0] }}</p>

                        <label for="edit-description">詳細</label>
                        <textarea id="edit-description" v-model="editForm.scheduleData.description"></textarea>
                        <p v-if="editError.errors.description">{{ editError.errors.description[0] }}</p>

                        <button type="submit">スケジュール更新</button>
                    </form>

                    <i class="fas fa-times" @click="hideModal"></i>
                    <div v-show="deleteForm.showFlg">
                        <p>このスケジュールを削除しますか？</p>
                        <p>日付</p>
                        <p>{{ deleteData.date}}</p>
                        <p>時間</p>
                        <p>{{ deleteData.time }}</p>
                        <p>スケジュール名</p>
                        <p>{{ deleteData.title }}</p>
                        <p>詳細</p>
                        <p>{{ deleteData.description }}</p>
                        <form @submit.prevent="deleteSchedule">
                            <button>削除</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import SideBar from "../components/SideBar"
    import moment from "moment"
    import {CREATED, NOT_FOUND, SUCCESS, VALIDATION_ERROR} from "../util";
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
                },
                modalFlg: false,
                editForm: {
                    showFlg: false,
                    scheduleData: {
                        id: null,
                        date: '',
                        hour: 'unspecified',
                        minute: 'unspecified',
                        title: '',
                        description: ''
                    }
                },
                deleteForm: {
                    showFlg: false,
                    scheduleData: null
                },
                createError: {
                    errorFlg: false,
                    errors: {}
                },
                editError: {
                    errorFlg: false,
                    errors: {}
                },
            }
        },
        computed: {
            // 選択日のスケジュールデータ
            selectDateSchedules: function() {
                for (let i = 0; i < this.dates.length; i++){
                    if (this.selectedDate === this.dates[i].date){
                        return this.dates[i].schedules
                    }
                }
            },
            datesData: function() {
                return this.dates
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
                this.createError.errorFlg = false
                this.createError.errors = {}
                // 入力値が不正な場合
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
                            break
                        }
                    }
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
            async updateSchedule(){
                this.editError.errorFlg = false
                this.editError.errors = {}

                // 入力値が不正な場合
                if (!this.editForm.scheduleData.id) {
                    this.editError.errors.schedule = ['スケジュールを選択してください。']
                    this.editError.errorFlg = true
                }
                if ((this.editForm.scheduleData.hour === 'unspecified' && this.editForm.scheduleData.minute !== 'unspecified') ||
                    (this.editForm.scheduleData.hour !== 'unspecified' && this.editForm.scheduleData.minute === 'unspecified')) {
                    this.editError.errors.time = ['時間の形式を確認してください。']
                    this.editError.errorFlg = true
                }
                if(!this.editForm.scheduleData.title){
                    this.editError.errors.title = ['スケジュール名は必須です。']
                    this.editError.errorFlg = true
                }

                if (this.editError.errorFlg) {
                    return false
                }

                // postするデータを作成
                const time = this.editForm.scheduleData.hour === 'unspecified'
                    ? null
                    : this.editForm.scheduleData.hour + ':' + this.editForm.scheduleData.minute + ':00'
                const description = !!this.editForm.scheduleData.description ? this.editForm.scheduleData.description : null
                const data = {
                    time: time,
                    title: this.editForm.scheduleData.title,
                    description: description
                }

                const response = await axios.patch('/api/schedule/' + this.editForm.scheduleData.id, data)

                if (response.status === SUCCESS) {
                    // 更新前のデータを削除
                    outer: for (let i = 0; i < this.dates.length; i++){
                        if (response.data.date === this.dates[i].date){
                            for (let t = 0 ; t < this.dates[i].schedules.length; t++) {
                                if (response.data.id === this.dates[i].schedules[t].id) {
                                    this.dates[i].schedules.splice(t, 1)
                                    break outer
                                }
                            }
                        }
                    }

                    // 更新後のデータを時間順に並ぶように追加
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

                    this.hideModal()
                    return false
                }

                if (response.status === VALIDATION_ERROR) {
                    this.editError.errors = response.data.errors
                    return false
                }

                this.$store.commit('error/setCode', response.status)

            },
            // スケジュールの削除
            async deleteSchedule(){
                if (!this.deleteForm.scheduleData) {
                    return false
                }

                const response = await axios.delete('/api/schedule/' + this.deleteForm.scheduleData.id)

                if (response.status === SUCCESS) {
                    outer: for (let i = 0; i < this.dates.length; i++){
                        if (response.data.date === this.dates[i].date){
                            for (let t = 0 ; t < this.dates[i].schedules.length; t++) {
                                if (response.data.id === this.dates[i].schedules[t].id) {
                                    this.dates[i].schedules.splice(t, 1)
                                    break outer
                                }
                            }
                        }
                    }
                    this.hideModal()
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            showEditModal(schedule){
                this.modalFlg = true
                this.editForm.showFlg = true

                let hour, minute

                if (schedule.time) {
                    hour = schedule.time.substr(0, 2)
                    minute = schedule.time.substr(3, 2)
                }else{
                    hour = 'unspecified'
                    minute = 'unspecified'
                }
                this.editForm.scheduleData = {
                    id: schedule.id,
                    date: schedule.date,
                    hour: hour,
                    minute: minute,
                    title: schedule.title,
                    description: schedule.description
                }
            },
            showDeleteModal(schedule){
                this.modalFlg = true
                this.deleteForm.showFlg = true
                this.deleteForm.scheduleData = schedule
            },
            hideModal(){
                this.modalFlg = false

                this.deleteForm.showFlg = false
                this.deleteForm.scheduleData = null

                this.editForm.showFlg = false
                this.editForm.scheduleData = {
                    id: null,
                    date: '',
                    hour: 'unspecified',
                    minute: 'unspecified',
                    title: '',
                    description: ''
                }

                this.editError = {
                    errorFlg: false,
                    errors: {}
                }
            }

        },
        created() {
            // 現在月を設定
            this.selectedMonth = moment()
            this.selectedDate = moment().format('YYYY-MM-DD')
        },
        watch: {
            selectedMonth: async function() {
                // データの初期化、選択中の年月日設定
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
    max-width: 800px;
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
