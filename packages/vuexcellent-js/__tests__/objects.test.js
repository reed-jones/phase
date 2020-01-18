import { isObject, objectMerge } from "@/objectMerge";

describe("isObject", () => {
    it("it returns true with an empty object", () => {
        expect(isObject({})).toBe(true);
    });
});
