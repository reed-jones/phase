import sucrase from "@rollup/plugin-sucrase";
import commonjs from '@rollup/plugin-commonjs'
import resolve from "@rollup/plugin-node-resolve";
import { terser } from "rollup-plugin-terser";
import alias from "@rollup/plugin-alias";
import pkg from "./package.json";

const production = !process.env.ROLLUP_WATCH;

export default [
  {
    input: "lib/index",
    output: [
      { file: pkg.main, format: "cjs" },
      { file: pkg.module, format: "es" }
    ],
    external: [ 'vue', 'vuex', 'axios' ],
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
        ]
      }),

      resolve({
        extensions: [".js", ".ts"],
      }),

      sucrase({
        exclude: ["node_modules/**", "types/**", "__tests__"],
        transforms: ["typescript"]
      }),

      commonjs({
        //
      }),
      production && terser()
    ]
  }
];
