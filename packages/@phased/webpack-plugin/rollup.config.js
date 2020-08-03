import sucrase from "@rollup/plugin-sucrase";
import commonjs from "@rollup/plugin-commonjs";
import resolve from "@rollup/plugin-node-resolve";
import { terser } from "rollup-plugin-terser";
import alias from "@rollup/plugin-alias";
import pkg from "./package.json";

const { ROLLUP_WATCH = false, NODE_ENV = 'development' } = process.env;
const production = !ROLLUP_WATCH && NODE_ENV === "production";

const outputs = {
  cjs: pkg => ({ file: pkg.main, format: "cjs" }),
  esm: pkg => ({ file: pkg.module, format: "es" })
}

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

const plugins = {
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
    namedExports: {
      "fs-extra": ["outputFileSync"]
    }
  }),

  terser: production && terser({
    //
  })
}

export default [
  {
    input: "lib/index",
    output: [
      outputs.cjs(pkg),
      outputs.esm(pkg)
    ],
    external: [
      '@phased/routing',
      'webpack',
      ...externals.node
    ],
    plugins: [
      plugins.alias,
      plugins.resolve,
      plugins.sucrase,
      plugins.commonjs,
      plugins.terser
    ]
  }
];
