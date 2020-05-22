<template>
    <v-card>
        <v-card-title>{{ scheduleData.date }}</v-card-title>
        <v-card-text>
            <v-form ref="form" @submit.prevent="updateSchedule">
                <v-container class="pa-0">
                    <v-row>
                        <v-col cols="6" class="pb-0">
                            <v-select
                                ref="hour"
                                v-model="form.hour"
                                :items="mixinFormTimes.hour"
                                item-text="label"
                                item-value="value"
                                :rules="[validationHour]"
                                return-object
                                label="時"
                                outlined
                                dense
                            ></v-select>
                        </v-col>
                        <v-col cols="6" class="pb-0">
                            <v-select
                                ref="minute"
                                v-model="form.minute"
                                :items="mixinFormTimes.minute"
                                item-text="label"
                                item-value="value"
                                :rules="[validationMinute]"
                                return-object
                                label="分"
                                outlined
                                dense
                            ></v-select>
                        </v-col>
                    </v-row>
                </v-container>


                <v-text-field
                    v-model="form.title"
                    :error="errorMessages ? !!errorMessages.title : false"
                    :error-messages="errorMessages ? errorMessages.title ? errorMessages.title : [] : []"
                    counter="50"
                    label="スケジュール名 ※必須"
                    outlined
                    dense
                ></v-text-field>

                <v-textarea
                    v-model="form.description"
                    :error="errorMessages ? !!errorMessages.description : false"
                    :error-messages="errorMessages ? errorMessages.description ? errorMessages.description : [] : []"
                    counter="100"
                    label="詳細"
                    outlined
                    dense
                    rows="4"
                >
                </v-textarea>
                <v-btn class="my-0" block :color="mixinThemeColor" dark type="submit">スケジュール更新</v-btn>

            </v-form>
        </v-card-text>
    </v-card>
</template>

<script>
    import formTimeMixin from "../../mixins/formTimeMixin"
    import validationRulesMixin from "../../mixins/validationRulesMixin"
    import colorsMixin from "../../mixins/colorsMixin"
    export default {
        name: "EditScheduleFormCard",
        mixins: [formTimeMixin, validationRulesMixin, colorsMixin],
        data() {
            return {
                form: {
                    hour: {label: '指定なし', value: 'unspecified'},
                    minute: {label: '指定なし', value: 'unspecified'},
                    title: '',
                    description: ''
                },
            }
        },
        props: {
            // 更新するスケジュールデータ
            scheduleData: {
                type: Object,
                required: true,
                default: () => ({})
            },
            // バックエンドバリデーションエラーメッセージ
            errorMessages: {
                type: Object,
                required: true,
                default: () => ({})
            }
        },
        methods: {
            // 時刻のバリデーションメソッド
            validationHour(val){
                if (val.value === 'unspecified' && this.form.minute.value !== 'unspecified'){
                    return '時刻の形式を確認してください'
                }
                return true
            },
            validationMinute(val){
                if (val.value === 'unspecified' && this.form.hour.value !== 'unspecified'){
                    return '時刻の形式を確認してください'
                }
                return true
            },
            // scheduleData(更新するスケジュールデータ)をもとにformの値を変更
            changeForm() {
                const schedule = this.scheduleData
                let hour, minute

                if (schedule.time) {
                    hour = schedule.time.substr(0, 2)
                    minute = schedule.time.substr(3, 2)
                }else{
                    hour = 'unspecified'
                    minute = 'unspecified'
                }

                this.form = {
                    hour: this.mixinFormTimes.hour.find((obj)=> obj.value === hour),
                    minute: this.mixinFormTimes.minute.find((obj)=> obj.value === minute),
                    title: schedule.title ? schedule.title : '',
                    description: schedule.description ? schedule.description : ''
                }
            },
            // フォームのリセット
            resetForm() {
                this.$refs.form.resetValidation()
            },
            // スケジュール更新リクエストをエミット
            updateSchedule() {
                // バリデーション
                if (!this.$refs.form.validate()){
                    return false
                }

                // スケジュール更新用のデータを作成
                const time = this.form.hour.value === 'unspecified'
                    ? null
                    : this.form.hour.value + ':' + this.form.minute.value + ':00'
                const description = !!this.form.description ? this.form.description : null
                const data = {
                    time: time,
                    title: this.form.title,
                    description: description
                }

                this.$emit('updateScheduleRequest', data)
            }
        },
        watch: {
            // scheduleData(変更するスケジュール)が変更された場合フォームを更新
            scheduleData: {
                handler: function (val) {
                    Object.keys(val).length && this.changeForm()
                },
                deep: true,
                immediate: true
            },
            // 時刻フォームで指定なしが選択された場合もう一方の時刻フォームも指定なしに変更
            'form.hour.value': function (val) {
                if(val === 'unspecified'){
                    this.form.minute = {label: '指定なし', value: 'unspecified'}
                }
            },
            'form.minute.value': function (val) {
                if(val === 'unspecified'){
                    this.form.hour = {label: '指定なし', value: 'unspecified'}
                }
            },

        }
    }
</script>

<style scoped>

</style>
