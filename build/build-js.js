#!/usr/bin/env node
import { readFileSync } from "fs";
import prettier from "prettier";
import minimist from "minimist";
import chalk from "chalk";
import path from 'path';
import execa from 'execa'
const { parse } = JSON

const packages = [
    "packages/@phased/types",
    "packages/@phased/state",
    "packages/@phased/routing",
    "packages/@phased/webpack-plugin",
    "packages/@phased/laravel-mix",
    "packages/@phased/phase"
];

packages.forEach(pkg => {
    process.chdir(path.resolve(__dirname, '../', pkg))
    const { name, version, scripts } = parse(readFileSync('./package.json', 'utf-8'))
    if (!scripts.build) {
        console.log(chalk.red('skipping...'.padEnd(15, ' ')), chalk.yellow(name.padEnd(25, ' ')), chalk.yellow(version.padEnd(15, ' ')), )
        return;
    }

    console.log(chalk.green('installing...'.padEnd(15, ' ')), chalk.yellow(name.padEnd(25, ' ')), chalk.yellow(version.padEnd(15, ' ')), )
    execa.sync('yarn', ['install'])
    console.log(chalk.green('building...'.padEnd(15, ' ')), chalk.yellow(name.padEnd(25, ' ')), chalk.yellow(version.padEnd(15, ' ')), )
    execa.sync('yarn', ['run', 'build'])
})
