export default {
    computed: {
        mixinValidationRules: function () {
            return{
                required: value => !!value || '必須項目です。',
                email: value => !!value.match(/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i) || 'メールアドレスの形式を確認してください。',
                min8: value => value.length >= 8 || '8文字以上で入力してください。',
                max50: value => value.length <= 50 || '50文字以内で入力してください。',
                max100: value => value.length <= 100 || '100文字以内で入力してください。',
                max255: value => value.length <= 255 || '255文字以内で入力してください。',
                image: value => !value || !!value.type.match('image.*') || 'ファイルの形式を確認してください。'
            }
        }
    }
}
