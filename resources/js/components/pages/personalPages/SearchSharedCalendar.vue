<template>
    <v-container class="py-12">
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
                    <v-btn block :color="mixinThemeColor" dark type="submit">検索</v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>
<script>
    import validationRulesMixin from "../../../mixins/validationRulesMixin"
    import colorsMixin from "../../../mixins/colorsMixin";
    export default {
        name: "SearchSharedCalendar",
        mixins: [validationRulesMixin, colorsMixin],
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
    .container{
        max-width: 600px;
    }
</style>
