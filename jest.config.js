module.exports = {
    // testMatch: ["tests/@phased/state/*.[jt]s?(x)"],
    transform: {
      ".(js|jsx|ts|tsx)": "@sucrase/jest-plugin"
    },
    moduleNameMapper: {
      "^@/(.*)$": "../lib/$1"
    },
    transformIgnorePatterns: [],
    testPathIgnorePatterns: ['./__tests__/config'],
  };
