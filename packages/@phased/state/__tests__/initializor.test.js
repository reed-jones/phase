import Vuex from "vuex";
import Vue from "vue";
import axios from "axios";
import { hydrate } from "..";

jest.mock("axios");
console.warn = jest.fn()
jest.spyOn(console, 'warn');


Vue.use(Vuex);

let store;
beforeEach(function() {
  global.window.__PHASE_STATE__ = {
    state: {
      isTesting: true,
      someObject: {},
      someArray: [],
      someNumber: 1,
      someNull: null,
      someString: 'test'
    },
    modules: {
      user: {
        state: {
          last_name: "Jones"
        }
      },

      other: {
        namespaced: false,
        state: {
          first_name: "Reed"
        }
      }
    }
  };

  store = new Vuex.Store(
    hydrate(
      {
        state: {
          count: 1,
          someObject: [],
          someArray: {},
          someNumber: null,
          someNull: 57,
          someString: true
        },
        mutations: {
          add: state => (state.count = state.count + 1)
        },
        modules: {
          user: {
            namespaced: true,
            state: {
              first_name: "Reed"
            }
          },

          other: {
            namespaced: false
          },

          deep: {
            namespaced: true,
            modules: {
              nested: {
                namespaced: true,
                modules: {
                  modules: {
                    namespaced: true,
                    state: {
                      blah: "yes"
                    }
                  }
                }
              }
            }
          }
        }
      },
      { axios, logLevel: 'emergency' }
    )
  );
});

afterEach(() => {
  jest.clearAllMocks();
});

describe("testing the default store actions", () => {

  it("It takes the servers state in the event of a type collision", () => {
    expect(store.state.someObject).toEqual({});
    expect(store.state.someArray).toEqual([]);
    expect(store.state.someNumber).toEqual(1);
    expect(store.state.someNull).toEqual(null);
    expect(store.state.someString).toEqual('test');
    expect(console.warn.mock.calls[0][0]).toEqual(
      expect.stringContaining("The server side data does not match client side expectations")
    )
  });

  it("regular state mutations still work", () => {
    expect(store.state.count).toBe(1);
    store.commit("add");
    expect(store.state.count).toBe(2);
  });

  it("generated base mutations work", () => {
    expect(store.state.count).toBe(1);
    store.commit("X_SET_COUNT", 5);
    expect(store.state.count).toBe(5);
  });

  it("merges successfully with the window state", () => {
    expect(store.state.isTesting).toBe(true);
  });

  it("generates mutations for namespaced modules", () => {
    expect(store.state.user.first_name).toBe("Reed");
    store.commit("user/X_SET_FIRST_NAME", "Tim");
    expect(store.state.user.first_name).toBe("Tim");
  });

  it("generates mutations for non-namespaced modules", () => {
    expect(store.state.other.first_name).toBe("Reed");
    store.commit("X_SET_FIRST_NAME", "Tim");
    expect(store.state.other.first_name).toBe("Tim");
  });

  it("generates mutations for deeply namespaced modules", () => {
    expect(store.state.deep.nested.modules.blah).toBe("yes");
    store.commit("deep/nested/modules/X_SET_BLAH", "woop");
    expect(store.state.deep.nested.modules.blah).toBe("woop");
  });
});
