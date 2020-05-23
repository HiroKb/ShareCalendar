<template>
    <v-container class="py-12">
        <v-card v-if="sharedCalendarData.status">
            <v-card-title>カレンダー共有申請</v-card-title>
            <v-card-text>
                <template v-if="sharedCalendarData.status === 'NotShared'">
                <p class="title transparent-black">{{'管理者: ' + sharedCalendarData.admin_name}}</p>
                <v-form ref="form" @submit.prevent="applicationSharedCalendar">
                    <v-btn block :color="mixinThemeColor" dark type="submit">共有申請</v-btn>
                </v-form>
                </template>

                <p class="title transparent-black" v-else-if="sharedCalendarData.status === 'Shared'">共有済みのカレンダーです。</p>

                <p class="title transparent-black" v-else-if="sharedCalendarData.status === 'Applied'">共有申請済みのカレンダーです。</p>

                <p class="title transparent-black" v-else>共有カレンダーが見つかりませんでした。</p>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import {CREATED, SUCCESS} from "../../../util";
    import colorsMixin from "../../../mixins/colorsMixin";
    export default {
        name: "ApplicationSharedCalendar",
        mixins: [colorsMixin],
        data() {
            // 検索結果
            return {
                sharedCalendarData: {}
            }
        },
        props: {
            // カレンダー検索ID
            searchId: {
                required: true,
                default: ''
            }
        },
        methods: {
            /**
             * カレンダー共有申請処理
             */
            async applicationSharedCalendar () {

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.put('/api/shared-calendars/' + this.sharedCalendarData.search_id + '/applications')
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === CREATED) {
                    this.$store.commit('flashMessage/setMessage', 'カレンダーの共有申請を行いました。')
                    this.$router.push({name: 'personalCalendar'})
                    return false
                }
                this.$store.commit('error/setCode', response.status)
            },
            /**
             * カレンダーの検索処理
             * @return {Promise<boolean>}
             */
            async searchSharedCalendar () {

                this.$store.commit('loading/setLoadingFlg', true)
                const response =  await axios.get('/api/shared-calendars/' + this.searchId + '/search')
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    this.sharedCalendarData = response.data
                    return false
                }

                this.$store.commit('error/setCode', response.status)
            }
        },
        created() {
            this.searchSharedCalendar()
        }
    }
</script>

<style scoped>
    .container{
        max-width: 600px;
    }
</style>
