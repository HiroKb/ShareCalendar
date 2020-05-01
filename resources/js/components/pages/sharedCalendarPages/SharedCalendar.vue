<template>
    <div>
        <div class="sidebar-wrap">
            <SideBar/>
            <div class="shared-calendar-menu">
                <p>{{ sharedCalendarData.calendar_name }}</p>
                <router-link :to="{name: 'sharedCalendarIndex', params:{sharedCalendarId: sharedCalendarId}}" class="shared-calendar-link">
                    カレンダー
                </router-link>
                <router-link :to="{name: 'sharedCalendarMembers', params:{sharedCalendarId: sharedCalendarId}}" class="shared-calendar-link">
                    共有メンバー一覧
                </router-link>
                <router-link :to="{name: 'sharedCalendarApplicants', params:{sharedCalendarId: sharedCalendarId}}" v-if="adminFlg" class="shared-calendar-link">
                    共有申請者一覧
                </router-link>
                <router-link :to="{name: 'sharedCalendarInfo'}" v-if="adminFlg">
                    共有カレンダー情報/変更
                </router-link>

                <router-link :to="{name: 'unShareCalendar'}" v-if="!adminFlg" class="shared-calendar-link">
                    共有解除
                </router-link>
            </div>
        </div>
        <router-view :shared-calendar-id="sharedCalendarId"
                     :shared-calendar-data="sharedCalendarData"
                     :shared-calendar-members="sharedCalendarMembers"
                     :shared-calendar-applicants="sharedCalendarApplicants"
                     :shared-schedules-data="sharedSchedulesData"
                     @unShareMember="removeMember"
                     @allowApplication="allowApplications"
                     @rejectApplication="removeApplicants"
                     @changeCalendarData="changeCalendarData"
                     @changeSchedulesData="changeSchedulesData"
        >
        </router-view>
    </div>

</template>

<script>
    import SideBar from "../../modules/SideBar"
    import {SUCCESS} from "../../../util";
    import {mapGetters} from "vuex";
    import moment from "moment";
    export default {
        name: "SharedCalendar",
        components: {SideBar},
        data () {
            return {
                sharedCalendarData: {
                    id: '',
                    admin_id: '',
                    calendar_name: ''
                },
                sharedCalendarMembers: [],
                sharedCalendarApplicants: [],
                sharedSchedulesData: {
                    schedulesYear: null,
                    schedules: [],
                }
            }
        },
        props: {
            sharedCalendarId: {
                required: true
            },
        },
        computed: {
            ...mapGetters({
                userId: 'user/userId'
            }),
            adminFlg: function () {
                return this.sharedCalendarData.admin_id === this.userId
            }
        },
        methods: {
            async fetchSharedCalendar() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId)

                if(response.status === SUCCESS) {
                    this.sharedCalendarData = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            async fetchSharedCalendarMembers() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/members')

                if(response.status === SUCCESS) {
                    this.sharedCalendarMembers = response.data

                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            async fetchSharedCalendarApplicants() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/applications')

                if(response.status === SUCCESS) {
                    this.sharedCalendarApplicants = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            allowApplications (userList) {
                this.addMembers(userList)
                this.removeApplicants(userList)
            },
            addMembers (userList) {
                userList.forEach((user) => {
                    this.sharedCalendarMembers.push(user)
                })
            },
            removeMember (memberId) {
                for (let i = 0; i < this.sharedCalendarMembers.length; i++) {
                    if (memberId === this.sharedCalendarMembers[i].id){
                        this.sharedCalendarMembers.splice(i, 1)
                        break
                    }
                }
            },
            removeApplicants (userList) {
                userList.forEach((removedUser) => {
                    for (let i = 0; i < this.sharedCalendarApplicants.length; i++){
                        if (this.sharedCalendarApplicants[i].id === removedUser.id) {
                            this.sharedCalendarApplicants.splice(i, 1)
                            break
                        }
                    }
                })
            },
            changeCalendarData(data) {
                this.sharedCalendarData = data
            },
            changeSchedulesData(data) {
                if (data.schedulesYear){
                    this.sharedSchedulesData.schedulesYear = data.schedulesYear
                }
                if (data.schedules){
                    this.sharedSchedulesData.schedules = data.schedules
                }
            },
        },
        created() {
            Promise.all([
                this.fetchSharedCalendar(),
                this.fetchSharedCalendarMembers()
            ]).then(x => {
                if (this.adminFlg) {
                    this.fetchSharedCalendarApplicants()
                }
            })
        },
    }
</script>

<style scoped>
    .sidebar-wrap{
        display: flex;
    }
    .shared-calendar-link{
        display: block;
    }
</style>
