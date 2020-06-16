<template>
    <v-container class="container-mt medium-container">
        <v-card>
            <v-card-title>共有カレンダー検索</v-card-title>
            <v-card-text>
                <v-form ref="form" @submit.prevent="searchSharedCalendar">
                    <v-text-field
                        label="検索ID"
                        v-model="form.search_id"
                        outlined
                        :rules="[mixinValidationRules.required]"
                    >
                    </v-text-field>
                    <v-btn
                        block :color="mixinThemeColor"
                        dark type="submit"
                        class="font-weight-bold"
                    >検索</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<script>
    import validationRulesMixin from "../../../mixins/validationRulesMixin"
    import utilDataMixin from "../../../mixins/utilDataMixin";
    export default {
        name: "SearchSharedCalendar",
        mixins: [validationRulesMixin, utilDataMixin],
        data() {
            return {
                form: {
                    search_id: ''
                },
            }
        },
        methods: {
            /**
             * applicationSharedCalendarに遷移
             * @return {boolean}
             */
            searchSharedCalendar () {
                // バリデーションチェック
                if (!this.$refs.form.validate()){
                    return false
                }
                this.$router.push({name: 'applicationSharedCalendar', params: {searchId: this.form.search_id}})
            }
        }
    }
</script>

<style scoped>
</style>
