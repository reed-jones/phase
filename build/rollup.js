import sucrase from "@rollup/plugin-sucrase";
import commonjs from '@rollup/plugin-commonjs'
import resolve from "@rollup/plugin-node-resolve";
import { terser } from "rollup-plugin-terser";
import alias from "@rollup/plugin-alias";

export const { ROLLUP_WATCH = false, NODE_ENV = 'development' } = process.env;
export const production = !ROLLUP_WATCH && NODE_ENV === "production";

export const outputs = {
  cjs: pkg => ({ file: pkg.main, format: "cjs" }),
  esm: pkg => ({ file: pkg.module, format: "es" })
}

export const externals = {
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

export const plugins = {
  alias: alias({
    entries: [
      {
        find: "@",
        replacement: "./lib"
      }
    ],
    customResolver: resolve({
      extensions: ["ts"]
    })
  }),

  resolve: resolve({
    extensions: [".ts"]
  }),

  sucrase: sucrase({
    exclude: ["node_modules/**", "types/**", "__tests__"],
    transforms: ["typescript"]
  }),

  commonjs: commonjs({
    //
  }),

  terser: production && terser({
    //
  })
}
