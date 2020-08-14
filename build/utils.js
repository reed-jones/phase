import { execSync } from "child_process";
import { existsSync } from "fs";
import { join } from 'path'

export const packages = [
  "packages/@phased/types",
  "packages/@phased/state",
  "packages/@phased/routing",
  "packages/@phased/webpack-plugin",
  "packages/@phased/laravel-mix",
  "packages/@phased/phase",
];

export function git(cmd) {
  return execSync(`git ${cmd}`).toString().trim().split("\n");
}

// release/changelog parseing
export function parseChangelogLine(line) {
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

export function acceptableLines(line) {
  if (!line) return false;
  if (line.change.startsWith("wip")) return false;
  if (!line.scope || !line.scope.includes("release")) return true;
  return false;
}

export function formatLine(line) {
  return `[${line.hash}]: **${line.type}${
    line.scope ? `(**_${line.scope}_**)` : ""
  }**: ${line.change} - (${line.author})`;
}

export function formatDiff(tag) {
  return function(line) {
  const [_, _base, _package] = line.split("/")
  const file = `./packages/${_base}/${_package}/package.json`
  const isForNpm = existsSync(file)
  const version = isForNpm
    ? require(join("..", file)).version
    : tag.name.replace(/^v/, "")

  const pkg = {
    base: _base,
    package: _package,
    version,
    manager: isForNpm ? "npm" : "composer",
  }

  return `[${`_${pkg.manager}_`.padEnd(10, " ")}] **${pkg.base}/${
    pkg.package
  }**@_${pkg.version}_`.toLowerCase();
  }
}
