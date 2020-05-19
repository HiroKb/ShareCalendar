<template>
    <v-sheet class="pt-4 pb-5 px-6">
        <p class="schedule-label">日付</p>
        <p class="schedule-data">{{ displayData.date }}</p>
        <p class="schedule-label">時刻</p>
        <p class="schedule-data">{{ displayData.time }}</p>
        <p class="schedule-label">スケジュール名</p>
        <p class="schedule-data">{{ displayData.title }}</p>
        <p class="schedule-label">詳細</p>
        <p class="schedule-data">{{ displayData.description }}</p>
        <v-btn class="my-0" block :color="colors.themeColor" dark
               @click="deleteSchedule"
        >スケジュール削除</v-btn>
    </v-sheet>
</template>

<script>
    import colorsMixin from "../../mixins/colorsMixin"
    import utilMethodsMixin from "../../mixins/utilMethodsMixin"
    export default {
        name: "DeleteScheduleFormCard",
        mixins: [colorsMixin, utilMethodsMixin],
        props: {
            scheduleData: {
                type: Object,
                require: true,
                default: () => ({})
            }
        },
        computed: {
            // 表示用データ
            displayData: function () {
                return {
                    date: this.scheduleData.date ? this.scheduleData.date : '',
                    time:  this.scheduleData.time ? this.utilMethods.timeFormatter(this.scheduleData.time) : '指定なし',
                    title: this.scheduleData ? this.scheduleData.title : '',
                    description: this.scheduleData.description ? this.scheduleData.description : 'なし'
                }
            }
        },
        methods: {
            deleteSchedule() {
                this.$emit('deleteScheduleRequest')
            }
        }
    }
</script>

<style scoped>
    .schedule-label{
        margin-bottom: 0;
    }
    .schedule-data{
        margin-bottom: 20px;
        font-size: 20px;
        word-wrap: break-word;
    }
</style>
