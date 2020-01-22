import { execSync } from "child_process";
import { PhasePhpOptions } from "@phased/routing";

export const artisan = (cmd: string, raw: boolean = false): PhasePhpOptions => {
    return raw
      ? execSync(`php artisan ${cmd}`).toString()
      : JSON.parse(execSync(`php artisan ${cmd}`).toString());
  };
