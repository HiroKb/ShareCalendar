<template>
    <div>
        <div class="contents">
            <h1>EditUserName</h1>
            <form @submit.prevent="updateName">
                <label for="name">ユーザー名</label>
                <input type="text" id="name" v-model="name">
                <p v-if="errorMessages && errorMessages.name">{{ errorMessages.name [0]}}</p>

                <button type="submit">更新</button>

            </form>
        </div>
    </div>
</template>

<script>
    import {mapState} from "vuex";
    export default {
        name: "EditUserName",
        data(){
            return {
                name: this.$store.getters['user/userName']
            }
        },
        computed: {
            ...mapState({
                apiStatus: state => state.user.apiStatus, // API通信成否
                errorMessages: state => state.user.errorMessages // バリデーションエラーメッセージ
            })
        },
        methods: {
            async updateName () {
                // userストアのupdateNameアクション呼び出し
                await this.$store.dispatch('user/updateName', {name: this.name})

                // 通信成功時
                if (this.apiStatus) {
                    this.$router.push({name: 'userInfo'})
                }
            }
        },
        created() {
            this.$store.commit('user/setErrorMessages', null)
        }
    }
</script>

<style scoped>
</style>
