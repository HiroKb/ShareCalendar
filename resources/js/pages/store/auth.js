const state = {
    user: null
}

const getters = {}

const mutations = {
    setUser(state, user){
        state.user = user
    }
}

const actions = {
    // 会員登録処理
    async register(context, data) {
        // 会員登録APIの呼び出し
        const response = await axios.post('/api/register', data)
        context.commit('setUser', response.data)
    },
    // ログイン処理
    async login(context, data) {
        // ログインAPIの呼び出し
        const response = await axios.post('/api/login', data)
        context.commit('setUser', response.data)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
