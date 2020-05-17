<template>
    <div style="height: 100%;">
        <v-navigation-drawer app  clipped
            :value="drawer" @input="$emit('changeDrawer', $event)"
        >
            <v-list dense nav>
                <v-list-item :to="{name: 'myCalendar'}">
                    <v-list-item-content>
                        マイカレンダー
                    </v-list-item-content>
                </v-list-item>
                <v-list-item :to="{name: 'sharedCalendarList'}">
                    <v-list-item-content>
                        共有カレンダー一覧
                    </v-list-item-content>
                </v-list-item>
                <v-list-item :to="{name: 'createSharedCalendar'}">
                    <v-list-item-content>
                        共有カレンダー作成
                    </v-list-item-content>
                </v-list-item>
                <v-list-item :to="{name: 'searchSharedCalendar'}">
                    <v-list-item-content>
                        共有カレンダー検索
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
        <router-view :shared-calendar-list="sharedCalendarList"
                     :schedulesData="schedulesData"
                     @changeSchedulesData="changeSchedulesData"
        >
        </router-view>
    </div>
</template>

<script>
    import {SUCCESS} from "../../../util";
    export default {
        name: "PersonalPages.vue",
        data() {
            return {
                schedulesData: {
                    schedulesYear: null,
                    schedules: []
                },
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
            changeSchedulesData(data) {
                if (data.schedulesYear){
                    this.schedulesData.schedulesYear = data.schedulesYear
                }
                if (data.schedules){
                    this.schedulesData.schedules = data.schedules
                }
            },
        },
        created() {
            this.fetchSharedCalendarList()
        }
    }
</script>

<style scoped>

</style>
