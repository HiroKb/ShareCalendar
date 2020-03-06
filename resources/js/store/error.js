const state = {
    code: null // ステータスコード
}

const mutations = {
    setCode (state, code) {
        state.code = code
    }
}

export default {
    namespaced: true,
    state,
    mutations
}
