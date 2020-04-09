<template>
    <div>
        <div class="sidebar-wrap">
            <SideBar/>
            <div class="shared-calendar-menu">
                <router-link :to="{name: 'sharedCalendar', params:{sharedCalendarId: sharedCalendarId}}">
                    <p>共有カレンダーTOP</p>
                </router-link>
            </div>
        </div>
        <div class="contents">
            <p v-if="loadingFlg">読み込み中</p>
            <template v-else>
                <div v-if="sharedCalendarApplicants.length">
                    <div v-for="applicant in sharedCalendarApplicants">
                        <p>{{ applicant.name }}</p>
                    </div>
                </div>
                <p v-else>
                    現在共有申請者はいません。
                </p>
            </template>
        </div>
    </div>
</template>

<script>
    import {SUCCESS} from "../util"
    import SideBar from "../components/SideBar";
    export default {
        name: "SharedCalendarApplicants",
        components: {SideBar},
        data () {
            return {
                sharedCalendarApplicants:{},
                loadingFlg: true
            }
        },
        props: {
            sharedCalendarId: {
                required: true
            },
        },
        methods: {
            async fetchSharedCalendarApplicants() {
                const response = await axios.get('/api/shared-calendar/' + this.sharedCalendarId + '/applicants')

                if(response.status === SUCCESS) {
                    this.sharedCalendarApplicants = response.data

                    this.loadingFlg = false
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            }
        },
        created() {
            this.fetchSharedCalendarApplicants()
        }
    }
</script>

<style scoped>
    .sidebar-wrap{
        display: flex;
    }
</style>
