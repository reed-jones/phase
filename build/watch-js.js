#!/usr/bin/env node
import { readFileSync } from "fs";
import chalk from "chalk";
import path from 'path';
import execa from 'execa'
import { packages } from './utils'

const { parse } = JSON

    async function watchPackages(packages) {

    for(let pkg of packages) {
        process.chdir(path.resolve(__dirname, '../', pkg))
        const { name, version, scripts } = parse(readFileSync('./package.json', 'utf-8'))

        if (! scripts.build) {
            continue;
        }
        console.log(chalk.yellow(''.padEnd(40, '-')))
        console.log('', chalk.yellow(name.padEnd(25, ' ')), chalk.yellow(version.padEnd(15, ' ')))
        execa('yarn', ['run', 'dev'], { stdio: 'inherit', buffer: false })

        console.log() // empty for new line
    }
}


watchPackages(packages)
