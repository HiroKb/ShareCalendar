<template>
    <div>
        <div class="sidebar-wrap">
            <SideBar />
        </div>
        <div class="contents">
            <h1>SearchSharedCalendar</h1>
            <form @submit.prevent="searchShareCalendar">
                <label for="calendar-id">検索ID</label>
                <input type="text" id="calendar-id" v-model="searchShareCalendarData.search_id">
<!--                <p v-if="createError.errors.calendar_name">{{ createError.errors.calendar_name[0]}}</p>-->

                <button type="submit">検索</button>

            </form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "SearchSharedCalendar"
    }
</script>

<style scoped>

</style>
