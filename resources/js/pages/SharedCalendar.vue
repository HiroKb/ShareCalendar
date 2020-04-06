<template>
    <div>
        <div class="sidebar-wrap">
            <SideBar/>
            <div class="shared-calendar-menu">

            </div>
        </div>
        <div class="shared-calendar">
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
                sharedCalendarData:{},
                invitationPath: location.protocol + '//' + location.host + '/shared-calendar/join/'
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
                    adminFlg: this.sharedCalendarData.admin_id === this.userId,
                    invitationUrl: this.sharedCalendarData.search_id ? this.invitationPath + this.sharedCalendarData.search_id : '',
                    searchId: this.sharedCalendarData.search_id ? this.sharedCalendarData.search_id : '',
                    name: this.sharedCalendarData.calendar_name ? this.sharedCalendarData.calendar_name : '',
                }
            }
        },
        methods: {
            async fetchSharedCalendar() {
                const response = await axios.get('/api/shared-calendar/' + this.sharedCalendarId)

                console.log(response)
                if(response.status === SUCCESS) {
                    this.sharedCalendarData = response.data
                    console.log(this.sharedCalendarData)
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
        position: fixed;
        z-index: 1;
    }
    .shared-calendar{
        max-width: 800px;
        margin: 0 auto;
    }
</style>
