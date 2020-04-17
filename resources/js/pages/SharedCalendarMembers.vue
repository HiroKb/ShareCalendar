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
            <div v-else v-for="Member in sharedCalendarMembers" >
                <p>{{ Member.name }}</p>
                <button @click="showUnShareModal(Member)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="modal-background" v-show="modalFlg" @click="hideModal">
            <div class="modal" >
                <div class="modal-inner" @click.stop>
                    <i class="fas fa-times" @click="hideModal"></i>

                    <form @submit.prevent="unShareMember">
                        <p>{{ memberName }}</p>
                        <p>カレンダーの共有を解除しますか？</p>
                        <button>解除</button>
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
        name: "SharedCalendarMembers",
        components: {SideBar},
        data () {
            return {
                sharedCalendarMembers:{},
                loadingFlg: true,
                modalFlg: false,
                memberData: null
            }
        },
        computed: {
            memberName: function () {
                return this.memberData ? this.memberData.name : ''
            }
        },
        props: {
            sharedCalendarId: {
                required: true
            },
        },
        methods: {
            async fetchSharedCalendarMembers() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/members')

                if(response.status === SUCCESS) {
                    this.sharedCalendarMembers = response.data

                    this.loadingFlg = false
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            async unShareMember() {
                if (!this.sharedCalendarId || !this.memberData.id) {
                    return false
                }
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarId + '/members/' + this.memberData.id)

                if (response.status === SUCCESS) {
                    for (let i = 0; i < this.sharedCalendarMembers.length; i++) {
                        if (this.memberData.id === this.sharedCalendarMembers[i].id){
                            this.sharedCalendarMembers.splice(i, 1)
                            break
                        }
                    }
                    this.hideModal()
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            showUnShareModal(member) {
                this.memberData = member
                this.modalFlg = true
            },
            hideModal() {
                this.modalFlg = false

                this.memberData = null
            }
        },
        created() {
            this.fetchSharedCalendarMembers()
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
