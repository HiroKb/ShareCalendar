<template>
    <v-card>
        <v-card-text class="pt-4">
            <p class="schedule-label">日付</p>
            <p class="schedule-data">{{ displayData.date }}</p>
            <p class="schedule-label">時刻</p>
            <p class="schedule-data">{{ displayData.time }}</p>
            <p class="schedule-label">スケジュール名</p>
            <p class="schedule-data">{{ displayData.title }}</p>
            <p class="schedule-label">詳細</p>
            <p class="schedule-data">{{ displayData.description }}</p>
            <v-btn class="my-0 font-weight-bold"
                   block :color="mixinThemeColor" dark
                   @click="deleteSchedule"
            >スケジュール削除</v-btn>
        </v-card-text>
    </v-card>
</template>

<script>
    import utilDataMixin from "../../mixins/utilDataMixin"
    import utilMethodsMixin from "../../mixins/utilMethodsMixin"
    export default {
        name: "DeleteScheduleFormCard",
        mixins: [utilDataMixin, utilMethodsMixin],
        props: {
            // 削除するスケジュールデータ
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
                    time:  this.scheduleData.time ? this.mixinTimeFormatter(this.scheduleData.time) : '指定なし',
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
        color: rgba(0, 0, 0 ,0.87);
    }
</style>
