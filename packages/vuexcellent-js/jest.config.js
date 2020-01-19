module.exports = {
  transform: {
    ".(js|jsx|ts|tsx)": "@sucrase/jest-plugin"
  },
  moduleNameMapper: {
    "^@/(.*)$": "<rootDir>/lib/$1"
  },
  transformIgnorePatterns: [],
  testPathIgnorePatterns: ['./__tests__/config'],
};
