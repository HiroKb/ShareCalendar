<template>
    <v-container class="fixed-content py-12">
        <v-card class="fill-height d-flex flex-column">
            <v-card-title class="flex-grow-0 flex-shrink-0">共有メンバー</v-card-title>
            <v-card-text
                class="mb-4 flex-grow-1 flex-shrink-1 custom-scrollbar"
                style="overflow-y: scroll"
            >
<!--                メンバーリスト-->
                <v-list>
                    <v-list-item
                        v-for="(member, index) in sharedCalendarMembers"
                        :key="index"
                    >
                        <v-list-item-avatar>
                            <v-img :src=" member.image_url || mixinNoImagePath"></v-img>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <p v-if="member.id === sharedCalendarData.admin_id">管理者</p>
                            <v-list-item-title class="title">{{member.name}}</v-list-item-title>
                        </v-list-item-content>

                        <v-list-item-action v-if="userId === sharedCalendarData.admin_id && member.id !== sharedCalendarData.admin_id">
                            <v-btn
                                small fab text
                                :color="mixinThemeColor"
                                @click="showRemoveMemberModal(member)"
                            >
                                <v-icon>mdi-account-remove-outline</v-icon>
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>
            </v-card-text>
        </v-card>

<!--        共有解除モーダル-->
        <v-dialog
            v-model="removeMemberModal.show"
            max-width="500px"
            v-if="userId === sharedCalendarData.admin_id"
        >
            <v-card>
                <div
                    class="d-flex flex-column align-center pt-5"
                >
                    <v-img
                        :src="removeMemberImageUrl"
                        height="200px"
                        width="200px"
                        aspect-ratio="1"
                        style="border-radius: 50%"
                    ></v-img>
                    <v-card-title class="pt-2">{{ removeMemberName }}</v-card-title>
                </div>
                <v-card-text>
                    <v-form @submit.prevent="removeMember">
                        <p>このユーザーとの共有を解除しますか？</p>
                        <v-btn
                            class="font-weight-bold"
                            block :color="mixinThemeColor"
                            dark type="submit"
                        >解除</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
    import {SUCCESS} from "../../../util"
    import utilDataMixin from "../../../mixins/utilDataMixin"
    import {mapGetters} from "vuex";
    export default {
        name: "SharedCalendarMembers",
        mixins: [utilDataMixin],
        data () {
            return {
                removeMemberModal: {
                    show: false, //モーダル表示フラグ
                    memberData: null // 共有解除するメンバーデータ
                }
            }
        },
        computed: {
            ...mapGetters({
                userId: 'user/userId'
            }),
            // 名前表示用
            removeMemberName: function () {
                return this.removeMemberModal.memberData ? this.removeMemberModal.memberData.name : ''
            },
            removeMemberImageUrl: function () {
                return this.removeMemberModal.memberData ? this.removeMemberModal.memberData.image_url ? this.removeMemberModal.memberData.image_url : this.mixinNoImagePath : ''
            }
        },
        props: {
            sharedCalendarData: {
                type: Object,
                required: true,
            },
            sharedCalendarMembers: {
                type: Array,
                required: true
            },
        },
        methods: {
            /**
             * 共有解除処理
             * @return {Promise<boolean>}
             */
            async removeMember() {
                if (!this.removeMemberModal.memberData) {
                    return false
                }

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarData.id + '/members/' + this.removeMemberModal.memberData.id)
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    this.$emit('removeMember', this.removeMemberModal.memberData.id)
                    this.$store.commit('flashMessage/setMessage', this.removeMemberModal.memberData.name + 'さんとの共有を解除しました')
                    this.removeMemberModal.show = false
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            },
            /**
             * モーダル表示処理
             * @param {Object} member 共有解除するメンバーデータ
             */
            showRemoveMemberModal(member) {
                this.removeMemberModal.show = true
                this.removeMemberModal.memberData = member
            },
        },
        watch: {
            /**
             * モーダルが消えた場合メンバーデータを初期化
             * @param {boolean} val モーダル表示フラグ
             */
            'removeMemberModal.show': function (val) {
                if (!val) {
                    this.removeMemberModal.memberData = null
                }
            }
        }
    }
</script>

<style scoped>
    .container{
        max-width: 600px;
    }
</style>
