import { packages } from './utils'
import execa from 'execa'
import chalk from "chalk";

async function releasePackages(packages) {
    packages.forEach(pkg => {
      process.chdir(path.resolve(__dirname, '../', pkg))
      console.log('Publishing...', chalk.yellow(process.cwd()))
      execa.sync('npm publish --access public')
    })
}

releasePackages(packages)
