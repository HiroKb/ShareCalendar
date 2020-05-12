<template>
    <div>
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
                                :data-date="calendarData[(row - 1) * 7 + column - 1].date"
                                @click="changeSelectDate"
                            >
                                <p>{{ calendarData[(row - 1) * 7  + column - 1 ].dateNum }}</p>
                                <p>{{ scheduleNumberData[(row - 1) * 7 + column - 1 ].schedules.length}}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="calendar-menu">
                <p>表示スケジュール</p>
                <label><input type="radio" value="all" v-model="showSchedules">全て</label>
                <label><input type="radio" value="personal" v-model="showSchedules">個人スケジュールのみ</label>
                <label><input type="radio" value="shared" v-model="showSchedules">共有スケジュールのみ</label>

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
                        <template v-if="schedule.calendar_id">
                            <router-link :to="{name: 'sharedCalendarIndex', params:{sharedCalendarId: schedule.calendar_id}}">
                                <p>共有カレンダー : {{schedule.calendar_name}}</p>
                            </router-link>
                        </template>
                        <template v-else>
                            <button @click="showEditModal(schedule)"><i class="far fa-file-alt"></i></button>
                            <button @click="showDeleteModal(schedule)"><i class="far fa-trash-alt"></i></button>
                        </template>
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
    import moment from "moment"
    import {CREATED, NOT_FOUND, SUCCESS, VALIDATION_ERROR} from "../../../util";
    export default {
        name: "MyCalendar",
        data() {
            return {
                calendarData: [], // 日付データ配列(選択月前後35日分)
                dateLabel: '', // 選択中の年月表示用(YYYY年MM月)
                selectedMonth: null, // 選択中の月(momentオブジェクト)
                selectedDate: null, // 選択中の日付
                weeks: 0, // 選択月が何週を跨ぐか
                showSchedules: 'all', // 表示するスケジュールの種類
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

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.post('/api/schedules', data)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === CREATED) {
                    // カレンダーデータとスケジュールリストデータを更新
                    const newSchedules = this.addScheduleData(response.data, this.calendarData, this.schedulesData.schedules)

                    this.$emit('changeSchedulesData',{schedules: newSchedules})

                    this.createScheduleData = {
                        hour: 'unspecified',
                        minute: 'unspecified',
                        title: '',
                        description: ''
                    }
                    this.$store.commit('flashMessage/setMessage', 'スケジュールを追加しました')
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

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.patch('/api/schedules/' + this.editForm.scheduleData.id, data)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {

                    const deletedSchedules = this.removeScheduleData(this.editForm.scheduleData, this.calendarData, this.schedulesData.schedules)
                    const newSchedules = this.addScheduleData(response.data, this.calendarData, deletedSchedules)

                    this.$emit('changeSchedulesData',{schedules: newSchedules})
                    this.hideModal()
                    this.$store.commit('flashMessage/setMessage', 'スケジュールを更新しました')
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

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.delete('/api/schedules/' + this.deleteForm.scheduleData.id)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    // カレンダーデータとスケジュールリストデータを更新
                    const newSchedules = this.removeScheduleData(this.deleteForm.scheduleData, this.calendarData, this.schedulesData.schedules)
                    this.$emit('changeSchedulesData',{schedules: newSchedules})

                    this.hideModal()
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
                        if (calendarData[i].schedules.length || additionalScheduleTime === null) {
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
