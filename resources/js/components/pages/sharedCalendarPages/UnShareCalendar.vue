<template>
    <v-container class="py-12">
        <v-card>
            <v-card-title class="pb-2">共有解除</v-card-title>
            <v-card-title class="pt-0">{{ sharedCalendarData.calendar_name}}</v-card-title>
            <v-card-text>
                <p>個人カレンダーに共有スケジュールが表示されなくなりますが、<br/>作成した共有スケジュールや投稿したチャットメッセージは削除されません。</p>
                <p>共有を解除しますか？</p>
                <v-form ref="form" @submit.prevent="unShareCalendar">
                    <v-btn block :color="mixinThemeColor" dark type="submit">解除</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import {SUCCESS} from "../../../util";
    import colorsMixin from "../../../mixins/colorsMixin"
    export default {
        name: "UnShareCalendar",
        mixins: [colorsMixin],
        props: {
            sharedCalendarData: {
                type: Object,
                required: true,
                default: () => ({})
            },
        },
        methods: {
            /**
             * 共有解除処理
             * @return {Promise<boolean>}
             */
            async unShareCalendar () {
                if (!this.sharedCalendarData.id) {
                    return false
                }

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.delete('/api/shared-calendars/' + this.sharedCalendarData.id + '/members')
                this.$store.commit('loading/setLoadingFlg', false)

                if (response.status === SUCCESS) {
                    this.$store.commit('flashMessage/setMessage', this.sharedCalendarData.calendar_name+ 'カレンダーの共有を解除しました')
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
