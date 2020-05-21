<template>
    <div>
        <div class="contents">
            <p>共有カレンダー名</p>
            <p>{{ sharedCalendarData.calendar_name }}</p>
            <button @click="showUpdateCalendarNameModal">変更</button>
            <p>カレンダー検索ID/招待URL</p>
            <p>{{ sharedCalendarData.search_id }}</p>
            <p>{{ invitationUrl }}</p>
            <button @click="showUpdateSearchIdModal">変更</button>
        </div>
        <div class="modal-background" v-show="modalFlg" @click="hideModal">
            <div class="modal" >
                <div class="modal-inner" @click.stop>

                    <i class="fas fa-times" @click="hideModal"></i>
                    <form @submit.prevent="updateCalendarName" v-show="updateCalendarNameForm.showFlg">
                        <label for="calendar-name">新しい共有カレンダー名</label>
                        <input id="calendar-name" type="text" v-model="updateCalendarNameForm.data.calendar_name">
                        <p v-if="updateCalendarNameForm.errors.calendar_name">{{ updateCalendarNameForm.errors.calendar_name[0] }}</p>
                        <button>変更</button>
                    </form>
                    <form @submit.prevent="updateSearchId" v-show="updateSearchIdForm.showFlg">
                        <p>カレンダー検索ID</p>
                        <p>{{ sharedCalendarData.search_id }}</p>
                        <p>招待URL</p>
                        <p>{{ invitationUrl }}</p>
                        <p>カレンダー検索IDとカレンダー招待URLを変更しますか？</p>
                        <button>変更</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {SUCCESS, VALIDATION_ERROR} from "../../../util";
    export default {
        name: "SharedCalendarInfo.vue",
        data () {
            return {
                modalFlg: false,
                updateCalendarNameForm: {
                    showFlg: false,
                    data: {
                        calendar_name: ''
                    },
                    errors: {}
                } ,
                updateSearchIdForm: {
                    showFlg: false
                }
            }
        },
        props: {
            sharedCalendarData: {
                type: Object,
                required: true,
                default: {}
            }
        },
        computed: {
            invitationUrl: function () {
                return this.sharedCalendarData.search_id ?
                    location.protocol + '//' + location.host + '/personal/shared-calendar/' + this.sharedCalendarData.search_id + '/application'
                    : ''
            }
        },
        methods: {
            async updateCalendarName() {
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.patch('/api/shared-calendars/' + this.sharedCalendarData.id + '/name', this.updateCalendarNameForm.data)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    this.$emit('changeCalendarData', response.data)
                    this.hideModal()
                    this.$store.commit('flashMessage/setMessage', '共有カレンダー名を変更しました')
                    return false
                }
                if (response.status === VALIDATION_ERROR) {
                    this.updateCalendarNameForm.errors = response.data.errors
                    return false
                }
                this.$store.commit('error/setCode', response.status)
            },
            async updateSearchId(){
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.patch('/api/shared-calendars/' + this.sharedCalendarData.id + '/search-id')
                this.$store.commit('loading/setLoadingFlg', false)

                if(response.status === SUCCESS) {

                    this.$emit('changeCalendarData', response.data)
                    this.hideModal()
                    this.$store.commit('flashMessage/setMessage', 'カレンダー検索ID/招待URLを変更しました')
                    return false
                }
                this.$store.commit('error/setCode', response.status)
            },
            showUpdateCalendarNameModal() {
                this.modalFlg = true;
                this.updateCalendarNameForm.showFlg = true;
            },
            showUpdateSearchIdModal() {
                this.modalFlg = true;
                this.updateSearchIdForm.showFlg = true;
            },
            hideModal() {
                this.modalFlg = false
            }
        }
    }
</script>

<style scoped>
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
