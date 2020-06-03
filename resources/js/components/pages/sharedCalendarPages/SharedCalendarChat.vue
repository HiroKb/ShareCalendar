<template>
    <v-container class="content-wrap py-12">
        <v-card class="fill-height">
            <v-card-text class="fill-height d-flex flex-column">
                <v-list
                    class="flex-grow-1 flex-shrink-1 custom-scrollbar"
                    style="overflow-y: scroll"
                    id="js-scroll-bottom-area"
                >
                    <v-list-item
                        v-for="(message, index) in chatMessages"
                        :key="index"
                    >
<!--                        自分が投稿したメッセージ-->
                        <template v-if="message.posted_user_id === userId">

                            <v-list-item-content
                                class="d-flex flex-column align-end"
                            >
                                <p>{{ dateTimeFormatter(message.created_at)}}</p>
                                <p class="balloon right-balloon"
                                >{{ message.message }}</p>
                            </v-list-item-content>
                            <v-list-item-avatar>
                                <v-img :src="userImage"></v-img>
                            </v-list-item-avatar>
                        </template>

<!--                        他ユーザーが投稿したメッセージ-->
                        <template v-else>
                            <v-list-item-avatar>
                                <v-img
                                    :src="message.posted_user.image_url ? message.posted_user.image_url : mixinNoImagePath"
                                    v-if="message.posted_user_id"
                                ></v-img>
                                <v-img
                                    :src="mixinNoImagePath"
                                    v-else
                                ></v-img>
                            </v-list-item-avatar>
                            <v-list-item-content
                                class="d-flex flex-column align-start"
                            >
                                <div class="d-flex" style="max-width: 70%">
                                    <!--                                ユーザーアカウントが存在-->
                                    <p class="mb-0 flex-shrink-1 text-truncate"
                                       v-if="message.posted_user_id"
                                    >{{message.posted_user.name}}</p>
                                    <!--                                ユーザーアカウントが削除済み-->
                                    <p class="mb-0 flex-shrink-1 text-truncate"
                                       v-else
                                    >削除されたユーザー</p>

                                    <p class="mb-0 ml-4 flex-shrink-0">{{ dateTimeFormatter(message.created_at)}}</p>
                                </div>
                                <p class="balloon left-balloon">{{ message.message }}</p>
                            </v-list-item-content>
                        </template>
                    </v-list-item>
                </v-list>
                <v-form
                    class="flex-grow-0 flex-shrink-0 mt-4"
                    ref="form" @submit.prevent="createMessage"
                >
                    <v-textarea
                        v-model="createMessageForm.form.message"
                        label="メッセージ"
                        counter="500"
                        :rules="[mixinValidationRules.required]"
                        :error="createMessageForm.errorMessages ? !!createMessageForm.errorMessages.message : false"
                        :error-messages="createMessageForm.errorMessages ? createMessageForm.errorMessages.message ? createMessageForm.errorMessages.message : [] : []"
                        outlined
                        dense
                        rows="2"
                    >
                    </v-textarea>
                    <v-btn class="my-0" block :color="mixinThemeColor" dark type="submit">スケジュール追加</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import validationRulesMixin from "../../../mixins/validationRulesMixin"
    import utilDataMixin from "../../../mixins/utilDataMixin"
    import {CREATED, VALIDATION_ERROR} from "../../../util"
    import {mapGetters} from "vuex";
    export default {
        name: "SharedCalendarChat",
        mixins: [validationRulesMixin, utilDataMixin],
        data() {
            return {
                createMessageForm: {
                    form: {
                        message: ''
                    },
                    errorsMessages: {}
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
                userId: 'user/userId',
                userImage: 'user/userImage'
            }),
        },
        methods: {
            /**
             * チャットエリアの最下部にスクロール
             */
            scrollBottomMessagesArea() {
                const messagesArea = this.$el.querySelector('#js-scroll-bottom-area')
                messagesArea.scrollTop = messagesArea.scrollHeight
            },
            dateTimeFormatter(date){
                return moment(date, 'YYYY-MM-DD HH:mm:ss').format('MM/DD  HH:mm')
            },
            /**
             // * メッセージ投稿処理
             * @return {Promise<boolean>}
             */
            async createMessage () {
                if(!this.$refs.form.validate()){
                    return false
                }

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.post('/api/shared-calendars/' + this.sharedCalendarId + '/chat/messages', this.createMessageForm.form)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === CREATED) {

                    this.createMessageForm.form.message = ''
                    this.$refs.form.resetValidation()

                    const newMessages = _.cloneDeep(this.chatMessages)
                    newMessages.push(response.data)
                    this.$emit('changeChatMessagesData', newMessages)

                    return false
                }

                if (response.status === VALIDATION_ERROR) {
                    this.createMessageForm.errorsMessages = response.data.errors
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
        },
        watch: {
            /**
             * メッセージリストが更新された場合チャットエリアを最下部にスクロール
             */
            chatMessages:{
                handler: function () {
                    this.$nextTick(() => {
                        this.scrollBottomMessagesArea()
                    })
                },
                deep: true,
            },
        },
        /**
         * 描画時にチャットエリアを最下部にスクロール
         */
        mounted() {
            this.$nextTick(() => {
                this.scrollBottomMessagesArea()
            })
        }
    }
</script>

<style scoped>
    .container{
        max-width: 680px;
    }
    .balloon{
        position: relative;
        display: inline-block;
        max-width: 70%;
        margin-bottom: 0;
        padding: 8px 12px;
        text-align: left;
        border-radius: 4px;
        font-size: 16px;
        white-space: pre-wrap;
        line-height: 1.5;
    }
    .left-balloon{
        margin-left: 10px;
        background: #767676;
        color: white;
    }
    .left-balloon::before {
        content: '';
        position: absolute;
        left: -10px;
        top: 0;
        border: 14px solid transparent;
        border-top-color: #767676;
    }
    .right-balloon{
        margin-right: 10px;
        background: #3949AB;
        color: white;
    }
    .right-balloon::after {
        content: '';
        position: absolute;
        right: -10px;
        top: 0;
        border: 14px solid transparent;
        border-top-color: #3949AB;
    }
</style>
