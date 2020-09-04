#!/usr/bin/env node
import { promises as fs } from "fs";
import prettier from "prettier";
import minimist from "minimist";
import chalk from "chalk";
import path from 'path';
import execa from 'execa'
import { packages } from './utils'
import { getLatestRelease } from './github-api'
import semver from "semver";
const argv = minimist(process.argv.slice(2));

const VERSION = argv.version;

if (!argv._.length) {
  console.error(chalk.red("A semver version level must be provided"));
  process.exit();
}

function defaultIdentifierFor(level) {
  return level.startsWith('pre') ? 'beta' : ''
}

const [level, identifier = defaultIdentifierFor(level)] = argv._
const allowedLevels = ['major', 'minor', 'patch', 'premajor', 'preminor', 'prepatch', 'prerelease']

if (!allowedLevels.includes(level)) {
  console.error(chalk.red(`Please use a valid semver level: ${allowedLevels.join(', ')}`))
  process.exit();
}

async function findNextVersion(level, identifier) {
  const latest = await getLatestRelease()
  return semver.inc(latest.tag_name, level, identifier)
}

const versionBump = (deps, next) => {
  return Object.fromEntries(
    Object.entries(deps).map(([dep, version]) =>
      dep.startsWith("@phased") ? [dep, `^${next}`] : [dep, version]
    )
  )
}

async function bumpPackageVersions(packages, version) {
  const promises = packages.map(async p => {
    const pkgJson = await import(`../${p}/package.json`).then(d => d.default);

    pkgJson.version = version;

    pkgJson.dependencies = pkgJson.dependencies
      ? versionBump(pkgJson.dependencies, version)
      : pkgJson.dependencies;

      pkgJson.devDependencies = pkgJson.devDependencies
      ? versionBump(pkgJson.devDependencies, version)
      : pkgJson.devDependencies;

      pkgJson.peerDependencies = pkgJson.peerDependencies
      ? versionBump(pkgJson.peerDependencies, version)
      : pkgJson.peerDependencies;

    await fs.writeFile(
      `${p}/package.json`,
      prettier.format(JSON.stringify(pkgJson), { parser: "json" })
    );

    console.log(
      `${chalk.green(pkgJson.name)} has been updated to ${chalk.green(
        "v" + pkgJson.version
      )}`
    );
  });

  return Promise.all(promises)
}

async function installPackageDependencies(packages) {
    packages.forEach(pkg => {
      process.chdir(path.resolve(__dirname, '../', pkg))
      console.log('installing...', chalk.yellow(process.cwd()))
      execa.sync('yarn')
    })
}

async function run(packages, level, identifier) {
  const version = await findNextVersion(level, identifier);
  await bumpPackageVersions(packages, version)
  await installPackageDependencies(packages)
}

run(packages, level, identifier)
