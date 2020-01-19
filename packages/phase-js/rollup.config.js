import sucrase from "@rollup/plugin-sucrase";
import commonjs from "@rollup/plugin-commonjs";
import resolve from "@rollup/plugin-node-resolve";
import { terser } from "rollup-plugin-terser";
import alias from "@rollup/plugin-alias";
import pkg from "./package.json";

const production = !process.env.ROLLUP_WATCH && process.env.NODE_ENV === 'production';

export default [
  {
    input: "lib/index",
    output: [
      { file: pkg.main, format: "cjs" },
      { file: pkg.module, format: "es" }
    ],
    // external: Object.keys(pkg.peerDependencies),
    external: [
      "webpack",
      "laravel-mix",
      "fs-extra",
      "prettier",
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
    ],
    plugins: [
      alias({
        entries: [
          {
            find: "@",
            replacement: "./lib",
          }, {
            find: "@typings",
            replacement: "./types"
          }
        ],
        customResolver: resolve({
          extensions: ['ts']
        })
      }),

      resolve({
        extensions: [".js", ".ts"]
        // preferBuiltins: true,
        // jail: '/'
        // only: ['fs-extra', 'lodash.merge', 'prettier', './lib/index.ts']
      }),

      sucrase({
        exclude: ["node_modules/**", "types/**", "__tests__"],
        transforms: ["typescript"]
      }),

      commonjs({
        namedExports: {
          // left-hand side can be an absolute path, a path
          // relative to the current directory, or the name
          // of a module in node_modules
          // 'lodash.merge': ['named']
          "fs-extra": ["outputFileSync"]
        }
      }),

      production && terser()
    ]
  }
];
