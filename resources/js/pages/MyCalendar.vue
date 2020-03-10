<template>
    <div>
        <SideBar />
        <div class="my-calendar">
            <h1>MyCalendar</h1>
        </div>
    </div>
</template>

<script>
    import SideBar from "../components/SideBar";
    import {mapGetters} from "vuex";
    export default {
        name: "MyCalendar",
        components: {SideBar},
    }
</script>

<style scoped>
.my-calendar{
    max-width: 500px;
    margin: 0 auto;
}
</style>
