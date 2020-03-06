<template>
    <div>

        <p>{{ status }}</p>

        <RouterView />
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import { INTERNAL_SERVER_ERROR } from './util'

    export default {
        computed:{
            status: function () {
                if(this.$store.getters['auth/loginCheck']){
                    return 'login [' + this.$store.getters['auth/getUser'].name + ']'
                }
                 return 'logout'
            },
            ...mapState({
                errorCode: state => state.error.code
            })
        },
        watch: {
            errorCode: { // エラーコードに変化があった場合
                handler (val) {
                    if (val === INTERNAL_SERVER_ERROR) {
                        this.$router.push('/500')
                    }
                },
                immediate: true
            },
            $route () { // ルーティングに変化があった場合
                this.$store.commit('error/setCode', null)
            }
        }
    }
</script>
