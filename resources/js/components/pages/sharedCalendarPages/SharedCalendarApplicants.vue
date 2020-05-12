<template>
    <div>
        <div class="contents">
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
        </div>
        <div class="modal-background" v-show="modalFlg" @click="hideModal">
            <div class="modal" >
                <div class="modal-inner" @click.stop>
                    <i class="fas fa-times" @click="hideModal"></i>

                    <form @submit.prevent="allowApplication([selectApplicantData])" v-show="allowModalFlg">
                        <p>{{ selectApplicantName }}</p>
                        <p>共有申請を許可しますか</p>
                        <button>許可</button>
                    </form>

                    <form @submit.prevent="allowApplication(allApplicantsData)" v-show="allowAllModalFlg">
                        <p v-for="applicants in sharedCalendarApplicants" :key="applicants.id">
                            {{ applicants.name}}
                        </p>
                        <p>全ての共有申請を許可しますか</p>
                        <button>許可</button>
                    </form>

                    <form @submit.prevent="rejectApplication([selectApplicantData])" v-show="rejectModalFlg">
                        <p>{{ selectApplicantName }}</p>
                        <p>共有申請を拒否しますか</p>
                        <button>拒否</button>
                    </form>

                    <form @submit.prevent="rejectApplication(allApplicantsData)" v-show="rejectAllModalFlg">
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
    import {CREATED, SUCCESS} from "../../../util"
    export default {
        name: "SharedCalendarApplicants",
        data () {
            return {
                modalFlg: false,
                allowModalFlg: false,
                allowAllModalFlg: false,
                rejectModalFlg: false,
                rejectAllModalFlg: false,
                selectApplicantData: null
            }
        },
        computed: {
            allApplicantsData: function () {
                return [...this.sharedCalendarApplicants]
            },
            selectApplicantName: function () {
                return this.selectApplicantData ? this.selectApplicantData.name : ''
            },
        },
        props: {
            sharedCalendarData: {
                type: Object,
                required: true,
            },
            sharedCalendarApplicants: {
                required: true
            },
        },
        methods: {
            async allowApplication(applicantsList = []) {
                if (!this.sharedCalendarData.id || !applicantsList.length) {
                    return false
                }

                const idList = applicantsList.map(function (applicant) {
                    return applicant.id
                })

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.put('/api/shared-calendars/' + this.sharedCalendarData.id + '/members', {
                    'id_list': idList
                })
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === CREATED) {
                    this.$emit('allowApplication', applicantsList)
                    this.hideModal()
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            async rejectApplication(applicantsList) {
                if (!this.sharedCalendarData.id || !applicantsList.length) {
                    return false
                }

                const idList = {'id_list': applicantsList.map(function (applicant) {
                    return applicant.id
                })}

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarData.id + '/applications', {data: idList})
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    this.$emit('rejectApplication', applicantsList)
                    this.hideModal()
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            showAllowModal(applicant) {
                this.selectApplicantData = applicant
                this.modalFlg = true
                this.allowModalFlg = true
            },
            showAllowAllModal() {
                this.modalFlg = true
                this.allowAllModalFlg= true
            },
            showRejectModal(applicant) {
                this.selectApplicantData = applicant
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

                this.selectApplicantData = null
            },
        },
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
