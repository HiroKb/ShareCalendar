<template>
    <div class="contents">
        <form @submit.prevent="deleteSharedCalendar">
            <p>{{ sharedCalendarData.calendar_name }}</p>
            <p>この共有カレンダーを削除しますか？</p>
            <button>削除</button>
        </form>
    </div>
</template>

<script>
    import {SUCCESS} from "../../../util";

    export default {
        name: "DeleteSharedCalendar",
        props: {
            sharedCalendarData: {
                type: Object,
                required: true,
                default: {}
            }
        },
        methods: {
            async deleteSharedCalendar() {
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarData.id)
                this.$store.commit('loading/setLoadingFlg', false)

                if(response.status === SUCCESS) {
                    this.$store.commit('flashMessage/setMessage',  this.sharedCalendarData.name + 'カレンダーを削除しました。')
                    this.$router.push({name: 'myCalendar'})
                    return false
                }
                this.$store.commit('error/setCode', response.status)
            }
        }
    }
</script>

<style scoped>

</style>
