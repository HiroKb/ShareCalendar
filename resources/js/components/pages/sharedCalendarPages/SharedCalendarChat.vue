<template>
    <div class="contents">
        <ul>
            <li v-for="message in chatMessages">
                <div class="message" v-if="message.posted_user_id === userId">
                    <p>自分</p>
                    <p>{{ changeDateFormat(message.created_at)}}</p>
                    <p>{{ message.message }}</p>
                </div>
                <div class="message" v-else-if="message.posted_user_id === null">
                    <p>削除されたユーザー</p>
                    <p>{{ changeDateFormat(message.created_at)}}</p>
                    <p>{{ message.message }}</p>
                </div>
                <div class="message" v-else>
                    <p>{{ message.posted_user.name}}</p>
                    <p>{{ changeDateFormat(message.created_at)}}</p>
                    <p>{{ message.message }}</p>
                </div>
            </li>
        </ul>
        <form @submit.prevent="createMessage">
            <textarea v-model="createMessageForm.data.message"></textarea>
            <button>送信</button>
        </form>
    </div>
</template>

<script>
    import {CREATED, VALIDATION_ERROR} from "../../../util";
    import {mapGetters} from "vuex";
    import moment from "moment";

    export default {
        name: "SharedCalendarChat",
        data() {
            return {
                createMessageForm: {
                    data: {
                        message: ''
                    },
                    errors: {}
                }
            }
        },
        props: {
            sharedCalendarId: {
                type: String,
                required: true,
                default: ''
            },
            chatMessages: {
                type: Array,
                required: true,
                default: []
            }
        },
        computed: {
            ...mapGetters({
                userId: 'user/userId'
            }),
        },
        methods: {
            async createMessage () {
                if (!this.createMessageForm.data.message){
                    return false
                }

                const response = await axios.post('/api/shared-calendars/' + this.sharedCalendarId + '/chat/messages', this.createMessageForm.data)

                if (response.status === CREATED) {

                    const newMessages = _.cloneDeep(this.chatMessages)

                    console.log(newMessages)

                    newMessages.push(response.data)

                    console.log(newMessages)

                    this.$emit('changeChatMessagesData', newMessages)

                    this.createMessageForm.data.message = ''
                    return false
                }

                if (response.status === VALIDATION_ERROR) {
                    this.createMessageForm.errors = response.data.errors
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            changeDateFormat(date){
                return moment(date, 'YYYY-MM-DD HH:mm:ss').format('MM/DD  HH:mm')
            }
        }
    }
</script>

<style scoped>
.message{
    border-bottom: 1px solid black;
}
</style>
