<template>
    <transition name="slide">
        <div class="message-wrap" v-show="message">
            <p class="message">{{ message }}</p>
        </div>
    </transition>
</template>

<script>
    import {mapState} from "vuex";

    export default {
        name: "FlashMessage",
        props: {
            message: {
                type: String,
                default: null
            }
        }
    }
</script>

<style scoped>

    .slide-enter-active, .slide-leave-active {
        transition: all .5s;
    }
    .slide-enter,
    .slide-leave-to {
        transform: translateY(-30px);
    }
    .slide-enter-to,
    .slide-leave {
        transform: translateY(0);
    }
    .message-wrap{
        position: fixed;
        top: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100vw;
        height: 30px;
        background: #160063;
        z-index: 1;
        color: white;
    }
    .message{
        font-size: 24px;
    }
</style>
