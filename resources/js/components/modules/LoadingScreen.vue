<template>
    <v-overlay :value="loadingFlg" z-index="203">
        <v-progress-circular
            size="100"
            width="8"
            :color="mixinThemeColor"
            indeterminate
        ></v-progress-circular>
    </v-overlay>
</template>

<script>
    import utilDataMixin from "../../mixins/utilDataMixin"
    export default {
        name: "LoadingScreen",
        mixins: [utilDataMixin],
        props: {
            loadingFlg: {
                type: Boolean,
                required: true,
                default: false
            }
        }
    }
</script>

<style scoped>
</style>
