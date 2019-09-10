module.exports = {
    root: true,
    parserOptions: {
        parser: 'babel-eslint',
        "ecmaVersion": 6,
    },
    'plugins': [
        'prettier'
    ],
    'extends': [
        'plugin:vue/essential',
        'plugin:prettier/recommended'
    ],
    rules: {
        'new-cap': 0,
        'no-new': 0,
        'no-tabs': 0,
        'prettier/prettier': 'warn'
    }
}