export default {
    methods: {
        /**
         * カレンダー描画に必要なデータを生成する
         * @param {Object} month momentオブジェクト
         * @param {Object} schedulesList スケジュールオブジェクト {'YYYY-MM-DD': Array}
         * @return {{selectedDateLabel: string, weeksNum: number, calendarData: [], selectedMonthLabel: string}}
         */
        mixinGenerateCalendarRelatedData(month, schedulesList) {

            let selectedDateLabel ='',
                weeksNum = 0,
                calendarData = []

            // monthが今月の場合selectedDateを今日の日付に
            // monthが今月以外の場合selectedDateをmonthの月初の日付に変更する
            if (moment(month).format('YYYY-MM') === moment().format('YYYY-MM')){
                selectedDateLabel = moment().format('YYYY-MM-DD')
            } else {
                selectedDateLabel = moment(month).startOf('month').format('YYYY-MM-DD')
            }

            // 月の日数
            const monthDays = moment(month).daysInMonth()
            // 月初の曜日
            const firstDay = moment(month).startOf('month').day()

            // 週数を割り出し
            weeksNum = Math.ceil((monthDays + firstDay) / 7)

            // 月初より前の日付データ(週の頭まで)をcalendarDataへ追加
            for(let i = 0; i < firstDay; i++){
                const day = moment(month).startOf('month').subtract(i + 1, 'days')
                calendarData.unshift({
                    date: day.format('YYYY-MM-DD'),
                    dateNum: day.date(),
                    schedules: [],
                    isSelectedMonth: false,
                })
            }


            // 選択月の日付データをcalendarDataへ追加
            for (let i = 0; i < monthDays; i++) {
                const day = moment(month).startOf('month').add(i, 'days')
                calendarData.push({
                    date: day.format('YYYY-MM-DD'),
                    dateNum: day.date(),
                    schedules: [],
                    isSelectedMonth: true
                })
            }


            // 月末より後の日付データ(週の最後まで)を配列へ追加
            for (let length = calendarData.length, i = 1; length < weeksNum * 7; length++, i++){
                const momentObj = moment(month).endOf('month').add(i, 'days')
                calendarData.push({
                    date: momentObj.format('YYYY-MM-DD'),
                    dateNum : momentObj.date(),
                    schedules: [],
                    isSelectedMonth: false,
                })
            }

            // calendarData内のschedules配列にスケジュールを追加
            calendarData.forEach((dateData) => {
                if (schedulesList[dateData.date]){
                    dateData.schedules = schedulesList[dateData.date]
                }
            })

            return {
                selectedDateLabel: selectedDateLabel,
                weeksNum: weeksNum,
                calendarData: calendarData
            }
        },
        /**
         * スケジュールリストを更新
         * @param {Object} scheduleToDelete 更新前のスケジュール(削除するスケジュール)
         * @param {Object} scheduleToAdd 更新後のスケジュール(追加するスケジュール)
         * @param {Object} schedulesList スケジュールリスト
         * @return {Object} 更新後のスケジュールリストオブジェクト
         */
        mixinUpdateScheduleInSchedulesList(scheduleToDelete, scheduleToAdd, schedulesList) {
            const deletedResultScheduleList = this.mixinRemoveScheduleFromScheduleList(scheduleToDelete, schedulesList)
            return  this.mixinAddScheduleToSchedulesList(scheduleToAdd, deletedResultScheduleList)

        },
        /**
         * スケジュールリストにスケジュールを追加
         * @param {Object} schedule 追加するスケジュール
         * @param  {Object} schedulesList スケジュールリスト
         * @return {Object} newSchedulesList スケジュールを追加したスケジュールリスト
         */
        mixinAddScheduleToSchedulesList(schedule, schedulesList){

            const newSchedulesList = Array.isArray(schedulesList) ? {} : _.cloneDeep(schedulesList)

            // スケジュールをスケジュールリストデータに追加
            // 登録した日にスケジュールがない場合
            if (newSchedulesList[schedule.date] === undefined || !newSchedulesList[schedule.date].length){
                newSchedulesList[schedule.date] = [schedule]
                return newSchedulesList
            }

            // 登録したしたスケジュールに時間指定がない場合先頭に追加
            if(schedule.time === null) {
                newSchedulesList[schedule.date].unshift(schedule)
                return newSchedulesList
            }

            // 前後スケジュールが登録されている場合
            // 時間順に並ぶようにデータを追加
            for (let i = 0; i < newSchedulesList[schedule.date].length; i++) {
                if (newSchedulesList[schedule.date][i].time === null) {
                    continue
                }
                if(newSchedulesList[schedule.date][i].time >= schedule.time) {
                    newSchedulesList[schedule.date].splice(i, 0, schedule)
                    return newSchedulesList
                }
            }
            // 上記以外の場合最後にデータを追加
            newSchedulesList[schedule.date].push(schedule)
            return newSchedulesList
        },
        /**
         * スケジュールリストからスケジュールを削除
         * @param {Object} schedule 削除するスケジュール
         * @param {Object} schedulesList スケジュールリスト
         * @return {Object} newScheduleList スケジュールを削除したスケジュールリスト
         */
        mixinRemoveScheduleFromScheduleList(schedule, schedulesList) {

            const newSchedulesList = _.cloneDeep(schedulesList)
            for(let i = 0; i < newSchedulesList[schedule.date].length; i++) {
                if (newSchedulesList[schedule.date][i].id === schedule.id){
                    newSchedulesList[schedule.date].splice(i, 1)
                    break
                }
            }
            // 変更したスケジュールリストデータを返却
            return newSchedulesList
        },
    }
}
