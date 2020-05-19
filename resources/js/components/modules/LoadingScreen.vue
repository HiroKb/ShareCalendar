<template>
    <v-overlay :value="loadingFlg" z-index="203">
        <v-progress-circular
            size="100"
            width="8"
            :color="colors.themeColor"
            indeterminate
        ></v-progress-circular>
    </v-overlay>
</template>

<script>
    import colorsMixin from "../../mixins/colorsMixin"
    export default {
        name: "LoadingScreen",
        mixins: [colorsMixin],
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
