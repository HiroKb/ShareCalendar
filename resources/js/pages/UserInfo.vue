<template>
    <div>
        <SideBar />
        <div class="user-info">
            <h1>UserInfo</h1>
        </div>
    </div>
</template>

<script>
    import SideBar from "../components/SideBar";
    export default {
        name: "UserInfo",
        components: {SideBar}
    }
</script>

<style scoped>
.user-info{
    max-width: 500px;
    margin: 0 auto;
}
</style>
