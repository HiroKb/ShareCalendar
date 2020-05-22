<template>
        <v-list>
            <template v-for="(schedule,index) in schedules">
<!--                スケジュール-->
                <v-list-item :key="index">
<!--                    スケジュール名・時刻-->
                    <v-list-item-content>
                        <v-list-item-title v-if="schedule.time">{{mixinUtilMethods.timeFormatter(schedule.time)}}</v-list-item-title>
                        <v-list-item-title>{{schedule.title}}</v-list-item-title>
                    </v-list-item-content>

<!--                    個人カレンダーで表示されている個人スケジュールの場合か-->
<!--                    共有カレンダーで表示されている場合は-->
<!--                    スケジュール更新・削除ボタンを表示-->
                    <template v-if="!isPersonal || (isPersonal && schedule.user_id)">
                        <v-list-item-action>
                            <v-btn
                                x-small fab :color="mixinThemeColor" outlined
                                @click="showEditModal(schedule)"
                            >
                                <v-icon>mdi-file-document-edit-outline</v-icon>
                            </v-btn>
                        </v-list-item-action>
                        <v-list-item-action>
                            <v-btn
                                x-small fab :color="mixinThemeColor" outlined
                                @click="showDeleteModal(schedule)"
                            >
                                <v-icon>mdi-trash-can-outline</v-icon>
                            </v-btn>
                        </v-list-item-action>
                    </template>

<!--                    個人カレンダーで表示されている共有スケジュールの場合-->
<!--                    共有カレンダーのリンクを表示-->
                    <v-list-item-action class="calendar-link-wrap" v-if="isPersonal && schedule.calendar_id">
                        <router-link class="calendar-link" :to="{name: 'sharedCalendar', params:{sharedCalendarId: schedule.calendar_id}}">{{ schedule.calendar_name }}</router-link>
                    </v-list-item-action>
                </v-list-item>
                <v-divider v-if="index + 1 < schedules.length"></v-divider>
            </template>
        </v-list>
</template>

<script>
    import colorsMixin from "../../mixins/colorsMixin"
    import utilMethodsMixin from "../../mixins/utilMethodsMixin"
    export default {
        name: "ScheduleList",
        mixins: [colorsMixin, utilMethodsMixin],
        props: {
            // スケジュール配列
            schedules: {
                type: Array,
                required: true,
                default: () => ([])
            },
            // 個人カレンダーか共有カレンダーかのフラグ
            isPersonal: {
                type: Boolean,
                required: true,
                default: false
            }
        },
        methods: {
            // スケジュール更新モーダルをエミット
            showEditModal(schedule) {
                this.$emit('showEditModalRequest', schedule)
            },
            // スケジュール削除モーダルをエミット
            showDeleteModal(schedule) {
                this.$emit('showDeleteModalRequest', schedule)
            }
        },
    }
</script>

<style scoped>
    .calendar-link-wrap{
        max-width: 40%;
    }
    .calendar-link{
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
