<template>
    <div>
        <SideBar />
        <div class="my-calendar">
            <h1>MyCalendar</h1>
            <div class="calendar">
                <table>
                    <caption>{{ dateLabel }}</caption>
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
                        <tr v-for="row in 5">
                            <th v-for="column in 7">{{ dates[(row - 1) * 7  + column - 1 ] }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import SideBar from "../components/SideBar"
    import moment from "moment"
    export default {
        name: "MyCalendar",
        components: {SideBar},
        data() {
            return {
                dates: [], // 日付配列(選択月前後35日分)
                dateLabel: '', // 選択中の年月表示用(YYYY年MM月)
                selectedMonth: null, // 選択中の月(momentオブジェクト)
            }
        },
        created() {
            // 現在月を設定
            this.selectedMonth = moment()
        },
        watch: {
            selectedMonth: function () {
                // 日付配列の初期化、選択中の年月設定
                this.dates = []
                this.dateLabel = moment(this.selectedMonth).format('YYYY年MM月')

                // 選択月初日の曜日
                let firstDay = moment(this.selectedMonth).startOf('month').day()
                // 選択月初日より前の日付データ(選択月前月)を配列へ追加
                for(let i = 0; i < firstDay; i++){
                    this.dates.unshift(moment(this.selectedMonth).startOf('month').subtract(i + 1, 'days').date())
                }


                // 選択月の日付データを配列へ追加
                for (let i = 0; i < moment(this.selectedMonth).daysInMonth(); i++) {
                    this.dates.push(i + 1)
                }


                // 選択月末日より後の日付データを配列へ追加
                for (let length = this.dates.length, i = 1; length < 35; length++, i++){
                    this.dates.push(moment(this.selectedMonth).endOf('month').add(i, 'days').date())
                }

            }
        }
    }
</script>

<style scoped>
.my-calendar{
    max-width: 500px;
    margin: 0 auto;
}
</style>
