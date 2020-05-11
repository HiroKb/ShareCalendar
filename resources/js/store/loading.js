const state = {
    loadingFlg: false
}

const mutations = {
    setLoadingFlg (state, boolean) {
        state.loadingFlg = boolean
    }
}

export default {
    namespaced: true,
    state,
    mutations
}
