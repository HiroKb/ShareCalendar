<template>
    <div>
        <SideBar />
        <div class="user-info">
            <h1>UserInfo</h1>

            <p>ユーザー名</p>
            <p>{{ userName }}</p>
            <router-link to="edit-username">編集</router-link>

            <p>パスワード</p>
            <p>********</p>
            <router-link to="edit-password">編集</router-link>
        </div>
    </div>
</template>

<script>
    import SideBar from "../components/SideBar";
    import {mapGetters} from "vuex";
    export default {
        name: "UserInfo",
        components: {SideBar},
        computed : {
            ...mapGetters({
                userName: 'user/userName'
            })
        }
    }
</script>

<style scoped>
.user-info{
    max-width: 500px;
    margin: 0 auto;
}
</style>
