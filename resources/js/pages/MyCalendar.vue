<template>
    <div>
        <button @click="logout">Logout</button>
        <h1>MyCalendar</h1>
    </div>
</template>

<script>
    export default {
        name: "MyCalendar",
        methods: {
            async logout () {
                await this.$store.dispatch('auth/logout')

                this.$router.push('/')
            }
        }
    }
</script>

<style scoped>

</style>
