<template>
    <div>
        <h1>ShareCalendar</h1>
        <p>{{ shareCalendarId }}</p>
    </div>
</template>

<script>
    export default {
        name: "ShareCalendar",
        props: {
            shareCalendarId: {
                required: true
            }
        }
    }
</script>

<style scoped>

</style>
