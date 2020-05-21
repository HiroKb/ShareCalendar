<template>
    <div>
        <v-navigation-drawer
            :value="drawer"
            @input="$emit('changeDrawer', $event)"
            app clipped
        >
            <v-list-item>
                <v-list-item-content>
                    <v-list-item-title class="title">
                        {{ sharedCalendarData.calendar_name }}
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>

            <v-divider></v-divider>
            <v-list dense nav>

                <v-list-item :to="{name: 'sharedCalendar', params: {sharedCalendarId: sharedCalendarId}}">
                    <v-list-item-content>
                        共有カレンダー
                    </v-list-item-content>
                </v-list-item>
                <v-list-item :to="{name: 'sharedCalendarChat', params: {sharedCalendarId: sharedCalendarId}}">
                    <v-list-item-content>
                        チャット
                    </v-list-item-content>
                </v-list-item>
                <v-list-item :to="{name: 'sharedCalendarMembers', params: {sharedCalendarId: sharedCalendarId}}">
                    <v-list-item-content>
                        共有メンバー一覧
                    </v-list-item-content>
                </v-list-item>
                <v-list-item :to="{name: 'sharedCalendarApplicants', params:{sharedCalendarId: sharedCalendarId}}" v-if="adminFlg === 'admin'">
                    <v-list-item-content>
                        共有申請者一覧
                    </v-list-item-content>
                </v-list-item>
                <v-list-item :to="{name: 'sharedCalendarInfo', params:{sharedCalendarId: sharedCalendarId}}" v-if="adminFlg === 'admin'">
                    <v-list-item-content>
                        共有カレンダー情報/変更
                    </v-list-item-content>
                </v-list-item>
                <v-list-item :to="{name: 'unShareCalendar', params:{sharedCalendarId: sharedCalendarId}}" v-if="adminFlg === 'notAdmin'">
                    <v-list-item-content>
                        共有解除
                    </v-list-item-content>
                </v-list-item>
                <v-list-item :to="{name: 'deleteSharedCalendar', params:{sharedCalendarId: sharedCalendarId}}" v-if="adminFlg === 'admin'">
                    <v-list-item-content>
                        カレンダー削除
                    </v-list-item-content>
                </v-list-item>
                <v-list-item :to="{name: 'personalCalendar'}">
                    <v-list-item-content>
                        マイページ
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
        <router-view :shared-calendar-id="sharedCalendarId"
                     :shared-calendar-data="sharedCalendarData"
                     :chat-messages="chatMessages"
                     :shared-calendar-members="sharedCalendarMembers"
                     :shared-calendar-applicants="sharedCalendarApplicants"
                     :schedules-data="sharedSchedulesData"
                     @unShareMember="removeMember"
                     @allowApplication="allowApplications"
                     @rejectApplication="removeApplicants"
                     @changeCalendarData="changeCalendarData"
                     @changeSchedulesData="changeSchedulesData"
                     @changeChatMessagesData="changeChatMessagesData"
        >
        </router-view>
    </div>

</template>

<script>
    import {SUCCESS} from "../../../util";
    import {mapGetters} from "vuex";
    export default {
        name: "SharedCalendarPages",
        data () {
            return {
                sharedCalendarData: {
                    id: '',
                    admin_id: '',
                    calendar_name: ''
                },
                chatMessages: [],
                sharedCalendarMembers: [],
                sharedCalendarApplicants: [],
                sharedSchedulesData: {
                    schedulesYear: null,
                    schedules: [],
                }
            }
        },
        props: {
            drawer:{
                type: Boolean,
            },
            sharedCalendarId: {
                required: true
            },
        },
        computed: {
            ...mapGetters({
                userId: 'user/userId'
            }),
            adminFlg: function () {
                if (this.sharedCalendarData.admin_id){
                    return this.sharedCalendarData.admin_id === this.userId ? 'admin' : 'notAdmin'
                }
                return null
            }
        },
        methods: {
            async fetchSharedCalendar() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId)

                if(response.status === SUCCESS) {
                    this.sharedCalendarData = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            async fetchChatMessages() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/chat/messages')

                if(response.status === SUCCESS) {
                    this.chatMessages = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            async fetchSharedCalendarMembers() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/members')

                if(response.status === SUCCESS) {
                    this.sharedCalendarMembers = response.data

                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            async fetchSharedCalendarApplicants() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/applications')

                if(response.status === SUCCESS) {
                    this.sharedCalendarApplicants = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            allowApplications (userList) {
                this.addMembers(userList)
                this.removeApplicants(userList)
            },
            addMembers (userList) {
                userList.forEach((user) => {
                    this.sharedCalendarMembers.push(user)
                })
            },
            removeMember (memberId) {
                for (let i = 0; i < this.sharedCalendarMembers.length; i++) {
                    if (memberId === this.sharedCalendarMembers[i].id){
                        this.sharedCalendarMembers.splice(i, 1)
                        break
                    }
                }
            },
            removeApplicants (userList) {
                userList.forEach((removedUser) => {
                    for (let i = 0; i < this.sharedCalendarApplicants.length; i++){
                        if (this.sharedCalendarApplicants[i].id === removedUser.id) {
                            this.sharedCalendarApplicants.splice(i, 1)
                            break
                        }
                    }
                })
            },
            changeCalendarData(data) {
                this.sharedCalendarData = data
            },
            changeSchedulesData(data) {
                if (data.schedulesYear){
                    this.sharedSchedulesData.schedulesYear = data.schedulesYear
                }
                if (data.schedules){
                    this.sharedSchedulesData.schedules = data.schedules
                }
            },
            changeChatMessagesData(data) {
                this.chatMessages = data
            },
        },
        created() {
            Promise.all([
                this.fetchSharedCalendar(),
                this.fetchChatMessages(),
                this.fetchSharedCalendarMembers()
            ]).then(x => {
                if (this.adminFlg === 'admin') {
                    this.fetchSharedCalendarApplicants()
                }
            })
        },
    }
</script>

<style scoped>
</style>
