<template>
    <div>
        <div class="sidebar-wrap">
            <SideBar/>
            <div class="shared-calendar-menu">
                <router-link :to="{name: 'sharedCalendar', params:{sharedCalendarId: sharedCalendarId}}">
                    <p>共有カレンダーTOP</p>
                </router-link>
                <router-link :to="{name: 'sharedCalendarMembers', params:{sharedCalendarId: sharedCalendarId}}">
                    共有メンバー一覧
                </router-link>
            </div>
        </div>
        <div class="contents">
            <p v-if="loadingFlg">読み込み中</p>
            <template v-else>
                <div v-if="sharedCalendarApplicants.length">
                    <div v-for="applicant in sharedCalendarApplicants" :key="applicant.id">
                        <p>{{ applicant.name }}</p>
                        <button @click="showAllowModal(applicant)">
                            <i class="fas fa-check"></i>
                        </button>
                        <button @click="showRejectModal(applicant)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <button @click="showAllowAllModal">全て許可</button>
                    <button @click="showRejectAllModal">全て拒否</button>
                </div>
                <p v-else>
                    現在共有申請者はいません。
                </p>
            </template>
        </div>
        <div class="modal-background" v-show="modalFlg" @click="hideModal">
            <div class="modal" >
                <div class="modal-inner" @click.stop>
                    <i class="fas fa-times" @click="hideModal"></i>

                    <form @submit.prevent="allowApplication" v-show="allowModalFlg">
                        <p>{{ applicantName }}</p>
                        <p>共有申請を許可しますか</p>
                        <button>許可</button>
                    </form>

                    <form @submit.prevent="allowAllApplication" v-show="allowAllModalFlg">
                        <p v-for="applicants in sharedCalendarApplicants" :key="applicants.id">
                            {{ applicants.name}}
                        </p>
                        <p>全ての共有申請を許可しますか</p>
                        <button>許可</button>
                    </form>

                    <form @submit.prevent="rejectApplication" v-show="rejectModalFlg">
                        <p>{{ applicantName }}</p>
                        <p>共有申請を拒否しますか</p>
                        <button>拒否</button>
                    </form>

                    <form @submit.prevent="rejectAllApplication" v-show="rejectAllModalFlg">
                        <p v-for="applicants in sharedCalendarApplicants" :key="applicants.id">
                            {{ applicants.name}}
                        </p>
                        <p>全ての共有申請を拒否しますか</p>
                        <button>拒否</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {CREATED, SUCCESS} from "../util"
    import SideBar from "../components/SideBar";
    export default {
        name: "SharedCalendarApplicants",
        components: {SideBar},
        data () {
            return {
                sharedCalendarApplicants:{},
                loadingFlg: true,
                modalFlg: false,
                allowModalFlg: false,
                allowAllModalFlg: false,
                rejectModalFlg: false,
                rejectAllModalFlg: false,
                applicantData: null
            }
        },
        computed: {
            applicantName: function () {
                return this.applicantData ? this.applicantData.name : ''
            }
        },
        props: {
            sharedCalendarId: {
                required: true
            },
        },
        methods: {
            async fetchSharedCalendarApplicants() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/applications')

                if(response.status === SUCCESS) {
                    this.sharedCalendarApplicants = response.data

                    this.loadingFlg = false
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            async allowApplication() {
                if (!this.sharedCalendarId || !this.applicantData.id) {
                    return false
                }
                const response = await axios.put('/api/shared-calendars/' + this.sharedCalendarId + '/applications/' + this.applicantData.id)

                if (response.status === CREATED) {
                    for (let i = 0; i < this.sharedCalendarApplicants.length; i++) {
                        if (response.data.id === this.sharedCalendarApplicants[i].id){
                            this.sharedCalendarApplicants.splice(i, 1)
                            break
                        }
                    }
                    this.hideModal()
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            async allowAllApplication() {
                if (!this.sharedCalendarId) {
                    return false
                }
                const data = {'id_list': this.sharedCalendarApplicants.map(function(applicant){
                    return applicant.id
                    })}

                const response = await axios.put('/api/shared-calendars/' + this.sharedCalendarId + '/applications/all', data)
                if (response.status === CREATED) {
                    this.sharedCalendarApplicants = {}
                    this.hideModal()
                    return false
                }
                this.$store.commit('error/setCode', response.status)
            },
            async rejectApplication() {
                if (!this.sharedCalendarId || !this.applicantData.id) {
                    return false
                }
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarId + '/applications/' + this.applicantData.id)

                if (response.status === SUCCESS) {
                    for (let i = 0; i < this.sharedCalendarApplicants.length; i++) {
                        if (response.data.id === this.sharedCalendarApplicants[i].id){
                            this.sharedCalendarApplicants.splice(i, 1)
                            break
                        }
                    }
                    this.hideModal()
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            async rejectAllApplication() {
                if (!this.sharedCalendarId) {
                    return false
                }
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarId + '/applications/all')
                if (response.status === SUCCESS) {
                    this.sharedCalendarApplicants = {}
                    this.hideModal()
                    return false
                }
                this.$store.commit('error/setCode', response.status)
            },
            showAllowModal(applicant) {
                this.applicantData = applicant
                this.modalFlg = true
                this.allowModalFlg = true
            },
            showAllowAllModal() {
                this.modalFlg = true
                this.allowAllModalFlg= true
            },
            showRejectModal(applicant) {
                this.applicantData = applicant
                this.modalFlg = true
                this.rejectModalFlg = true
            },
            showRejectAllModal() {
                this.modalFlg = true
                this.rejectAllModalFlg= true
            },
            hideModal() {
                this.modalFlg = false
                this.allowModalFlg = false
                this.allowAllModalFlg = false
                this.rejectModalFlg = false
                this.rejectAllModalFlg = false

                this.applicantData = null
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
    .modal-background{
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 100vw ;
        z-index: 10;
        background: rgba(0, 0, 0, .1);
    }

    .modal{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        width: 100vw ;
    }
    .modal-inner{
        background: #ffffff;
    }
</style>
