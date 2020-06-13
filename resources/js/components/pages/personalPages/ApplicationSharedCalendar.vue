<template>
    <v-container class="py-12">
        <v-card v-if="Object.keys(sharedCalendarData).length">
            <v-card-title>カレンダー共有申請</v-card-title>
            <v-card-text>
                <template v-if="sharedCalendarData.isApplicable">
                    <div class="d-flex flex-column align-center">
                        <v-img
                            :src="sharedCalendarData.admin_image || mixinNoImagePath"
                            height="200px"
                            width="200px"
                            aspect-ratio="1"
                            style="border-radius: 50%"
                        ></v-img>
                        <p class="title transparent-black">{{'管理者: ' + sharedCalendarData.admin_name}}</p>
                    </div>
                <v-form ref="form" @submit.prevent="applicationSharedCalendar">
                    <v-btn block :color="mixinThemeColor" dark type="submit">共有申請</v-btn>
                </v-form>
                </template>
                <p class="title transparent-black" v-else>{{sharedCalendarData.message}}</p>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import {CREATED, SUCCESS} from "../../../util";
    import utilDataMixin from "../../../mixins/utilDataMixin";
    export default {
        name: "ApplicationSharedCalendar",
        mixins: [utilDataMixin],
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

                const searchId = this.searchId.replace(/\u002f/g, '')

                this.$store.commit('loading/setLoadingFlg', true)
                const response =  await axios.get('/api/shared-calendars/' + searchId + '/search')
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
