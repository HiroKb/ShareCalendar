<template>
        <v-card class="d-flex flex-column align-center">
            <v-card-title>
<!--                カレンダーヘッダー-->
                <v-icon x-large class="my-0"@click="changeSelectedMonth(-1)">mdi-menu-left</v-icon>
                <p class="my-0">{{ selectedMonthLabel }}</p>
                <v-icon x-large class="my-0" @click="changeSelectedMonth(1)">mdi-menu-right</v-icon>
            </v-card-title>

            <v-card-text>
                    <table>
<!--                        曜日-->
                        <thead>
                            <tr>
                                <th class="cell-outer"
                                    v-for="(day, index) in dayOfTheWeek"
                                    :key="day">
                                    <v-card class="cell-inner d-flex flex-column align-center justify-center" flat tile>
                                        <v-card-title
                                            class="pa-0"
                                            :class="getWeekEndCSS(index + 1  )"
                                        >{{ day }}</v-card-title>
                                    </v-card>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="row in weeks" :key="row">
<!--                                １日分のセル-->
                                <td
                                    class="cell-outer"
                                    v-for="col in 7"
                                    :key="row * col"
                                    :data-date="getDateData(row, col).date"
                                    @click="changeSelectedDate"
                                >
                                    <v-card class="cell-inner d-flex flex-column align-center justify-space-around" flat tile
                                            :class="getDateData(row,  col).isSelectedMonth ? '' : 'grey lighten-4'"
                                    >
<!--                                        日付-->
                                        <v-card-title
                                            class="pa-0"
                                            :class="getWeekEndCSS(col)"
                                        >{{ getDateData(row, col).dateNum }}</v-card-title>
<!--                                        スケジュール数-->
                                        <v-card-text class="text-center subtitle-1 pa-0">{{ getDateData(row, col).schedules.length}}</v-card-text>
                                    </v-card>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </v-card-text>
        </v-card>
</template>

<script>
    export default {
        name: "Calendar",
        data() {
            return {
                dayOfTheWeek: [
                    '日', '月', '火', '水', '木', '金', '土'
                ]
            }
        },
        props: {
            // 選択月のmomentオブジェクト
            selectedMonth: {
                type: Object,
                required: true,
                default: () => ({})
            },
            // 週数
            weeks: {
                type: Number,
                required: true,
                default: 0
            },
            // カレンダーデータ
            calendarData: {
                type: Array,
                required: true,
                default: () => ([])
            },
        },
        computed: {
            // 表示用の選択月ラベル
            selectedMonthLabel: function () {
                return Object.keys(this.selectedMonth).length && moment(this.selectedMonth).format('YYYY年MM月')
            }
        },
        methods: {
            // 日付データを返却
            getDateData(row, col) {
                return this.calendarData[(row - 1) * 7 + col - 1 ]
            },
            // 土日の場合テキストカラークラスを返却
            getWeekEndCSS(col) {
                if (col === 1) {
                    return 'red--text'
                }
                if (col === 7) {
                    return 'blue--text'
                }
                return ''
            },
            /**
             * 選択月の変更後リクエストをエミット
             * @param {Number} num -1(前月) or 1(翌月)
             */
            changeSelectedMonth(num){
                this.$emit('changeSelectedMonthRequest', num)
            },
            /**
             * 選択日の変更リクエストをエミット
             * エミットデータ = 'YYYY-MM-DD'
             * @param {Object} e マウスイベント
             */
            changeSelectedDate(e) {
                this.$emit('changeSelectedDateRequest', e.currentTarget.dataset.date)
            },
        }
    }
</script>

<style scoped>
    table{
        width:100% ;
        border-collapse: collapse;
    }
    .cell-outer{
        position: relative;
        width: calc(100% / 7);
        padding-top: 13%;
        border: solid 1px black;
    }
    .cell-inner{
        position: absolute;
        top: 0;
        width: 100%;
        height: 100%;
    }
</style>
