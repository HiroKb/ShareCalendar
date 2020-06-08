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
    userId: state => state.user ? state.user.id : null,
    // ユーザーの名前
    userName: state => state.user ? state.user.name : null,
    // ユーザー画像
    userImage: state => state.user ? state.user.image_url ? state.user.image_url : '/images/gray.jpg' : '/images/gray.jpg',
    // ユーザーのメールアドレス
    userEmail: state => state.user ? state.user.email : null,
    // OAuth認証を利用したユーザーか
    isOAuth: state => state.user ? state.user.isOAuth : null
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
        context.commit('loading/setLoadingFlg', true, { root: true})
        // 会員登録APIの呼び出し
        const response = await axios.post('/api/register', data)

        context.commit('loading/setLoadingFlg', false, { root: true})

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
        context.commit('loading/setLoadingFlg', true, { root: true})
        // ログインAPIの呼び出し
        const response = await axios.post('/api/login', data)

        context.commit('loading/setLoadingFlg', false, { root: true})
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
        context.commit('loading/setLoadingFlg', true, { root: true})

        // ログアウトAPIの呼び出し
        const response = await axios.post('/api/logout')

        context.commit('loading/setLoadingFlg', false, { root: true})

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
        const response = await axios.get('/api/users')
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
        context.commit('loading/setLoadingFlg', true, { root: true})

        // ユーザー名変更APIの呼び出し
        const response = await axios.patch('/api/users/name', data)

        context.commit('loading/setLoadingFlg', false, { root: true})

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
    // ユーザー画像変更
    async updateImage(context, data){
        // apiStatusの初期化
        context.commit('setApiStatus', null)
        context.commit('loading/setLoadingFlg', true, { root: true})

        const response = await axios.post('/api/users/image', data)

        context.commit('loading/setLoadingFlg', false, { root: true})

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
    // ユーザーパスワード変更
    async updateEmail(context, data) {
        // apiStatusの初期化
        context.commit('setApiStatus', null)
        context.commit('loading/setLoadingFlg', true, { root: true})

        // メールアドレス変更APIの呼び出し
        const response = await axios.patch('/api/users/email', data)

        context.commit('loading/setLoadingFlg', false, { root: true})

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
        context.commit('loading/setLoadingFlg', true, { root: true})
        // パスワード変更APIの呼び出し
        const response = await axios.patch('/api/users/password', data)

        context.commit('loading/setLoadingFlg', false, { root: true})
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
    async deleteAccount(context){
        // apiStatusの初期化
        context.commit('setApiStatus', null)
        context.commit('loading/setLoadingFlg', true, { root: true})

        const response = await axios.delete('/api/users')

        context.commit('loading/setLoadingFlg', false, { root: true})
        // 通信成功時
        if(response.status === SUCCESS) {
            context.commit('setApiStatus', true)
            context.commit('setUser', null)
            return false
        }

        // エラー時
        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, {root: true })
    }

}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
