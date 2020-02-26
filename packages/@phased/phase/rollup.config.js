import sucrase from "@rollup/plugin-sucrase";
import commonjs from "@rollup/plugin-commonjs";
import resolve from "@rollup/plugin-node-resolve";
import { terser } from "rollup-plugin-terser";
import alias from "@rollup/plugin-alias";
import pkg from "./package.json";

const production = !process.env.ROLLUP_WATCH && process.env.NODE_ENV === 'production';

const externals = {
  node: [
    "path",
    "fs",
    "child_process",
    "os",
    "assert",
    "events",
    "util",
    "module",
    "stream",
    "constants"
  ]
}

export default [
  {
    input: "lib/index",
    output: [
      { file: pkg.main, format: "cjs" },
      { file: pkg.module, format: "es" }
    ],
    external: [
      // State
      "axios",
      "vue",
      "vuex",

      // Routing
      "fs-extra",
      "prettier",

      // Webpack
      'webpack',
      'laravel-mix',
      ...externals.node
    ],
    plugins: [
      alias({
        entries: [ { find: "@", replacement: "./lib" } ],
        customResolver: resolve({ extensions: ['ts'] })
      }),

      resolve({ extensions: [".js", ".ts"] }),

      sucrase({
        exclude: ["node_modules/**", "types/**", "__tests__"],
        transforms: ["typescript"]
      }),

      commonjs({ namedExports: { "fs-extra": ["outputFileSync"] } }),

      production && terser()
    ]
  }
];
