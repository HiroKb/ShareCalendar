<template>
    <v-container class="container-mt medium-container">
        <v-card>
            <v-card-title>アカウント削除</v-card-title>
            <v-card-text>
                <v-form ref="form" @submit.prevent="deleteAccount">
                    <p>作成した個人スケジュール、共有カレンダーが全て削除されます。<br/>アカウントを削除しますか？</p>
                    <v-btn block :color="mixinThemeColor" dark type="submit">削除</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import utilDataMixin from "../../../mixins/utilDataMixin"
    import {mapState} from "vuex";
    export default {
        name: "DeleteAccount",
        mixins: [utilDataMixin],
        computed: {
            ...mapState({
                apiStatus: state => state.user.apiStatus, // API通信成否
            })
        },
        methods: {
            /**
             * アカウント削除処理
             * @return {Promise<void>}
             */
            async deleteAccount() {

                await this.$store.dispatch('user/deleteAccount')

                if (this.apiStatus) {
                    this.$router.push({name: 'authentication'})
                }
            }
        }
    }
</script>

<style scoped>
</style>
