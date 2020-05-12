const state = {
    message: null
}

const mutations = {
    setMessage (state, message) {
        state.message = message

        setTimeout(() => {
            state.message = null
        }, 3000)
    }
}

export default {
    namespaced: true,
    state,
    mutations
}
