<template>
    <div>
        <div class="contents">
            <h1>UserInfo</h1>

            <p>ユーザー名</p>
            <p>{{ userName }}</p>
            <button @click="showUpdateNameModal">変更</button>

            <p>メールアドレス</p>
            <p>{{ userEmail }}</p>
            <button @click="showUpdateEmailModal">変更</button>

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

                    <form @submit.prevent="updateEmail" v-show="updateEmailForm.showFlg">
                        <label for="email">新しいメールアドレス</label>
                        <input type="text" id="email" v-model="updateEmailForm.data.email">
                        <p v-if="errorMessages && errorMessages.email">{{ errorMessages.email [0]}}</p>

                        <label for="password">パスワード</label>
                        <input type="text" id="password" v-model="updateEmailForm.data.password">
                        <p v-if="errorMessages && errorMessages.password">{{ errorMessages.password[0] }}</p>

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
                },
                updateEmailForm: {
                    showFlg: false,
                    data: {
                        email: '',
                        password: ''
                    }
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
            async updateEmail () {
                // userストアのupdateEmailアクション呼び出し
                await this.$store.dispatch('user/updateEmail', this.updateEmailForm.data)

                // 通信成功時
                if (this.apiStatus) {
                    this.hideModal()
                }
            },
            showUpdateNameModal () {
                this.modalFlg = true
                this.updateNameForm.showFlg = true
            },
            showUpdateEmailModal () {
                this.modalFlg = true
                this.updateEmailForm.showFlg = true
            },
            hideModal() {
                this.modalFlg = false
                this.updateNameForm.showFlg = false
                this.updateNameForm.data.name = ''

                this.updateEmailForm.showFlg = false
                this.updateEmailForm.data.email = ''
                this.updateEmailForm.data.password = ''


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
