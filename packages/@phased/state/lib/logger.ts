import { ILogLevels, IPhaseLogger, IExtendedLogLevels } from '@phased/state'

const LOG_MAPPING: ILogLevels = {
    debug: 'log', // log everything
    info: 'log', // log some things
    notice: 'warn',
    warning: 'warn', // log warnings
    error: 'error', // log all errors
    critical: 'error', // log really bad errors
    alert: 'error',
    emergency: 'error'
}

const LOG_LEVELS = Object.keys(LOG_MAPPING)

export const createLogger = (logLevel: keyof ILogLevels): IPhaseLogger => {
    const shouldLog = (currentLevel: keyof IExtendedLogLevels) => {
        return LOG_LEVELS.indexOf(logLevel) <= LOG_LEVELS.indexOf(currentLevel)
    }

    const logger = <IPhaseLogger>new Proxy(console.log, {
        get(target, prop: keyof IExtendedLogLevels) {
            return (...args: any): any => {
                if (process.env.NODE_ENV === 'production') {
                    // noop in production
                    return () => { };
                }

                if (prop === 'raw') {
                    return console;
                }

                const level = LOG_MAPPING[prop] as keyof Console;
                return shouldLog(prop) && console[level](...args);
            }
        },
    });

    logger.debug('[Phase] Created Logger Instance')

    return logger;
}
