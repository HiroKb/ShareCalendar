<template>
    <div class="message-wrap">
        <v-alert
            :value="!!message"
            :color="colors.themeColor"
            dark
            dense
            tile
            class="text-center"
            transition="scroll-y-transition"
        >{{message}}</v-alert>
    </div>
</template>

<script>
    import colorsMixin from "../../mixins/colorsMixin";
    export default {
        name: "FlashMessage",
        mixins:[colorsMixin],
        props: {
            message: {
                type: String,
                default: null
            }
        }
    }
</script>

<style scoped>
    .message-wrap{
        position: absolute;
        top: 0;
        width: 100%;
        z-index: 1;
    }
</style>
