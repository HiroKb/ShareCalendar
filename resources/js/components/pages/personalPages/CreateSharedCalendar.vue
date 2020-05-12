<template>
    <div>
        <div class="contents">
            <h1>CreateShareCalendar</h1>
            <form @submit.prevent="createShareCalendar">
                <label for="calendar-name">カレンダー名</label>
                <input type="text" id="calendar-name" v-model="createShareCalendarData.calendar_name">
                <p v-if="createError.errors.calendar_name">{{ createError.errors.calendar_name[0]}}</p>

                <button type="submit">作成</button>

            </form>
        </div>
    </div>
</template>

<script>
    import {CREATED, VALIDATION_ERROR} from "../../../util"
    export default {
        name: "CreateShareCalendar",
        data(){
            return {
                createShareCalendarData: {
                    calendar_name: ''
                },
                createError: {
                    errors: {},
                    errorFlg: false
                }
            }
        },
        methods: {
            async createShareCalendar () {
                this.createError.errors = {}
                this.createError.errorFlg = false
                if (!this.createShareCalendarData.calendar_name){
                    this.createError.errors.calendar_name = ['共有カレンダー名は必須です。']
                    this.createError.errorFlg = true
                }
                if (this.createError.errorFlg){
                    return false
                }


                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.post('/api/shared-calendars', this.createShareCalendarData)
                this.$store.commit('loading/setLoadingFlg', false)


                if (response.status === CREATED) {
                    this.$router.push({name: 'sharedCalendarIndex', params: {sharedCalendarId: response.data.id}})
                    return false
                }

                if (response.status === VALIDATION_ERROR) {
                    this.createError.errors = response.data.errors
                }

                this.$store.commit('error/setCode', response.status)
            }
        },
        created() {
        }
    }
</script>

<style scoped>
</style>
