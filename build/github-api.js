import fetch from 'node-fetch'
import semver from 'semver'

let latestRelease = null;
export async function getLatestRelease() {
    if (latestRelease) return latestRelease
    const releaseData = await fetch("https://api.github.com/repos/reed-jones/phase/releases/latest")
    return latestRelease = await releaseData.json()
}

let releases = []
export async function getReleases() {
    if (releases.length) return releases
    const releasesData = await fetch("https://api.github.com/repos/reed-jones/phase/releases")
    return releases = await releasesData.json();
}

export async function createRelease(version, body) {
    const data = {
        tag_name: version,
        name: version,
        draft: false,
        prerelease: false,
        body
    }

	const response = await fetch('https://api.github.com/repos/reed-jones/phase/releases', {
		method: 'post',
		body: JSON.stringify(data),
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/vnd.github.v3+json',
            Authorization: `token ${process.env.GITHUB_TOKEN}`
        }
	});
	return await response.json();
}

let tags = []
export async function getTags() {
    if (tags.length) return tags
    const tagsData = await fetch("https://api.github.com/repos/reed-jones/phase/tags")
    const rawTags = await tagsData.json()
    return tags = rawTags.sort((a,b) => semver.compare(b.name, a.name))
}

export async function getLatestTag() {
    if (tags.length) return tags[0]
    await getTags()
    return tags[0]
}
