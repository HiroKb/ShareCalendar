<template>
    <v-card>
        <v-card-title>{{ selectedDateLabel }}</v-card-title>
        <v-card-text>
            <v-form ref="form" @submit.prevent="createSchedule">
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
                    :rules="[mixinValidationRules.required, mixinValidationRules.max50]"
                    :error="errorMessages ? !!errorMessages.title : false"
                    :error-messages="errorMessages ? errorMessages.title ? errorMessages.title : [] : []"
                    counter="50"
                    label="スケジュール名 ※必須"
                    outlined
                    dense
                ></v-text-field>

                <v-textarea
                    v-model="form.description"
                    :rules="[mixinValidationRules.max100]"
                    :error="errorMessages ? !!errorMessages.description : false"
                    :error-messages="errorMessages ? errorMessages.description ? errorMessages.description : [] : []"
                    counter="100"
                    label="詳細"
                    outlined
                    dense
                    rows="4"
                >
                </v-textarea>
                <v-btn class="my-0" block :color="mixinThemeColor" dark type="submit">スケジュール追加</v-btn>

            </v-form>
        </v-card-text>
    </v-card>
</template>

<script>
    import formTimeMixin from "../../mixins/formTimeMixin"
    import validationRulesMixin from "../../mixins/validationRulesMixin"
    import colorsMixin from "../../mixins/colorsMixin"
    export default {
        name: "CreateScheduleFormCard",
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
            // 選択日(スケジュールを追加する日)
            selectedDateLabel: {
                type: String,
                required: true,
                default: null
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
            // フォームのリセット
            resetForm() {
                this.form = {
                    hour: {label: '指定なし', value: 'unspecified'},
                    minute: {label: '指定なし', value: 'unspecified'},
                    title: '',
                    description: ''
                }
                this.$refs.form.resetValidation()
            },
            /**
             * スケジュール作成リクエストをエミット
             * @returns {boolean}
             */
            createSchedule() {
                // バリデーション
                if (!this.$refs.form.validate()){
                    return false
                }

                // スケジュール作成用のデータを作成
                const time = this.form.hour.value === 'unspecified'
                    ? null
                    : this.form.hour.value + ':' + this.form.minute.value + ':00'
                const description = !!this.form.description ? this.form.description : null
                const data = {
                    date: this.selectedDateLabel, // 'YYYY-MM-DD'
                    time: time, // null or 'HH:mm:ss'
                    title: this.form.title, // String
                    description: description // null or String
                }

                this.$emit('createScheduleRequest', data)
            }
        },
        watch: {
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
            }
        }
    }
</script>

<style scoped>

</style>
