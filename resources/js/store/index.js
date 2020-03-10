import Vue from 'vue'
import Vuex from 'vuex'

import user from "./user"
import error from "./error";

Vue.use(Vuex)

const store = new Vuex.Store({
    modules: {
        user,
        error
    }
})

export default store
