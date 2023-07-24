module.exports = {
    'env': {
        'browser': true,
        'node': true, // Para o eslint não reclamar do require
        'es2021': true
    },
    'globals': {
        'route': 'readonly',
        'axios': 'readonly',
    },
    'extends': [
        'eslint:recommended',
        'plugin:vue/vue3-essential'
    ],
    'root': true,
    'ignorePatterns': ['webpack.mix.js'],
    'overrides': [
        {
            'env': {
                'node': true
            },
            'files': [
                'resources/js/*.vue',
                '.eslintrc.{js,cjs}'
            ],
            'parserOptions': {
                'sourceType': 'script'
            },
            'rules': {
                'vue/no-reserved-component-names': ['error', {
                    'disallowVueBuiltInComponents': false,
                    'disallowVue3BuiltInComponents': false
                }],
                'vue/multi-word-component-names': ['error', {
                    'ignores': [
                        'Head',
                        'Link',
                        'Breadcrumb',
                        'Button',
                        'Dialog',
                        'Checkbox',
                        'Menu',
                        'Message',
                        'Sidebar',
                        'Toast',
                        'Card'
                    ]
                }]
            }
        }
    ],
    'parserOptions': {
        'ecmaVersion': 'latest',
        'sourceType': 'module'
    },
    'plugins': [
        'vue'
    ],
    'rules': {
        'indent': [
            'error',
            4
        ],
        'linebreak-style': [
            'error',
            'unix'
        ],
        'quotes': [
            'error',
            'single'
        ],
        'semi': [
            'error',
            'never'
        ]
    }
}
