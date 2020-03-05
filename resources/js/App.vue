<template>
    <div>

        <p>{{ status }}</p>

        <RouterView />
    </div>
</template>

<script>
    export default {
        computed:{
            status: function () {
                if(this.$store.getters['auth/loginCheck']){
                    return 'login [' + this.$store.getters['auth/getUser'].name + ']'
                }
                 return 'logout'
            }
        }
    }
</script>
