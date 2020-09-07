#!/usr/bin/env node
import { readFileSync, existsSync } from "fs";
import chalk from "chalk";
import path from 'path';
import execa from 'execa'
import { packages } from './utils'
import gzipSize from 'gzip-size';
// import brotliSize from 'brotli-size'; // todo
import prettyBytes from 'pretty-bytes';
import ora from 'ora'
const { parse } = JSON

async function buildPackages(packages) {

    for(let pkg of packages) {
        process.chdir(path.resolve(__dirname, '../', pkg))
        const { name, version, scripts, main:pkgMain, module:pkgModule } = parse(readFileSync('./package.json', 'utf-8'))
        console.log(chalk.yellow(''.padEnd(40, '-')))
        console.log('', chalk.yellow(name.padEnd(25, ' ')), chalk.yellow(version.padEnd(15, ' ')))
        const installSpinner = ora(chalk.green('installing...'.padEnd(15, ' ')), chalk.yellow(name.padEnd(25, ' ')), chalk.yellow(version.padEnd(15, ' '))).start()
        await execa('yarn', ['install'])
        installSpinner.stopAndPersist()

        if (scripts.build) {
            const buildSpinner = ora(chalk.green('building...'.padEnd(15, ' ')), chalk.yellow(name.padEnd(25, ' '))).start()
            await execa('yarn', ['run', 'build'])
            buildSpinner.stopAndPersist()
        } else {
            console.log(chalk.red('  no build step...'))
        }

        if (pkgMain && existsSync(pkgMain)) {
            const mainSize = gzipSize.fileSync(pkgMain)
            console.log(chalk.blue('  common (gzip)'.padEnd(25, ' ')), chalk.cyan(prettyBytes(mainSize).padEnd(25, ' ')))
        }

        if (pkgModule && existsSync(pkgModule)) {
            const moduleSize = gzipSize.fileSync(pkgModule)
            console.log(chalk.blue('  esm (gzip)'.padEnd(25, ' ')), chalk.cyan(prettyBytes(moduleSize).padEnd(25, ' ')))
        }

        console.log() // empty for new line
    }
}


buildPackages(packages)
