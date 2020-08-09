module.exports = {
    extends: ['@commitlint/config-conventional'],
    ignores: [commit => commit.trim() === 'wip'],
    rules: {
        'scope-enum': [2, 'always', ['state', 'routing', 'core', 'js', 'php']]
    }
}
