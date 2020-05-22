export default {
    computed: {
        mixinUtilMethods: function() {
            return {
                /**
                 * 時刻フォーマットを変更
                 * '01:02:03'->'1:02', '11:22:33'->'11:22'
                 * @param time
                 * @returns {string}
                 */
                timeFormatter(time) {
                    if (time.toString().charAt(0) === '0'){
                        return time.toString().substr(1, 4)
                    }
                    return time.toString().substr(0, 5)
                },
            }
        }
    },
}
