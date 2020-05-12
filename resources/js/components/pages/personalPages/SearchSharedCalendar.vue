<template>
    <div>
        <div class="contents">
            <h1>SearchSharedCalendar</h1>
            <form @submit.prevent="searchSharedCalendar">
                <label for="calendar-id">検索ID</label>
                <input type="text" id="calendar-id" v-model="searchSharedCalendarData.search_id">
                <p v-if="searchError.errors.search_id">{{ searchError.errors.search_id[0]}}</p>

                <button type="submit">検索</button>

            </form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "SearchSharedCalendar",
        data() {
            return {
                searchSharedCalendarData: {
                    search_id: ''
                },
                searchError: {
                    errors: {},
                    errorFlg: false
                }
            }
        },
        methods: {
            searchSharedCalendar () {
                this.searchError.errors = {}
                this.searchError.errorFlg = false

                if (!this.searchSharedCalendarData.search_id) {
                    this.searchError.errors.search_id = ['検索IDを入力してください。']
                    this.searchError.errorFlg = true
                }
                if (this.searchError.errorFlg) {
                    return false
                }

                this.$router.push({name: 'applicationSharedCalendar', params: {searchId: this.searchSharedCalendarData.search_id}})
            }
        }
    }
</script>

<style scoped>

</style>
