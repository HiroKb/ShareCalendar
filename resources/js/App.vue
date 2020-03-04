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
                    return 'login [' + this.$store.state.auth.user.name + ']'
                }
                 return 'logout'
            }
        }
    }
</script>
