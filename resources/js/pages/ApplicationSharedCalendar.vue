<template>
    <div>
        <div class="sidebar-wrap">
            <SideBar />
        </div>
        <div class="contents">
            <h1>JoinSharedCalendar</h1>
            <template v-if="sharedCalendarData.status">

                <form v-if="sharedCalendarData.status === 'NotShared'" @submit.prevent="ApplicationSharedCalendar">
                    <p>管理者: {{sharedCalendarData.admin_name}}</p>
                    <button type="submit">共有申請</button>
                </form>
                <p v-else-if="sharedCalendarData.status === 'Shared'">共有済みのカレンダーです。</p>
                <p v-else>共有カレンダーが見つかりません。</p>
            </template>
            <p v-else>読み込み中</p>
        </div>
    </div>
</template>

<script>
    import SideBar from "../components/SideBar"
    import {CREATED, SUCCESS} from "../util";
    export default {
        name: "ApplicationSharedCalendar",
        components: {SideBar},
        data() {
            return {
                sharedCalendarData: {}
            }
        },
        props: {
            searchId: {
                required: true,
                default: ''
            }
        },
        methods: {
            async ApplicationSharedCalendar () {
                const response = await axios.post('/api/shared-calendar/application', {'search_id': this.sharedCalendarData.search_id})
                if (response.status === CREATED) {
                    this.$router.push('/my-calendar')
                    return false
                }
                this.$store.commit('error/setCode', response.status)
            },
            async searchSharedCalendar () {
                const response =  await axios.get('/api/shared-calendar/search/' + this.searchId)

                if (response.status === SUCCESS) {

                    this.sharedCalendarData = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            }
        },
        created() {
            this.searchSharedCalendar()
        }
    }
</script>

<style scoped>

</style>
