<template>
    <div>
        <div class="sidebar-wrap">
            <SideBar />
        </div>
        <router-view :shared-calendar-list="sharedCalendarList"
                     :schedulesData="schedulesData"
                     @changeSchedulesData="changeSchedulesData"
        >
        </router-view>
    </div>
</template>

<script>
    import SideBar from "../../modules/SideBar"
    import {SUCCESS} from "../../../util";
    import moment from "moment";
    export default {
        name: "PersonalPages.vue",
        components: {SideBar},
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
