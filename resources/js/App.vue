<template>
    <div>
        <RouterView />
        <loading-screen v-show="loadingFlg"></loading-screen>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import {INTERNAL_SERVER_ERROR, NOT_FOUND} from './util'
    import LoadingScreen from "./components/modules/LoadingScreen.vue"

    export default {
        components: {
            LoadingScreen
        },
        computed: {
            ...mapState({
                errorCode: state => state.error.code,
                loadingFlg: state => state.loading.loadingFlg
            })
        },
        watch: {
            errorCode: { // エラーコードに変化があった場合
                handler (val) {
                    if (val === INTERNAL_SERVER_ERROR) {
                        this.$router.push('/500')
                    }else if (val === NOT_FOUND) {
                        this.$router.push('/404')
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

<style>
    html, body, div, span, object, iframe,
    h1, h2, h3, h4, h5, h6, p, blockquote, pre,
    abbr, address, cite, code,
    del, dfn, em, img, ins, kbd, q, samp,
    small, strong, sub, sup, var,
    b, i,
    dl, dt, dd, ol, ul, li,
    fieldset, form, label, legend,
    table, caption, tbody, tfoot, thead, tr, th, td,
    article, aside, canvas, details, figcaption, figure,
    footer, header, hgroup, menu, nav, section, summary,
    time, mark, audio, video {
        margin:0;
        padding:0;
        border:0;
        outline:0;
        font-size:100%;
        vertical-align:baseline;
        background:transparent;
    }

    body {
        line-height:1;
    }

    article,aside,details,figcaption,figure,
    footer,header,hgroup,menu,nav,section {
        display:block;
    }

    nav ul {
        list-style:none;
    }

    blockquote, q {
        quotes:none;
    }

    blockquote:before, blockquote:after,
    q:before, q:after {
        content:'';
        content:none;
    }

    a {
        margin:0;
        padding:0;
        font-size:100%;
        vertical-align:baseline;
        background:transparent;
    }

    /* change colours to suit your needs */
    ins {
        background-color:#ff9;
        color:#000;
        text-decoration:none;
    }

    /* change colours to suit your needs */
    mark {
        background-color:#ff9;
        color:#000;
        font-style:italic;
        font-weight:bold;
    }

    del {
        text-decoration: line-through;
    }

    abbr[title], dfn[title] {
        border-bottom:1px dotted;
        cursor:help;
    }

    table {
        border-collapse:collapse;
        border-spacing:0;
    }

    /* change border colour to suit your needs */
    hr {
        display:block;
        height:1px;
        border:0;
        border-top:1px solid #cccccc;
        margin:1em 0;
        padding:0;
    }

    input, select {
        vertical-align:middle;
    }

    *{
        box-sizing: border-box;
    }

    ul, ol{
        list-style: none;
    }

    .sidebar-wrap{
        position: fixed;
        z-index: 1;
    }
    .contents{
        max-width: 800px;
        margin: 0 auto;
    }
</style>
