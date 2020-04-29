<template>
    <div>
        <div class="contents">
            <p>共有カレンダー名</p>
            <p>{{ sharedCalendarData.calendar_name }}</p>
            <p>カレンダー検索ID/招待URL</p>
            <p>{{ sharedCalendarData.search_id }}</p>
            <p>{{ invitationUrl }}</p>
            <button @click="showUpdateSearchIdModal">変更</button>
        </div>
        <div class="modal-background" v-show="modalFlg" @click="hideModal">
            <div class="modal" >
                <div class="modal-inner" @click.stop>

                    <i class="fas fa-times" @click="hideModal"></i>
<!--                    <form @submit.prevent="updateSchedule" v-show="editForm.showFlg">-->
<!--                        <p v-if="editError.errors.schedule">{{ editError.errors.schedule[0] }}</p>-->
<!--                    </form>-->
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
    import {SUCCESS} from "../util";

    export default {
        name: "SharedCalendarInfo.vue",
        data () {
            return {
                modalFlg: false,
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
                    location.protocol + '//' + location.host + '/shared-calendar/' + this.sharedCalendarData.search_id + '/application'
                    : ''
            }
        },
        methods: {
            async updateSearchId(){
                const response = await axios.patch('/api/shared-calendars/' + this.sharedCalendarData.id + '/search-id')

                if(response.status === SUCCESS) {

                    this.$emit('changeCalendarData', response.data)
                    this.hideModal()
                    return false
                }
                this.$store.commit('error/setCode', response.status)
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
