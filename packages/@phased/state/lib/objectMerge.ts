/**
 * Checks if an item is an Object
 *
 * @param {Any} item
 *
 * @return {Boolean}
 */
export const isObject = (item: any) =>
  !!item && typeof item === "object" && !Array.isArray(item);

/**
 * Deep merge two objects.
 *
 * @param {Object} target
 * @param {...Object} sources
 */
const recursiveMerge = (
  target: { [key: string]: any },
  ...sources: { [key: string]: any }[]
): object => {
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
  } else {
    console.warn(
      `[@phased/state] The server side data does not match client side expectations.
Server: ${JSON.stringify(source)}.
Client: ${JSON.stringify(target)}`
    );

    // set the value to match server request
    target = source;
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
export const objectMerge = (...sources: object[]): object => {
  if (process.env.NODE_ENV !== "production") {
    if (!sources.every(a => isObject(a))) {
      console.warn("Invalid arguments supplied. Could not properly merge");
    }
  }

  return recursiveMerge({}, ...sources);
};
