import fs from 'fs'

export const fileEqualsCode = (to: string, code: string): boolean => {
    const isEqual = (file: string, code: string): boolean => {
      return fs.readFileSync(file, "utf8").trim() === code.trim();
    };
    return fs.existsSync(to) && isEqual(to, code);
  };

  export const writeCodeToFile = (to: string, code: string): void => {
    if (fileEqualsCode(to, code)) {
      return;
    }

    return fs.writeFileSync(to, code);
  };
