// ステータスコード
export const SUCCESS = 200
export const CREATED = 201
export const AUTHENTICATION_REQUIRED = 401
export const EMAIL_NOT_VERIFIED = 443
export const NOT_FOUND = 404
export const CSRF_NOT_EXIST = 419
export const ONLY_UNAUTHENTICATED = 420
export const VALIDATION_ERROR= 422
export const INTERNAL_SERVER_ERROR = 500

    /**
 * クッキーの値を取得
 * @param {string} searchKey 検索するキー
 * @returns {string} キーに対応する値
 */
export function getCookieValue(searchKey) {
    if (typeof searchKey === 'undefined'){
        return ''
    }

    let val = ''


    // クッキーは cookie1=123;cookie2=456;cookie3=789 の様な形式で参照できる
    // ';'で分割[cookie1=123, cookie2=456, cookie3=789]の様な配列に変換後forEachで回す
    document.cookie.split(';').forEach(cookie => {
        // '='で分割後key, valueに分割代入
        const [key, value] = cookie.split('=')
        if(key === searchKey) {
            return val = value
        }
    })

    return val
}
