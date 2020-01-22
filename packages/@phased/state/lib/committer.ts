import { AxiosInstance, AxiosResponse } from "axios";
import { InitializedVuexStore, VuexModule, VuexStore } from "@phased/state"

export const VuexcellentAutoCommitter = (
  axios: AxiosInstance,
  _state: VuexModule,
  mutator: (key: string, ns?: string | null) => string
) => {
  return (store: InitializedVuexStore) => {
    // Setup axios interceptors
    axios.interceptors.response.use(
      autoMutateInterceptor(store, _state, mutator),
      (error: any) => Promise.reject(error)
    );
  };
};
/**
 * iterates through updated store values & attempts to auto-commit them
 *
 * @param {Object} data_1.store initialized vuex store
 * @param {Object} data_1._state raw vuex starting data
 * @param {Function} data_1.mutator function name generator
 *
 * @param {Object} data_2 server supplied state for mutations
 */
const commitData = (
  {
    store,
    _state,
    mutator
  }: {
    store: InitializedVuexStore;
    _state: VuexModule;
    mutator: (key: string, ns?: string | null) => string;
  },
  { state = {}, modules = {} }: VuexModule
) => {
  // Commit base state changes
  Object.entries(state).forEach(([key, value]) => {
    store.commit(mutator(key), value);
  });

  // Commit 1-level deep module state changes
  const commitModules = (m: VuexModule, prefix: string | null = null) => {
    Object.entries(m).forEach(([name, data]) => {
      Object.entries(data.state || {}).forEach(([key, value]) => {
        // optionally prefix name with namespace
        const fullName = [prefix, name].filter(a => a).join("/");

        if (_state.modules) {
          // iterate through modules names to find the
          // desired module.
          // e.g. fullName = app/users/active
          // modules: { app: { modules: { users: { modules: { active: { .... }}}}}}
          const mod = fullName
            .split("/")
            .reduce<VuexModule>(
              (acc: any, n: string): VuexModule => acc[n] || acc.modules[n],
              _state.modules
            );

          const namespace = mod.namespaced ? fullName : null;
          store.commit(mutator(key, namespace), value);
        } else {
          store.commit(mutator(key), value);
        }
      });

      if (Object.keys(data.modules || {}).length) {
        commitModules(data.modules, name);
      }
    });
  };

  commitModules(modules);
};

/**
 * Axios interceptor Generator to automatically call mutations
 * and commit changed data. Sets up interceptor
 *
 * @param {Object} store initialized vuex store
 * @param {Object} _state raw vuex starting data
 * @param {Function} mutator function name generator
 *
 * @return {Function}
 */
const autoMutateInterceptor = (
  store: InitializedVuexStore,
  _state: VuexStore,
  mutator: (key: string, ns?: string | null) => string
) =>
  /**
   * Axios Interceptor. if $vuex key is present in response data,
   * an attempt is made to parse the state and modules and commit
   * any changes
   *
   * @param {Object} response axios response
   *
   * @return {Object} response
   */
  (response: AxiosResponse) => {
    if (response.data.$vuex) {
      try {
        // grab state & modules, if existing
        commitData({ store, _state, mutator }, response.data.$vuex);
      } catch (err) {
        console.error(err);
        console.error(
          `[Vuexcellent] An error occurred during the auto commit process.\nYour vuex state may not be what you expected.`
        );
      }
    }

    return response;
  };
