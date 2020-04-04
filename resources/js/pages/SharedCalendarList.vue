<template>
    <div>
        <SideBar />
        <div class="shared-calendar-list">
            <h1>SharedCalendarList</h1>
            <p v-if="loadingFlg">読み込み中</p>

            <template v-else>
                <ul v-if="SharedCalendarList.length">
                    <li v-for="SharedCalendar in SharedCalendarList">
                        <router-link :to="{name: 'sharedCalendar', params:{sharedCalendarId: SharedCalendar.id}}">
                            <p>{{SharedCalendar.calendar_name}}</p>
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
                SharedCalendarList: [],
                loadingFlg: false
            }
        },
        methods: {
            async fetchSharedCalendarList () {
                this.loadingFlg = true

                const response = await axios.get('/api/shared-calendar/list')
                this.SharedCalendarList = response.data

                this.loadingFlg = false
            },
        },
        created() {
            this.fetchSharedCalendarList()
        }
    }
</script>

<style scoped>

    .shared-calendar-list{
        max-width: 800px;
        margin: 0 auto;
    }
</style>
