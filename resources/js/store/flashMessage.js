const state = {
    message: null,
    timerId: null
}

const mutations = {
    setMessage (state, message) {
        state.message = message

        // timerIdが存在する場合(既にメッセージが表示されている場合)リセット処理をキャンセル
        state.timerId && clearTimeout(state.timerId)

        // 4秒後にメッセージをリセット
        state.timerId = setTimeout(() => {
            state.message = null
            state.timerId = null
        }, 4000)
    }
}

export default {
    namespaced: true,
    state,
    mutations
}
