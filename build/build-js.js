#!/usr/bin/env node
import { readFileSync, existsSync } from "fs";
import prettier from "prettier";
import minimist from "minimist";
import chalk from "chalk";
import path from 'path';
import execa from 'execa'
import { packages } from './utils'
import gzipSize from 'gzip-size';
import brotliSize from 'brotli-size'; // todo
import prettyBytes from 'pretty-bytes';
const { parse } = JSON

packages.forEach(pkg => {
    process.chdir(path.resolve(__dirname, '../', pkg))
    const { name, version, scripts, main:pkgMain, module:pkgModule } = parse(readFileSync('./package.json', 'utf-8'))
    console.log(chalk.green('installing...'.padEnd(15, ' ')), chalk.yellow(name.padEnd(25, ' ')), chalk.yellow(version.padEnd(15, ' ')))
    execa.sync('yarn', ['install'])

    if (scripts.build) {
        console.log(chalk.green('building...'.padEnd(15, ' ')), chalk.yellow(name.padEnd(25, ' ')))
        execa.sync('yarn', ['run', 'build'])
    } else {
        console.log(chalk.red('no build step...'))
    }

    if (pkgMain && existsSync(pkgMain)) {
        const mainSize = gzipSize.fileSync(pkgMain)
        console.log(''.padEnd(15, ' '), chalk.blue('common'.padEnd(25, ' ')), chalk.cyan(prettyBytes(mainSize).padEnd(25, ' ')))
    }

    if (pkgModule && existsSync(pkgModule)) {
        const moduleSize = gzipSize.fileSync(pkgModule)
        console.log(''.padEnd(15, ' '), chalk.blue('esm'.padEnd(25, ' ')), chalk.cyan(prettyBytes(moduleSize).padEnd(25, ' ')))
    }

    console.log() // empty for new line
})
