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
                if(this.$store.state.auth.user){
                    return 'login'
                }
                 return 'logout'
            }
        }
    }
</script>
