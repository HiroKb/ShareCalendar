<template>
    <div>
        <div class="contents">
            <p>{{ sharedCalendarData.calendar_name }}</p>
            <p>{{ invitationUrl }}</p>
        </div>
    </div>
</template>

<script>
    export default {
        name: "SharedCalendarIndex",
        computed: {
            invitationUrl: function () {
                return this.sharedCalendarData.search_id ?
                        location.protocol + '//' + location.host + '/shared-calendar/' + this.sharedCalendarData.search_id + '/application'
                        : ''
            }
        },
        props: {
            sharedCalendarData: {
                type: Object,
                required: true,
                default: {}
            },
        },
    }
</script>

<style scoped>
    .sidebar-wrap{
        display: flex;
    }
</style>
