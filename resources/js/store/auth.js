import { SUCCESS, CREATED, VALIDATION_ERROR} from "../util";

const state = {
    user: null, // ログインユーザーデータ
    apiStatus: null, // 通信成否
    errorMessages: null // エラーメッセージ
}

const getters = {
    // ログインしているかどうか
    loginCheck: state => !! state.user,
    // ユーザー情報
    getUser: state => state.user ? state.user : ''
}

const mutations = {
    setUser (state, user) {
        state.user = user
    },
    setApiStatus (state, status) {
        state.apiStatus = status
    },
    setErrorMessages (state, messages) {
        state.errorMessages = messages
    }
}

const actions = {
    // 会員登録処理
    async register(context, data) {
        // apiStatusの初期化
        context.commit('setApiStatus', null)
        // 会員登録APIの呼び出し
        const response = await axios.post('/api/register', data)

        // 通信成功時
        if(response.status === CREATED) {
            context.commit('setApiStatus', true)
            context.commit('setUser', response.data)
            return false
        }

        // エラー時
        context.commit('setApiStatus', false)
        // バリデーションエラーの場合
        if (response.status === VALIDATION_ERROR) {
            context.commit('setErrorMessages', response.data.errors)

        // その他のエラーの場合
        } else {
            context.commit('error/setCode', response.status, { root: true})
        }
    },
    // ログイン処理
    async login(context, data) {
        // apiStatusの初期化
        context.commit('setApiStatus', null)
        // ログインAPIの呼び出し
        const response = await axios.post('/api/login', data)

        // 通信成功時
        if(response.status === SUCCESS) {
            context.commit('setApiStatus', true)
            context.commit('setUser', response.data)
            return false
        }

        // エラー時
        context.commit('setApiStatus', false)
        // バリデーションエラーの場合
        if (response.status === VALIDATION_ERROR) {
            context.commit('setErrorMessages', response.data.errors)

            // その他のエラーの場合
        } else {
            context.commit('error/setCode', response.status, { root: true})
        }
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
