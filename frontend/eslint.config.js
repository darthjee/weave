import js from '@eslint/js';
import react from 'eslint-plugin-react';
import reactHooks from 'eslint-plugin-react-hooks';
import reactRefresh from 'eslint-plugin-react-refresh';
import jasmine from 'eslint-plugin-jasmine';
import globals from 'globals';

export default [
  // Ignore patterns
  {
    ignores: [
      'weave/static/**/*.js',
      'static/**/*.js',
      'report/**'
    ]
  },
  
  // Main configuration
  {
    files: ['**/*.{js,jsx}'],
    plugins: {
      react,
      'react-hooks': reactHooks,
      'react-refresh': reactRefresh,
      jasmine
    },
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      parserOptions: {
        ecmaFeatures: {
          jsx: true
        }
      },
      globals: {
        ...globals.browser,
        ...globals.commonjs,
        ...globals.es2021,
        ...globals.jasmine
      }
    },
    settings: {
      react: {
        version: 'detect'
      }
    },
    rules: {
      ...js.configs.recommended.rules,
      ...react.configs.recommended.rules,
      ...react.configs['jsx-runtime'].rules,
      ...reactHooks.configs.recommended.rules,
      
      // Custom rules
      'indent': ['error', 2],
      'linebreak-style': ['error', 'unix'],
      'quotes': ['error', 'single'],
      'semi': ['error', 'always'],
      'complexity': ['error', 10],
      'max-depth': ['error', 4],
      'max-lines': ['error', 300]
    }
  }
];
