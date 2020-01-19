import { isObject } from "@/objectMerge";

describe("isObject", () => {
  it("it returns true with objects", () => {
    expect(isObject({})).toBe(true);
    expect(isObject({ test: 7 })).toBe(true);
  });

  it("it returns false with non-objects", () => {
    expect(isObject(null)).toBe(false);
    expect(isObject([])).toBe(false);
    expect(isObject([{}])).toBe(false);
    expect(isObject()).toBe(false);
    expect(isObject(true)).toBe(false);
    expect(isObject(false)).toBe(false);
    expect(isObject(undefined)).toBe(false);
    expect(isObject("Hello")).toBe(false);
    expect(isObject("")).toBe(false);
    expect(isObject(0)).toBe(false);
    expect(isObject(42)).toBe(false);
  });
});
