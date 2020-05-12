<template>
    <div class="contents">
        <h1>JoinSharedCalendar</h1>
        <template v-if="sharedCalendarData.status">

            <form v-if="sharedCalendarData.status === 'NotShared'" @submit.prevent="applicationSharedCalendar">
                <p>管理者: {{sharedCalendarData.admin_name}}</p>
                <button type="submit">共有申請</button>
            </form>
            <p v-else-if="sharedCalendarData.status === 'Shared'">共有済みのカレンダーです。</p>
            <p v-else-if="sharedCalendarData.status === 'Applied'">共有申請済みのカレンダーです。</p>
            <p v-else>共有カレンダーが見つかりません。</p>
        </template>
        <p v-else>読み込み中</p>
    </div>
</template>

<script>
    import {CREATED, SUCCESS} from "../../../util";
    export default {
        name: "ApplicationSharedCalendar",
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
            async applicationSharedCalendar () {
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.put('/api/shared-calendars/' + this.sharedCalendarData.search_id + '/applications')
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === CREATED) {
                    this.$store.commit('flashMessage/setMessage', 'カレンダーの共有申請を行いました。')
                    this.$router.push({name: 'myCalendar'})
                    return false
                }
                this.$store.commit('error/setCode', response.status)
            },
            async searchSharedCalendar () {
                this.$store.commit('loading/setLoadingFlg', true)
                const response =  await axios.get('/api/shared-calendars/' + this.searchId + '/search')
                this.$store.commit('loading/setLoadingFlg', false)

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
