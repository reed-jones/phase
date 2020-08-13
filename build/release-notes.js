import { join } from 'path'
import { existsSync } from 'fs'
import { execSync } from 'child_process'
import fetch from 'node-fetch'
import semver from 'semver'

process.chdir(join(__dirname, '..'))

const parseChangelog = line => {
    const regex = /^\[(?<hash>.{7,7})\] (?<type>[\w\s\d]*)(\((?<scope>.*)\))?: (?<change>.*) \((?<author>.*)\)$/gm;
    let m;

    while ((m = regex.exec(line)) !== null) {
        // This is necessary to avoid infinite loops with zero-width matches
        if (m.index === regex.lastIndex) {
            regex.lastIndex++;
        }
        return m.groups;
    }
}

async function generateChangelog() {
    const releaseData = await fetch('https://api.github.com/repos/reed-jones/phase/releases')
    const release = await releaseData.json()
    release.sort((a,b) => semver.compare(b.tag_name, a.tag_name))
    const [latest, previous] = release // todo sort by version
    
    let { tag_name: latest_tag_name } = latest
    let { tag_name: previous_tag_name } = previous
    if (latest.draft) {
        return;
    }
    const tagData = await fetch('https://api.github.com/repos/reed-jones/phase/tags');
    const tags = await tagData.json()
    tags.sort((a,b) => semver.compare(b.name, a.name))

  const log = execSync(`git log --pretty=format:"[%h] %s (%an <%ae>)" ${previous_tag_name}...${latest_tag_name}`)
      .toString()
      .trim()
      .split('\n')
      .map(parseChangelog)
      .filter(line => line && !line.change.startsWith('wip') && (!line.scope || !line.scope.includes('release')))
      .sort((a,b) => a.type.localeCompare(b.type))
      .map(line => `[${line.hash}]: **${line.type}${line.scope ? `(_${line.scope}_)` : ''}**: ${line.change} - (${line.author})`)
      .join('\n') || 'NO CHANGES DETECTED';


  console.log(`Notable changes since the last stable release (${previous_tag_name}):`);
  console.log(`\n${log}\n`)


  const pkgs =
    Array.from(
      new Set(
        execSync(`git diff --name-only ${previous_tag_name}...${latest_tag_name}`)
          .toString()
          .trim()
          .split('\n')
          .filter(line => line.startsWith('packages/'))
          .map(line => ({ base: line.split('/')[1], package: line.split('/')[2] }))
          .map(pkg => {
              const file = `./packages/${pkg.base}/${pkg.package}/package.json`

              return {
                ...pkg, 
                version: existsSync(file) ? require(join('..', file)).version : tags[0].name.replace(/^v/, '')
            }
          })
          .map(pkg => `${pkg.base}/${pkg.package}@${pkg.version}`.toLowerCase())
          .filter(s => Boolean(s))
      )
    )

    console.log('Packages updated in this release')
    console.log(`\n${pkgs.join('\n')}\n`)
}

generateChangelog()
    .catch(console.error)
