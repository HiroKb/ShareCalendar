<template>
    <div>
        <div class="contents">
            <h1>UserInfo</h1>

            <p>ユーザー名</p>
            <p>{{ userName }}</p>
            <button @click="showUpdateNameForm">編集</button>

            <p>メールアドレス</p>
            <p>{{ userEmail }}</p>
            <router-link to="edit-user-email">編集</router-link>

            <p>パスワード</p>
            <p>********</p>
            <router-link to="edit-user-password">編集</router-link>
        </div>
        <div class="modal-background" v-show="modalFlg" @click="hideModal">
            <div class="modal" >
                <div class="modal-inner" @click.stop>
                    <i class="fas fa-times" @click="hideModal"></i>

                    <form @submit.prevent="updateName" v-show="updateNameForm.showFlg">
                        <label for="name">ユーザー名</label>
                        <input type="text" id="name" v-model="updateNameForm.data.name">
                        <p v-if="errorMessages && errorMessages.name">{{ errorMessages.name [0]}}</p>

                        <button type="submit">更新</button>

                    </form>
<!--                    <form @submit.prevent="updateSchedule" v-show="editForm.showFlg">-->
<!--                        <p v-if="editError.errors.schedule">{{ editError.errors.schedule[0] }}</p>-->

<!--                        <button type="submit">スケジュール更新</button>-->
<!--                    </form>-->

<!--                    <form @submit.prevent="deleteSchedule" v-show="deleteForm.showFlg">-->
<!--                        <p>このスケジュールを削除しますか？</p>-->
<!--                        <p>日付</p>-->
<!--                        <p>{{ deleteData.date}}</p>-->
<!--                        <p>時間</p>-->
<!--                        <p>{{ deleteData.time }}</p>-->
<!--                        <p>スケジュール名</p>-->
<!--                        <p>{{ deleteData.title }}</p>-->
<!--                        <p>詳細</p>-->
<!--                        <p>{{ deleteData.description }}</p>-->
<!--                        <button>削除</button>-->
<!--                    </form>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters, mapState} from "vuex";
    export default {
        name: "UserInfo",
        data() {
            return {
                modalFlg: false,
                updateNameForm: {
                    showFlg: false,
                    data: {name: ''}
                }
            }
        },
        computed : {
            ...mapGetters({
                userName: 'user/userName',
                userEmail: 'user/userEmail'
            }),
            ...mapState({
                apiStatus: state => state.user.apiStatus, // API通信成否
                errorMessages: state => state.user.errorMessages // バリデーションエラーメッセージ
            })
        },
        methods: {
            async updateName () {
                // userストアのupdateNameアクション呼び出し
                await this.$store.dispatch('user/updateName', this.updateNameForm.data)

                // 通信成功時
                if (this.apiStatus) {
                    this.hideModal()
                }
            },
            showUpdateNameForm () {
                this.modalFlg = true
                this.updateNameForm.showFlg = true
            },
            hideModal() {
                this.modalFlg = false
                this.updateNameForm.showFlg = false
                this.updateNameForm.data.name = ''
                this.$store.commit('user/setErrorMessages', null)
            }
        },
    }
</script>

<style scoped>
    .modal-background{
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 100vw ;
        z-index: 10;
        background: rgba(0, 0, 0, .1);
    }

    .modal{
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        width: 100vw ;
    }
    .modal-inner{
        background: #ffffff;
    }
</style>
