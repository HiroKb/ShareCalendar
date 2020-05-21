const state = {
    message: null,
    timerId: null
}

const mutations = {
    setMessage (state, message) {
        state.message = message

        state.timerId && clearTimeout(state.timerId)

        state.timerId = setTimeout(() => {
            state.message = null
        }, 4000)
    }
}

export default {
    namespaced: true,
    state,
    mutations
}
