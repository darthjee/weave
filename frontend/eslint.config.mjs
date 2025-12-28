import { defineConfig } from "eslint/config";
import complexity from "eslint-plugin-complexity";

export default defineConfig([
{
    ignores: [
        'node_modules/**/*.js',
        'dist/**/*.js',
        'report/**'
    ]
},
{
    plugins: {
        complexity,
    },

    rules: {
        complexity: ["warn", {
            max: 10,
        }],

        "max-lines": ["warn", {
            max: 300,
        }],

        "max-depth": ["warn", {
            max: 4,
        }],
    },
}]);