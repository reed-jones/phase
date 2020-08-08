import axios from "axios";
import Vuex from "vuex";
import Vue from "vue";
import { default as rawAxios } from "axios";
import { hydrate } from "@phased/state";

Vue.use(Vuex);

let store;
let axios;
beforeEach(function() {
  axios = rawAxios.create();
  global.window.__PHASE_STATE__ = {
    state: { appName: "Testing" },
    modules: {
      user: {
        state: { name: "Reed" }
      },
      app: {
        modules: {
          user: {
            modules: {
              test: {
                modules: {
                  deep: {
                    state: { isAdmin: true }
                  }
                }
              }
            }
          }
        }
      }
    }
  };

  store = new Vuex.Store(
    hydrate(
      {
        state: { number: 0 },
        modules: {
          anonymous: {
            state: {
              whoKnows: false
            }
          },
          user: { namespaced: true },
          app: {
            namespaced: true,
            modules: {
              user: {
                namespaced: true,
                modules: {
                  test: {
                    namespaced: true,
                    modules: {
                      deep: {
                        namespaced: true,
                        state: { }
                      }
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

describe("axios - testing the interceptor", () => {
  it("it updates a namespaced modules", () => {
    // starts out as expected
    expect(store.state.user.name).toEqual("Reed");

    // fulfilled, or rejected (for errors) here
    axios.interceptors.response.handlers[0].fulfilled({
      data: { $vuex: { modules: { user: { state: { name: "Erin" } } } } }
    });

    expect(store.state.user.name).toEqual("Erin");
  });

  it("it updates a non namespaced modules", () => {
    // starts out as expected
    expect(store.state.anonymous.whoKnows).toEqual(false);

    // fulfilled, or rejected (for errors) here
    axios.interceptors.response.handlers[0].fulfilled({
      data: { $vuex: { modules: { anonymous: { state: { whoKnows: true } } } } }
    });

    expect(store.state.anonymous.whoKnows).toEqual(true);
  });

  it("it updates the base state set via the server", () => {
    // starts out as expected
    expect(store.state.appName).toEqual("Testing");

    // fulfilled, or rejected (for errors) here
    axios.interceptors.response.handlers[0].fulfilled({
      data: { $vuex: { state: { appName: "New Name" } } }
    });

    expect(store.state.appName).toEqual("New Name");
  });

  it("it updates the base state set via the client", () => {
    // starts out as expected
    expect(store.state.number).toEqual(0);

    // fulfilled, or rejected (for errors) here
    axios.interceptors.response.handlers[0].fulfilled({
      data: { $vuex: { state: { number: 5 } } }
    });

    expect(store.state.number).toEqual(5);
  });

  it("it updates the deeply nested modules", () => {
    // starts out as expected
    expect(store.state.app.user.test.deep.isAdmin).toEqual(true);

    // fulfilled, or rejected (for errors) here
    axios.interceptors.response.handlers[0].fulfilled({
      data: {
        $vuex: {
          modules: {
            app: {
              modules: {
                user: {
                  modules: {
                    test: {
                      modules: {
                        deep: { state: { isAdmin: false } }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    });

    expect(store.state.app.user.test.deep.isAdmin).toEqual(false);
  });
});
