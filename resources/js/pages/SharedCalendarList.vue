<template>
    <div>
        <div class="contents">
            <h1>SharedCalendarList</h1>
            <p v-if="loadingFlg">読み込み中</p>

            <template v-else>
                <ul v-if="sharedCalendarList.length">
                    <li v-for="sharedCalendar in sharedCalendarList">
                        <router-link :to="{name: 'sharedCalendarIndex', params:{sharedCalendarId: sharedCalendar.id}}">
                            <p>{{sharedCalendar.calendar_name}}</p>
                        </router-link>
                    </li>
                </ul>

                <p v-else>まだ共有しているカレンダーがありません。</p>
            </template>

        </div>
    </div>
</template>

<script>
    import SideBar from "../components/SideBar";
    export default {
        name: "SharedCalendar",
        components: {SideBar},
        data() {
            return {
                sharedCalendarList: [],
                loadingFlg: false
            }
        },
        methods: {
            async fetchSharedCalendarList () {
                this.loadingFlg = true

                const response = await axios.get('/api/shared-calendars/list')
                this.sharedCalendarList = response.data

                this.loadingFlg = false
            },
        },
        created() {
            this.fetchSharedCalendarList()
        }
    }
</script>

<style scoped>

</style>
