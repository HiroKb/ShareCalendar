<template>
    <div>
        <div class="contents">
            <h1>UserInfo</h1>

            <p>ユーザー名</p>
            <p>{{ userName }}</p>
            <router-link to="edit-user-name">編集</router-link>

            <p>メールアドレス</p>
            <p>{{ userEmail }}</p>
            <router-link to="edit-user-email">編集</router-link>

            <p>パスワード</p>
            <p>********</p>
            <router-link to="edit-user-password">編集</router-link>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";
    export default {
        name: "UserInfo",
        computed : {
            ...mapGetters({
                userName: 'user/userName',
                userEmail: 'user/userEmail'
            })
        }
    }
</script>

<style scoped>
</style>
