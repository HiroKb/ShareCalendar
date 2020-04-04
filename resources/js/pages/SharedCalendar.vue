<template>
    <div>
        <h1>SharedCalendar</h1>
        <p>{{ sharedCalendarId }}</p>
    </div>
</template>

<script>
    export default {
        name: "SharedCalendar",
        props: {
            sharedCalendarId: {
                required: true
            }
        }
    }
</script>

<style scoped>

</style>
