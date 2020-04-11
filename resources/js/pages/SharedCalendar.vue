<template>
    <div>
        <div class="sidebar-wrap">
            <SideBar/>
            <div class="shared-calendar-menu">
                <router-link :to="{name: 'sharedCalendarApplicants', params:{sharedCalendarId: sharedCalendar.id}}" v-if="sharedCalendar.adminFlg">
                    共有申請者一覧
                </router-link>
                <router-link :to="{name: 'sharedCalendarMembers', params:{sharedCalendarId: sharedCalendar.id}}">
                    共有メンバー一覧
                </router-link>
            </div>
        </div>
        <div class="contents">
            <h1>SharedCalendar</h1>
            <p>{{ sharedCalendar.name }}</p>
            <p>{{ sharedCalendar.invitationUrl }}</p>
            <p>{{ sharedCalendar.searchId }}</p>
        </div>
    </div>
</template>

<script>
    import {SUCCESS} from "../util"
    import {mapGetters} from 'vuex'
    import SideBar from "../components/SideBar";
    export default {
        name: "SharedCalendar",
        components: {SideBar},
        data () {
            return {
                sharedCalendarData:{
                },
                invitationPath: location.protocol + '//' + location.host + '/shared-calendar/application/'
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
            sharedCalendar: function () {
                return {
                    adminFlg: this.sharedCalendarData.admin_id ? this.sharedCalendarData.admin_id === this.userId : false,
                    id: this.sharedCalendarData.id ? this.sharedCalendarData.id : '',
                    invitationUrl: this.sharedCalendarData.search_id ? this.invitationPath + this.sharedCalendarData.search_id : '',
                    searchId: this.sharedCalendarData.search_id ? this.sharedCalendarData.search_id : '',
                    name: this.sharedCalendarData.calendar_name ? this.sharedCalendarData.calendar_name : '',
                }
            }
        },
        methods: {
            async fetchSharedCalendar() {
                const response = await axios.get('/api/shared-calendar/' + this.sharedCalendarId)

                if(response.status === SUCCESS) {
                    this.sharedCalendarData = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            }
        },
        created() {
            this.fetchSharedCalendar()
        }
    }
</script>

<style scoped>
    .sidebar-wrap{
        display: flex;
    }
</style>
