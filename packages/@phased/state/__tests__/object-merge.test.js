import { objectMerge } from "@/objectMerge";

let obj_1;
let obj_2;
beforeEach(function () {
  obj_1 = { a: 1 }
  obj_2 = { b: 2 }
});

describe("many objects can be merged together", () => {
  it("two simple objects get merged", () => {
    const obj_3 = objectMerge(obj_1, obj_2)
    expect(obj_1).toEqual({ a: 1 });
    expect(obj_2).toEqual({ b: 2 });
    expect(obj_3).toEqual({ a: 1, b: 2 });
  });

  it("later objects overwrite earlier objects", () => {
    const obj_3 = objectMerge(obj_1, { a: 5 })
    const obj_4 = (objectMerge(obj_1, obj_2, obj_3, { c: 9 }))
    expect(obj_1).toEqual({ a: 1 });
    expect(obj_3).toEqual({ a: 5 });
    expect(obj_4).toEqual({ a: 5, b: 2, c: 9 });
  });

  it("it creates an empty object when no valid arguments are passed", () => {
    expect(objectMerge()).toEqual({});
  });
});
