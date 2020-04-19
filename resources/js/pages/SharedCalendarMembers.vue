<template>
    <div>
        <div class="contents">
            <div v-for="Member in sharedCalendarMembers" >
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
                modalFlg: false,
                selectMemberData: null
            }
        },
        computed: {
            memberName: function () {
                return this.selectMemberData ? this.selectMemberData.name : ''
            }
        },
        props: {
            sharedCalendarData: {
                type: Object,
                required: true,
            },
            sharedCalendarMembers: {
                type: Array,
                required: true
            },
        },
        methods: {
            async unShareMember() {
                if (!this.sharedCalendarData.id || !this.selectMemberData.id) {
                    return false
                }
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarData.id + '/members/' + this.selectMemberData.id)

                if (response.status === SUCCESS) {
                    this.$emit('unShareMember', this.selectMemberData.id)
                    this.hideModal()
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            showUnShareModal(member) {
                this.selectMemberData = member
                this.modalFlg = true
            },
            hideModal() {
                this.modalFlg = false

                this.selectMemberData = null
            }
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
