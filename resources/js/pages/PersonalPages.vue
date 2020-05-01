<template>
    <div>
        <div class="sidebar-wrap">
            <SideBar />
        </div>
        <router-view></router-view>
    </div>
</template>

<script>
    import SideBar from "../components/SideBar"
    export default {
        name: "PersonalPages.vue",
        components: {SideBar}
    }
</script>

<style scoped>

</style>
