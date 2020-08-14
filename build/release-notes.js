import { join } from "path";
import { existsSync } from "fs";
import { execSync } from "child_process";
import fetch from "node-fetch";
import semver from "semver";

process.chdir(join(__dirname, ".."));

const parseChangelog = (line) => {
  const regex = /^\[(?<hash>.{7,7})\] (?<type>[\w\s\d]*)(\((?<scope>.*)\))?: (?<change>.*) \((?<author>.*)\)$/gm;
  let m;

  while ((m = regex.exec(line)) !== null) {
    // This is necessary to avoid infinite loops with zero-width matches
    if (m.index === regex.lastIndex) {
      regex.lastIndex++;
    }
    return m.groups;
  }
};

async function generateChangelog() {
  const sections = [];

  const releaseData = await fetch(
    "https://api.github.com/repos/reed-jones/phase/releases/latest"
  );
  const { tag_name } = await releaseData.json();

  const tagData = await fetch(
    "https://api.github.com/repos/reed-jones/phase/tags"
  );
  const tags = await tagData.json();
  tags.sort((a, b) => semver.compare(b.name, a.name));

  const log =
    execSync(`git log --pretty=format:"[%h] %s (%an <%ae>)" ${tag_name}...HEAD`)
      .toString()
      .trim()
      .split("\n")
      .map(parseChangelog)
      .filter(
        (line) =>
          line &&
          !line.change.startsWith("wip") &&
          (!line.scope || !line.scope.includes("release"))
      )
      .sort((a, b) => a.type.localeCompare(b.type))
      .map(
        (line) =>
          `[${line.hash}]: **${line.type}${
            line.scope ? `(**_${line.scope}_**)` : ""
          }**: ${line.change} - (${line.author})`
      )
      .join("\n") || "No notable changes tracked";

  sections.push(
    `## Notable changes since the last stable release (${tag_name}):`
  );
  sections.push(`\n${log}\n`);

  const pkgs = Array.from(
    new Set(
      execSync(
        `git diff --name-only ${tag_name}...HEAD packages ':(exclude)*/package.json' ':(exclude)*/composer.json' ':(exclude)*/__tests__/*'`
      )
        .toString()
        .trim()
        .split("\n")
        .map((line) => {
          const [_, _base, _package] = line.split('/')
          const file = `./packages/${_base}/${_package}/package.json`;
          const isForNpm = existsSync(file);
          const version = isForNpm
            ? require(join("..", file)).version
            : tags[0].name.replace(/^v/, "");

          const pkg = {
            base: _base,
            package: _package,
            version,
            manager: isForNpm ? "npm" : "composer",
          };
          
          return `[${`_${pkg.manager}_`.padEnd(10, " ")}] **${pkg.base}/${
            pkg.package
          }**@_${pkg.version}_`.toLowerCase()
        })
        .filter((s) => Boolean(s))
    )
  );

  sections.push("## Packages updated in this release");
  sections.push(`\n${pkgs.join("\n")}\n`);

  console.log(sections.join("\n"));
}

generateChangelog().catch(console.error);
