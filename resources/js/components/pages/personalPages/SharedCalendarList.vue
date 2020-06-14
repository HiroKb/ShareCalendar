<template>
    <v-container class="fixed-content py-12">
        <v-card class="fill-height">
            <template v-if="!sharedCalendarList.loadingFlg">
                <v-card-title v-if="!sharedCalendarList.data.length">共有しているカレンダーがありません</v-card-title>
                <div
                    class="fill-height d-flex flex-column"
                    v-else
                >
                    <v-card-title class="flex-grow-0 flex-shrink-0">共有カレンダー一覧</v-card-title>
                    <v-card-text
                        class="flex-grow-1 flex-shrink-1 custom-scrollbar"
                        style="overflow-y: scroll;"
                    >
                        <v-list
                            v-if="adminCalendar.length"
                            nub
                        >
                            <v-subheader>管理カレンダー</v-subheader>
                            <v-list-item
                                v-for="(calendar, index) in adminCalendar"
                                :key="index"
                                :to="{name: 'sharedCalendar', params:{sharedCalendarId: calendar.id}}"
                                link
                            >
                                <v-list-item-avatar>
                                    <v-img :src=" calendar.image_url || mixinNoImagePath"></v-img>
                                </v-list-item-avatar>
                                <v-list-item-content>
                                    <v-list-item-title>{{calendar.calendar_name}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                        <v-divider v-if="adminCalendar.length && memberCalendar.length"></v-divider>
                        <v-list v-if="memberCalendar.length">
                            <v-subheader>参加カレンダー</v-subheader>
                            <v-list-item
                                v-for="(calendar, index) in memberCalendar"
                                :key="index"
                                :to="{name: 'sharedCalendar', params:{sharedCalendarId: calendar.id}}"
                                link
                            >
                                <v-list-item-avatar>
                                    <v-img :src=" calendar.image_url || mixinNoImagePath"></v-img>
                                </v-list-item-avatar>
                                <v-list-item-content>
                                    <v-list-item-title>{{calendar.calendar_name}}</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </div>
            </template>
        </v-card>
    </v-container>
</template>

<script>
    import {mapGetters} from "vuex";
    import utilDataMixin from "../../../mixins/utilDataMixin";
    export default {
        name: "SharedCalendar",
        mixins: [utilDataMixin],
        props: {
            // 参加共有カレンダーリスト
            sharedCalendarList: {
                type: Object,
                required: true,
                default: {}
            }
        },
        computed: {
            ...mapGetters({
                userId: 'user/userId'
            }),
            /**
             * 管理カレンダー共有カレンダー
             * @return {Array}
             */
            adminCalendar() {
                return this.sharedCalendarList.data.filter((calendar) => {
                    return calendar.admin_id === this.userId
                })
            },
            /**
             * メンバーとして共有しているカレンダー
             * @return {Array}
             */
            memberCalendar() {
                return this.sharedCalendarList.data.filter((calendar) => {
                    return calendar.admin_id !== this.userId
                })
            }
        }
    }
</script>

<style scoped>
    .container{
        max-width: 600px;
    }
</style>
