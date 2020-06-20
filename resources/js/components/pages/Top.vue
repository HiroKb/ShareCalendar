<template>
    <v-container class="container-mt">
        <v-row>
            <v-col cols="12" sm="6" order="2" order-sm="1">
                <v-img
                    src="/images/topimage.png"
                ></v-img>
            </v-col>
            <v-col
                cols="12" sm="6" order="1" order-sm="2"
                class="d-flex flex-column align-center justify-center"
            >
                <p class="display-1">スケジュール共有を<br>もっとシンプルに</p>
                <v-btn
                    class="font-weight-bold mt-6 d-none d-sm-inline-flex"
                    dark
                    :color="mixinThemeColor"
                    :to="{name: 'authentication'}"
                >はじめる</v-btn>
            </v-col>
            <v-col cols="12" order="3" class="d-sm-none d-flex justify-center">
                <v-btn
                    class="font-weight-bold mt-6"
                    dark block
                    :color="mixinThemeColor"
                    :to="{name: 'authentication'}"
                >はじめる</v-btn>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import utilDataMixin from "../../mixins/utilDataMixin";
    export default {
        name: "Top",
        mixins:[utilDataMixin]
    }
</script>

<style scoped>

</style>
