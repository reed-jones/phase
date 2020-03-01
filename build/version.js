#!/usr/bin/env node
import { promises as fs } from "fs";
import prettier from "prettier";
import minimist from 'minimist'
import chalk from 'chalk'
const argv = minimist(process.argv.slice(2));

const VERSION = argv.version

if (!argv.version) {
    console.error("A Version is required to match the format '--version 0.0.0'")
    process.exit()
}

const packages = [
  "packages/@phased/laravel-mix",
  "packages/@phased/phase",
  "packages/@phased/routing",
  "packages/@phased/state",
  "packages/@phased/types",
  "packages/@phased/webpack-plugin"
];

const versionBump = deps =>
  Object.fromEntries(
    Object.entries(deps).map(([dep, version]) =>
      dep.startsWith("@phased") ? [dep, `^${VERSION}`] : [dep, version]
    )
  );

const promises = packages.map(async p => {
  const pkg = await import(`../${p}/package.json`).then(d => d.default);

  pkg.version = VERSION;

  pkg.dependencies = pkg.dependencies
    ? versionBump(pkg.dependencies)
    : pkg.dependencies;

  pkg.devDependencies = pkg.devDependencies
    ? versionBump(pkg.devDependencies)
    : pkg.devDependencies;

  pkg.peerDependencies = pkg.peerDependencies
    ? versionBump(pkg.peerDependencies)
    : pkg.peerDependencies;

  await fs.writeFile(
    `${p}/package.json`,
    prettier.format(JSON.stringify(pkg), { parser: "json" })
  );

  console.log(`${chalk.green(pkg.name)} has been updated to ${pkg.version}`);
});
