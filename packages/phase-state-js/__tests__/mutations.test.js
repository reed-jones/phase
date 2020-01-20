import { mutantGenerator } from "@/mutations";

describe("get mutations", () => {
  it("it generates single layer deep mutations", () => {
    const { getMutation } = mutantGenerator();
    expect(getMutation("user")).toBe("X_SET_USER");
  });

  it("it generates shallow nested mutations", () => {
    const { getMutation } = mutantGenerator();
    expect(getMutation("active", "users")).toBe("users/X_SET_ACTIVE");
  });

  it("it generates arbitrarily nested deep mutations", () => {
    const { getMutation } = mutantGenerator();
    expect(getMutation("active", "users/other/great")).toBe(
      "users/other/great/X_SET_ACTIVE"
    );
  });

  it("Without a key, it throws", () => {
    const { getMutation } = mutantGenerator();
    expect(_ => getMutation()).toThrow();
  });
});

describe("it creates mutations based on the supplied state", () => {
  it("it allows for arbitrary prefixes", () => {
    const { createMutant } = mutantGenerator({
      mutationPrefix: "RANDOM_PREFIX"
    });
    const original = { state: { name: "Reed" } };
    expect(Object.keys(createMutant(original).mutations)).toEqual([
      "RANDOM_PREFIX_NAME"
    ]);
  });

  it("it generates mutations for the base state", () => {
    const { createMutant } = mutantGenerator();
    const original = { state: { name: "Reed" } };
    const generated = {
      state: { name: "Reed" },
      mutations: { X_SET_NAME: () => {} }
    };

    expect(createMutant(original).state).toEqual(generated.state);
    expect(Object.keys(createMutant(original).mutations)).toEqual(
      Object.keys(generated.mutations)
    );
    expect(Object.keys(createMutant(original).mutations)).toEqual([
      "X_SET_NAME"
    ]);
  });

  it("it merges mutations for the base state", () => {
    const { createMutant } = mutantGenerator();
    const original = {
      state: { name: "Reed" },
      mutations: {
        EXPAND_NAME: state => (state.name += ` ${state.name}`)
      }
    };
    const generated = {
      state: { name: "Reed" },
      mutations: {
        EXPAND_NAME: state => (state.name += ` ${state.name}`),
        X_SET_NAME: () => {}
      }
    };

    expect(createMutant(original).state).toEqual(generated.state);
    expect(Object.keys(createMutant(original).mutations)).toEqual(
      Object.keys(generated.mutations)
    );
    expect(Object.keys(createMutant(original).mutations)).toEqual([
      "EXPAND_NAME",
      "X_SET_NAME"
    ]);
  });

  it("it generates mutations for nested modules", () => {
    const { createMutant } = mutantGenerator();
    const original = {
      modules: {
        names: {
          state: { first: "Reed" }
        }
      }
    };
    const generated = {
      modules: {
        names: {
          state: { first: "Reed" },
          mutations: { X_SET_FIRST: () => {} }
        }
      }
    };

    expect(createMutant(original).modules.names.state).toEqual(
      generated.modules.names.state
    );
    expect(Object.keys(createMutant(original).modules.names.mutations)).toEqual(
      Object.keys(generated.modules.names.mutations)
    );
    expect(
      Object.keys(createMutant(original).modules.names.mutations)
    ).toEqual(["X_SET_FIRST"]);
  });

  it("generates mutations for super duper nested modules", () => {
    const { createMutant } = mutantGenerator();
    const original = {
      modules: {
        a: {
          modules: {
            b: {
              modules: { c: { modules: { d: { state: { silly: true } } } } }
            }
          }
        }
      }
    };
    expect(
      Object.keys(
        createMutant(original).modules.a.modules.b.modules.c.modules.d.mutations
      )
    ).toEqual(["X_SET_SILLY"]);
  });
});
