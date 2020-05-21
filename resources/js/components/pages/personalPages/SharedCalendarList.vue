<template>
    <div>
        <div class="contents">
            <h1>SharedCalendarList</h1>
            <template v-if="!sharedCalendarList.loadingFlg">
                <ul v-if="sharedCalendarList.data.length">
                    <li v-for="sharedCalendar in sharedCalendarList.data">
                        <router-link :to="{name: 'sharedCalendar', params:{sharedCalendarId: sharedCalendar.id}}">
                            <p>{{sharedCalendar.calendar_name}}</p>
                        </router-link>
                    </li>
                </ul>

                <p v-else>まだ共有しているカレンダーがありません。</p>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        name: "SharedCalendar",
        props: {
            sharedCalendarList: {
                type: Object,
                required: true,
                default: {}
            }
        },
    }
</script>

<style scoped>

</style>
