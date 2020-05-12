<template>
    <div class="contents">
        <form @submit.prevent="unShareCalendar">
            <p>{{ sharedCalendarData.calendar_name }}</p>
            <p>共有を解除しますか？</p>
            <button>解除</button>
        </form>
    </div>
</template>

<script>
    import {SUCCESS} from "../../../util";

    export default {
        name: "UnShareCalendar",
        props: {
            sharedCalendarData: {
                type: Object,
                required: true,
            },
        },
        methods: {
            async unShareCalendar () {
                if (!this.sharedCalendarData.id) {
                    return false
                }

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarData.id + '/members')
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
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
