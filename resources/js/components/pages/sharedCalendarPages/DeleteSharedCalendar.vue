<template>
    <v-container class="py-12">
        <v-card>
            <v-card-title class="pb-2">共有カレンダー削除</v-card-title>
            <v-card-title class="pt-0">{{ sharedCalendarData.calendar_name}}</v-card-title>
            <v-card-text>
                <p class="mb-0">共有カレンダー内のスケジュール、チャット等のデータが全て削除されます</p>
                <p>この共有カレンダーを削除しますか？</p>
                <v-form ref="form" @submit.prevent="deleteSharedCalendar">
                    <v-btn
                        class="font-weight-bold"
                        block :color="mixinThemeColor" dark
                        type="submit"
                    >削除</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import {SUCCESS} from "../../../util";
    import utilDataMixin from "../../../mixins/utilDataMixin"
    export default {
        name: "DeleteSharedCalendar",
        mixins: [utilDataMixin],
        props: {
            sharedCalendarData: {
                type: Object,
                required: true,
                default: () => ({})
            }
        },
        methods: {
            /**
             * カレンダー削除処理
             * @return {Promise<boolean>}
             */
            async deleteSharedCalendar() {
                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarData.id)
                this.$store.commit('loading/setLoadingFlg', false)

                if(response.status === SUCCESS) {
                    this.$store.commit('flashMessage/setMessage',  this.sharedCalendarData.calendar_name + 'カレンダーを削除しました。')
                    this.$router.push({name: 'personalCalendar'})
                    return false
                }
                this.$store.commit('error/setCode', response.status)
            }
        }
    }
</script>

<style scoped>
    .container{
        max-width: 600px;
    }
</style>
