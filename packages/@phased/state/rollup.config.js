import pkg from "./package.json";
import { outputs, plugins } from '../../../build/rollup'

export default [
  {
    input: "lib/index",
    output: [
      outputs.cjs(pkg),
      outputs.esm(pkg)
    ],
    external: Object.keys(pkg.peerDependencies),
    plugins: [
      plugins.alias,
      plugins.resolve,
      plugins.sucrase,
      plugins.commonjs,
      plugins.terser
    ]
  }
];
