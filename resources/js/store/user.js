import { SUCCESS, CREATED, VALIDATION_ERROR} from "../util";

const state = {
    user: null, // ログインユーザーデータ
    apiStatus: null, // 通信成否
    errorMessages: null // エラーメッセージ
}

const getters = {
    // ログインしているかどうか
    loginCheck: state => !! state.user,
    // ユーザーのID
    userId: state => state.user ? state.user.id : '',
    // ユーザーの名前
    userName: state => state.user ? state.user.name : '',
    // ユーザーのメールアドレス
    userEmail: state => state.user ? state.user.email : ''
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
        // apiStatusの初期化
        context.commit('setApiStatus', null)
        // ログアウトAPIの呼び出し
        const response = await axios.post('/api/logout')

        // 通信成功時
        if(response.status === SUCCESS) {
            context.commit('setApiStatus', true)
            context.commit('setUser', null)
            return false
        }

        // エラー時
        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, {root: true })
    },
    // ログインユーザー取得
    async getLoginUser(context) {
        // apiStatusの初期化
        context.commit('setApiStatus', null)
        // ログインユーザー取得APIの呼び出し
        const response = await axios.get('/api/user')
        const user = response.data || null

        if (response.status === SUCCESS) {
            context.commit('setUser', user)
            return false
        }
        // エラー時
        context.commit('error/setCode', response.status, {root: true })
    },
    // ユーザー名変更
    async updateName(context, data) {
        // apiStatusの初期化
        context.commit('setApiStatus', null)
        // ユーザー名変更APIの呼び出し
        const response = await axios.patch('/api/user-name', data)

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
    // ユーザー名変更
    async updateEmail(context, data) {
        // apiStatusの初期化
        context.commit('setApiStatus', null)
        // メールアドレス変更APIの呼び出し
        const response = await axios.patch('/api/user-email', data)

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
    // パスワード変更
    async updatePassword(context, data) {
        // apiStatusの初期化
        context.commit('setApiStatus', null)
        // パスワード変更APIの呼び出し
        const response = await axios.patch('/api/user-password', data)

        // 通信成功時
        if(response.status === SUCCESS) {
            context.commit('setApiStatus', true)
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

}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
