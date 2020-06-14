<template>
    <v-container class="container-mt medium-container">
        <v-card>
            <v-card-title>404</v-card-title>
            <v-card-text>
                <p>お探しのページが見つかりませんでした。</p>
                <router-link :to="{name: 'top'}">TOPページ</router-link>
                <a style="text-decoration: underline"
                   class="ml-4"
                   @click.prevent="$router.go(-1)" v-if="!!from"
                >一つ前のページ</a>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    export default {
        name: "NotFound",
        data() {
            return{
                from: null
            }
        },
        /**
         * どこからアクセスしたかをfromに代入
         * @param to
         * @param from
         * @param next
         */
        beforeRouteEnter (to, from, next) {
            next(vm => {
                vm.from = from.name
            })
        }
    }
</script>

<style scoped>
</style>
