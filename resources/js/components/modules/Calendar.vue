<template>
        <v-card flat tile class="d-flex flex-column align-center">
            <v-card-title>
                <v-icon x-large class="my-0"@click="changeSelectedMonth(-1)">mdi-menu-left</v-icon>
                <p class="my-0">{{ dateLabel }}</p>
                <v-icon x-large class="my-0" @click="changeSelectedMonth(1)">mdi-menu-right</v-icon>
            </v-card-title>
            <v-card-text>
                    <table>
                        <thead>
                            <tr>
                                <th class="cell-outer" v-for="day in dayOfTheWeek" :key="day">
                                    <v-card class="cell-inner d-flex flex-column align-center justify-center" flat tile>
                                        <v-card-title class="pa-0">{{ day }}</v-card-title>
                                    </v-card>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in weeks" :key="row">
                                <td
                                    class="cell-outer"
                                    v-for="col in 7"
                                    :key="row * col"
                                    :data-date="calendarData[(row - 1) * 7 + col - 1].date"
                                    @click="changeSelectedDate"
                                >
                                    <v-card class="cell-inner d-flex flex-column align-center justify-space-around" flat tile>
                                        <v-card-title class="pa-0">{{ calendarData[(row - 1) * 7  + col - 1 ].dateNum }}</v-card-title>
                                        <v-card-text class="text-center subtitle-1 pa-0">{{ scheduleNumberData[(row - 1) * 7 + col - 1 ].schedules.length}}</v-card-text>
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
            dateLabel:{
                type: String,
                required: true,
                default:''
            },
            weeks: {
                type: Number,
                required: true,
                default: 0
            },
            calendarData: {
                type: Array,
                required: true,
                default: []
            },
            scheduleNumberData: {
                type: Array,
                required: true,
                default: []
            }
        },
        methods: {
            changeSelectedMonth(num){
                this.$emit('changeSelectedMonthRequest', num)
            },
            changeSelectedDate(e) {
                this.$emit('changeSelectedDateRequest', e.currentTarget.dataset.date)
            }
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
