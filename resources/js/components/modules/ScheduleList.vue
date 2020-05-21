<template>
        <v-list>
            <template v-for="(schedule,index) in schedules">
                <v-list-item :key="index">
                    <v-list-item-content>
                        <v-list-item-title v-if="schedule.time">{{utilMethods.timeFormatter(schedule.time)}}</v-list-item-title>
                        <v-list-item-title>{{schedule.title}}</v-list-item-title>
                    </v-list-item-content>

                    <template v-if="!isPersonal || (isPersonal && !schedule.calendar_id)">
                        <v-list-item-action>
                            <v-btn
                                x-small fab :color="colors.themeColor" outlined
                                @click="showEditModal(schedule)"
                            >
                                <v-icon>mdi-file-document-edit-outline</v-icon>
                            </v-btn>
                        </v-list-item-action>
                        <v-list-item-action>
                            <v-btn
                                x-small fab :color="colors.themeColor" outlined
                                @click="showDeleteModal(schedule)"
                            >
                                <v-icon>mdi-trash-can-outline</v-icon>
                            </v-btn>
                        </v-list-item-action>
                    </template>

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
            schedules: {
                type: Array,
                required: true,
                default: () => ([])
            },
            isPersonal: {
                type: Boolean,
                required: true,
                default: false
            }
        },
        methods: {
            showEditModal(schedule) {
                this.$emit('showEditModalRequest', schedule)
            },
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
