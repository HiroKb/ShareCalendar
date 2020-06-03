<template>
    <v-container class="py-12">
        <v-card>
            <v-card-title>共有カレンダー作成</v-card-title>
            <v-card-text>
                <v-form ref="form" @submit.prevent="createShareCalendar">
                    <v-text-field
                        label="カレンダー名"
                        v-model="form.calendar_name"
                        outlined
                        :rules="[mixinValidationRules.required]"
                        :error="errors ? !!errors.calendar_name : false"
                        :error-messages="errors ? errors.calendar_name ? errors.calendar_name : [] : []"
                    >
                    </v-text-field>
                    <v-btn block :color="mixinThemeColor" dark type="submit">作成</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    import {CREATED, VALIDATION_ERROR} from "../../../util"
    import validationRulesMixin from "../../../mixins/validationRulesMixin"
    import utilDataMixin from "../../../mixins/utilDataMixin";
    export default {
        name: "CreateShareCalendar",
        mixins: [validationRulesMixin, utilDataMixin],
        data(){
            return {
                form: {
                    calendar_name: ''
                },
                errors: {}, //カレンダー作成APIバリデーションエラーメッセージ
            }
        },
        methods: {
            /**
             * カレンダー作成処理
             * @return {Promise<boolean>}
             */
            async createShareCalendar () {
                // バリデーションチェック
                if (!this.$refs.form.validate()){
                    return false
                }

                this.$store.commit('loading/setLoadingFlg', true)
                const response = await axios.post('/api/shared-calendars', this.form)
                this.$store.commit('loading/setLoadingFlg', false)


                if (response.status === CREATED) {
                    this.$store.commit('flashMessage/setMessage', this.form.calendar_name + 'カレンダーを作成しました。')
                    this.$router.push({name: 'sharedCalendar', params: {sharedCalendarId: response.data.id}})
                    return false
                }

                if (response.status === VALIDATION_ERROR) {
                    this.errors = response.data.errors
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
