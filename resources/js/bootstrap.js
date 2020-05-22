import { getCookieValue } from "./util"

window._ = require('lodash')

window.moment = require('moment')

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios')

// Ajaxリクエストであることを示すヘッダを付与
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// トークンを取り出してヘッダーに添付
window.axios.interceptors.request.use(config => {
    config.headers['X-XSRF-TOKEN'] = getCookieValue('XSRF-TOKEN')

    return config
})

// 通信エラー時のレスポンスを変更(エラーそのものではなくレスポンスオブジェクトを返す)
window.axios.interceptors.response.use(
    response => response,
    error => error.response || error
)

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
