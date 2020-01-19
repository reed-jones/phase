import Vuex from "vuex";
import Vue from "vue";
import axios from "axios";
import { hydrate } from "..";

jest.mock("axios");

Vue.use(Vuex);

let store;
beforeEach(function() {
  global.window.__VUEXCELLENT_STATE__ = {
    state: { isTesting: true },
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
        state: { count: 1 },
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
      { axios }
    )
  );
});

describe("testing the default store actions vuexcellent", () => {
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
