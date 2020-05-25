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
                     :shared-schedules-data="sharedSchedulesData"
                     :fetch-shared-schedules="fetchSharedSchedules"
                     @removeMember="removeMember"
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
                // 共有スケジュールデータ
                sharedSchedulesData: {
                    schedulesYear: null,
                    schedules: [],
                },
                // 共有カレンダーデータ
                sharedCalendarData: {
                    id: '',
                    admin_id: '',
                    calendar_name: ''
                },
                // メッセージリスト
                chatMessages: [],
                // 共有メンバーリスト
                sharedCalendarMembers: [],
                // 共有申請者リスト
                sharedCalendarApplicants: [],
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
            /**
             * 共有カレンダーの管理者フラグ
             * @return {string|null}
             */
            adminFlg: function () {
                if (this.sharedCalendarData.admin_id){
                    return this.sharedCalendarData.admin_id === this.userId ? 'admin' : 'notAdmin'
                }
                return null
            }
        },
        methods: {
            /**
             *
             * スケジュールデータの取得
             * @param {String} year スケジュールを取得したい年 'YYYY'
             * @param {boolean} isEmit コンポーネント子コンポーネントから呼び出す場合はtrue
             * @return {Promise<boolean>}
             */
            async fetchSharedSchedules(year, isEmit = false) {
                // year年1月1日のmomentオブジェクトと曜日
                const firstDay = moment().year(year).startOf('year')
                const firstDayWeek = firstDay.day()

                // year年12月31日のmomentオブジェクトと曜日
                const lastDay = moment().year(year).endOf('year')
                const lastDayWeek = lastDay.day()

                // 取得するスケジュールの先頭と末尾の日付
                const from = firstDay.subtract(firstDayWeek, 'day').format('YYYY-MM-DD')
                const until = lastDay.add(6 - lastDayWeek, 'day').format('YYYY-MM-D')

                isEmit && this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/schedules/' + from + '/' + until)
                isEmit && this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    this.sharedSchedulesData.schedulesYear = year
                    this.sharedSchedulesData.schedules = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * 共有カレンダー情報の取得
             * @return {Promise<boolean>}
             */
            async fetchSharedCalendar() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId)

                if(response.status === SUCCESS) {
                    this.sharedCalendarData = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * メッセージデータの取得
             * @return {Promise<boolean>}
             */
            async fetchChatMessages() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/chat/messages')

                if(response.status === SUCCESS) {
                    this.chatMessages = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * 共有メンバーの取得
             * @return {Promise<boolean>}
             */
            async fetchSharedCalendarMembers() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/members')

                if(response.status === SUCCESS) {
                    this.sharedCalendarMembers = response.data

                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * 共有申請者の取得
             * @return {Promise<boolean>}
             */
            async fetchSharedCalendarApplicants() {
                const response = await axios.get('/api/shared-calendars/' + this.sharedCalendarId + '/applications')

                if(response.status === SUCCESS) {
                    this.sharedCalendarApplicants = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * 共有申請を許可した場合の処理
             * @param {Object} userList 許可したユーザーデータリスト
             */
            allowApplications (userList) {
                this.addMembers(userList)
                this.removeApplicants(userList)
            },
            /**
             * メンバーリストへの追加処理
             * @param {Object} userList 追加するユーザーデータリスト
             */
            addMembers (userList) {
                userList.forEach((user) => {
                    this.sharedCalendarMembers.push(user)
                })
            },
            /**
             * メンバーリストからの削除処理
             * @param {String} memberId 削除するメンバーのID
             */
            removeMember (memberId) {
                for (let i = 0; i < this.sharedCalendarMembers.length; i++) {
                    if (memberId === this.sharedCalendarMembers[i].id){
                        this.sharedCalendarMembers.splice(i, 1)
                        break
                    }
                }
            },
            /**
             * 共有申請リストからの削除処理
             * @param {Object} userList 削除するユーザーデータリスト
             */
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
            /**
             * カレンダーデータの変更
             * @param {Object} data 新しいカレンダーデータ
             */
            changeCalendarData(data) {
                this.sharedCalendarData = data
            },
            /**
             * スケジュールデータの変更
             * @param {Object} data 新しいスケジュールデータ
             */
            changeSchedulesData(data) {
                if (data.schedulesYear){
                    this.sharedSchedulesData.schedulesYear = data.schedulesYear
                }
                if (data.schedules){
                    this.sharedSchedulesData.schedules = data.schedules
                }
            },
            /**
             * メッセージリストの変更処理
             * @param {Array} data 新しいメッセージ配列
             */
            changeChatMessagesData(data) {
                this.chatMessages = data
            },
        },
        /**
         * データの取得
         * 共有申請者データはカレンダー管理者の場合のみ取得
         * @return {Promise<void>}
         */
        async created() {
            this.sharedSchedulesData.schedulesYear = (moment().format('YYYY'))

            this.$store.commit('loading/setLoadingFlg', true)
            await Promise.all([
                this.fetchSharedSchedules(this.sharedSchedulesData.schedulesYear),
                this.fetchSharedCalendar(),
                this.fetchChatMessages(),
                this.fetchSharedCalendarMembers()
            ]).then(x => {
                if (this.adminFlg === 'admin') {
                    this.fetchSharedCalendarApplicants()
                }
            })
            this.$store.commit('loading/setLoadingFlg', false)
        },
    }
</script>

<style scoped>
</style>
