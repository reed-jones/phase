import { getLatestRelease, getLatestTag, createRelease } from "./github-api";
import {
  git,
  parseChangelogLine,
  acceptableLines,
  formatLine,
  formatDiff,
} from "./utils";

async function generateChangelog() {

  const { tag_name } = await getLatestRelease();
  const tag = await getLatestTag();

  const log =
    git(`log --pretty=format:"[%h] %s (%an <%ae>)" ${tag_name}...HEAD`)
      .map(parseChangelogLine)
      .filter(acceptableLines)
      .map(formatLine)
      .join("\n") || "No notable commits tracked";

  const pkgs = [
    ...new Set(
      git(
        [
          `diff --name-only ${tag_name}...HEAD packages`,
          `':(exclude)*/package.json'`,
          `':(exclude)*/composer.json'`,
          `':(exclude)*/__tests__/*'`,
        ].join(" ")
      )
        .map(formatDiff(tag))
        .filter(Boolean)
    ),
  ].join("\n");

  const date = new Date
  const formattedDate = `${date.getFullYear()}-${`${date.getMonth() + 1}`.padStart(2, '0')}-${`${date.getDate()}`.padStart(2, '0')}`;

  const body = [
    `## [${tag.name}](https://github.com/reed-jones/phase/compare/${tag_name}...${tag.name}) - ${formattedDate}`,
    '\n',
    `## Notable commits since the last release (${tag_name}...${tag.name}):`,
    `\n${log}`,
    `\n`,
    "## Packages updated in this release",
    `\n${pkgs}\n`,
  ].join("\n");

  return createRelease(tag.name, body)
}

generateChangelog()
    .then(console.log)
    .catch(console.error);
