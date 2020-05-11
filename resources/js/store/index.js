import Vue from 'vue'
import Vuex from 'vuex'

import user from "./user"
import error from "./error";
import loading from "./loading"

Vue.use(Vuex)

const store = new Vuex.Store({
    modules: {
        user,
        error,
        loading
    }
})

export default store
