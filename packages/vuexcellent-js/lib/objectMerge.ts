
/**
 * Checks if an item is an Object
 *
 * @param {Any} item
 *
 * @return {Boolean}
 */
export const isObject = (item: any) => item && typeof(item) === 'object' && !Array.isArray(item);

/**
 * Deep merge two objects.
 *
 * @param {Object} target
 * @param {...Object} sources
 */
const recursiveMerge = (target: { [key: string]: any }, ...sources: { [key: string]: any }[]): object => {
    // base case
    if (!sources.length) {
        return target;
    }

    // get first source
    const source = <object>sources.shift();

    if (isObject(target) && isObject(source)) {
        Object.entries(source).forEach(([key, value]: [string, any]) => {
            if (isObject(value)) {
                if (!target[key]) {
                    Object.assign(target, { [key]: {} });
                }
                target[key] = recursiveMerge(target[key], value);
            } else {
                Object.assign(target, { [key]: value });
            }
        });
    }

    return recursiveMerge(target, ...sources);
};

/**
 * Merges two or more objects together
 *
 * @param  {...Object} sources
 *
 * @return {Object}
 */
export const objectMerge = (...sources: object[]): object => recursiveMerge({}, ...sources)
