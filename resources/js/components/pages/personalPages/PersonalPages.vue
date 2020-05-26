<template>
    <div>
        <v-navigation-drawer app  clipped
            :value="drawer" @input="$emit('changeDrawer', $event)"
        >
            <v-list dense nav>
                <v-list-item :to="{name: 'personalCalendar'}">
                    <v-list-item-icon class="mr-4">
                        <v-icon>mdi-calendar</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title class="subtitle-1">マイカレンダー</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item :to="{name: 'sharedCalendarList'}">
                    <v-list-item-icon class="mr-4">
                        <v-icon>mdi-calendar-multiple</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title class="subtitle-1">共有カレンダー一覧</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item :to="{name: 'createSharedCalendar'}">
                    <v-list-item-icon class="mr-4">
                        <v-icon>mdi-calendar-edit</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title class="subtitle-1">共有カレンダー作成</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item :to="{name: 'searchSharedCalendar'}">
                    <v-list-item-icon class="mr-4">
                        <v-icon>mdi-calendar-search</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title class="subtitle-1">共有カレンダー検索</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

            </v-list>
        </v-navigation-drawer>

        <router-view
            ref="routerView"
            :shared-calendar-list="sharedCalendarList"
            :schedules-data="schedulesData"
            :fetch-schedules="fetchSchedules"
            @changeSchedulesData="changeSchedulesData"
        >
        </router-view>
    </div>
</template>

<script>
    import {SUCCESS} from "../../../util";
    import schedulesAndCalendarMethodsMixin from "../../../mixins/schedulesAndCalendarMethodsMixin"
    export default {
        name: "PersonalPages.vue",
        mixins: [schedulesAndCalendarMethodsMixin],
        data() {
            return {
                // スケジュールデータ
                schedulesData: {
                    schedulesYear: null,
                    schedules: [],
                },
                // 参加共有カレンダーリスト
                sharedCalendarList: {
                    data: [],
                    loadingFlg: false
                },
            }
        },
        props:{
            drawer:{
                type: Boolean,
            }
        },
        methods: {
            /**
             * スケジュールデータの取得
             * @param {String} year スケジュールを取得したい年 'YYYY'
             * @param {boolean} isEmit コンポーネント子コンポーネントから呼び出す場合はtrue
             * @return {Promise<boolean>}
             */
            async fetchSchedules(year, isEmit = false){
                // year年1月1日のmomentオブジェクトと曜日
                const firstDay = moment().year(year).startOf('year')
                const firstDayWeek = firstDay.day()

                // year年12月31日のmomentオブジェクトと曜日
                const lastDay = moment().year(year).endOf('year')
                const lastDayWeek = lastDay.day()

                // 取得するスケジュールの先頭と末尾の日付
                const from = firstDay.subtract(firstDayWeek, 'day').format('YYYY-MM-DD')
                const until = lastDay.add(6 - lastDayWeek, 'day').format('YYYY-MM-D')

                isEmit && this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.get('/api/schedules/' + from + '/' + until)
                isEmit && this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    this.schedulesData.schedulesYear = year
                    this.schedulesData.schedules = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * 参加共有カレンダーの取得
             */
            async fetchSharedCalendarList () {
                this.sharedCalendarList.loadingFlg = true

                const response = await axios.get('/api/shared-calendars/list')

                if(response.status === SUCCESS) {
                    this.sharedCalendarList.data = response.data
                    this.sharedCalendarList.loadingFlg = false
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * スケジュールデータを変更する
             * @param {Object} data
             */
            changeSchedulesData(data) {
                if (data.schedulesYear){
                    this.schedulesData.schedulesYear = data.schedulesYear
                }
                if (data.schedules){
                    this.schedulesData.schedules = data.schedules
                }
            },
        },
        /**
         * スケジュールデータと参加共有カレンダーリストを取得
         */
        async created() {
            this.schedulesData.schedulesYear = (moment().format('YYYY'))

            this.$store.commit('loading/setLoadingFlg', true)
            await Promise.all([
                this.fetchSchedules(this.schedulesData.schedulesYear),
                this.fetchSharedCalendarList()
            ])
            this.$store.commit('loading/setLoadingFlg', false)
        }
    }
</script>

<style scoped>

</style>
