<template>
    <div>
        <div class="loading-background">
            <vue-loading type="bubbles"
                         color="#160063"
                         :size="{ width: '100px', height: '100px'}"
            >
            </vue-loading>
        </div>
    </div>
</template>

<script>
    import {VueLoading} from 'vue-loading-template'
    export default {
        name: "LoadingScreen",
        components: {
            VueLoading
        }
    }
</script>

<style scoped>
    .loading-background{
        display: flex;
        justify-content: center;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 100vw ;
        z-index: 11;
        background: rgba(0, 0, 0, .1);
    }
</style>
