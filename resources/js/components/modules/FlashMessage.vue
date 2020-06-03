<template>
    <div class="message-wrap">
        <v-alert
            :value="!!message"
            :color="mixinThemeColor"
            dark
            dense
            tile
            class="text-center"
            transition="scroll-y-transition"
        >{{message}}</v-alert>
    </div>
</template>

<script>
    import utilDataMixin from "../../mixins/utilDataMixin";
    export default {
        name: "FlashMessage",
        mixins:[utilDataMixin],
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
