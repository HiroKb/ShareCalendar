import Vue from 'vue'
import Vuex from 'vuex'

import user from "./user"
import error from "./error";
import loading from "./loading"
import flashMessage from "./flashMessage"

Vue.use(Vuex)

const store = new Vuex.Store({
    modules: {
        user,
        error,
        loading,
        flashMessage
    }
})

export default store
