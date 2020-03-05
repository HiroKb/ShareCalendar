const state = {
    user: null
}

const getters = {
    // ログインしているかどうか
    loginCheck: state => !! state.user,
    // ユーザー情報
    getUser: state => state.user ? state.user : ''
}

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
    },
    // ログアウト処理
    async logout (context) {
        const response = await axios.post('/api/logout')
        context.commit('setUser', null)
    },
    // ログインユーザー取得
    async getLoginUser(context) {
        // ログインユーザー取得APIの呼び出し
        const response = await axios.get('/api/user')
        const user = response.data || null
        context.commit('setUser', user)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
