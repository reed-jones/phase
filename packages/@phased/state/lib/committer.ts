import { AxiosResponse } from "axios";
import { InitializedVuexStore, VuexModule, VuexStore, VuexcellentOptions, IPhaseLogger } from "@phased/state";

export const VuexcellentAutoCommitter = (
  options: VuexcellentOptions,
  _state: VuexModule,
  mutator: (key: string, ns?: string | null) => string
) => {
  const { axios, logger } = options
  if (!axios) {
    logger.warning("[Phase] Axios not found, skipping API auto mutation Vuex Plugin");
    return [];
  }

  return [(store: InitializedVuexStore) => {
    logger.debug("[Phase] Initializing axios auto mutation Interceptors");

    // Setup axios interceptors
    axios.interceptors.response.use(
      autoMutateInterceptor(store, _state, mutator, logger),
      (error: any) => Promise.reject(error)
    );
  }];
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
const autoCommitData = (
  { store, _state, mutator }: { store: InitializedVuexStore; _state: VuexModule; mutator: (key: string, ns?: string | null) => string; },
  { state = {}, modules = {} }: VuexModule,
  logger: IPhaseLogger
) => {
  // Commit base state changes
  Object.entries(state).forEach(([key, value]) => {
    const mutation = mutator(key)
    logger.debug(`[Phase] attempting mutation to base state ${mutation}`, { mutation, key, value })
    store.commit(mutation, value);
  });

  // Commit 1-level deep module state changes
  const commitModules = (m: VuexModule, prefix?: string | null) => {
    Object.entries(m).forEach(([name, data]) => {

      // optionally prefix name with namespace
      const fullName = [prefix, name].filter(a => a).join("/");
      logger.info(`[Phase] Generating namespace '${fullName}'`)

      Object.entries(data.state || {}).forEach(([key, value]) => {

        if (!_state.modules) {
          const mutation = mutator(key);
          logger.debug(`[Phase] attempting mutation to vuex module ${mutation}`, { mutation, key, value })
          store.commit(mutation, value);
        } else {

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
          const mutation = mutator(key, namespace);
          logger.debug(`[Phase] attempting mutation to namespaced vuex module ${mutation}`, { mutation, namespace, key, value })
          store.commit(mutation, value);
        }
      });

      if (Object.keys(data.modules || {}).length) {
        commitModules(data.modules, fullName);
      }
    });
  };
  logger.debug(`[Phase] Beginning auto commit routine for all available modules`)
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
  mutator: (key: string, ns?: string | null) => string,
  logger: IPhaseLogger
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
    if (!response.data.$vuex) {
      logger.debug("[Phase] no vuex data detected. Skipping auto mutations.")
      return response;
    }

    logger.debug("[Phase] vuex data detected, attempting to auto-commit")

    try {
      // grab state & modules, if existing & auto-commit
      autoCommitData({ store, _state, mutator }, response.data.$vuex, logger);

      // user specified mutations
      (response.data.$vuex.mutations || []).forEach(
        ([mutation, value]: [string, any?]) => store.commit(mutation, value)
      );

      // user specified actions
      (response.data.$vuex.actions || []).forEach(
        ([action, value]: [string, any?]) => store.dispatch(action, value)
      );
    } catch (err) {
      logger.error(err);
      logger.warning(
        `[@phased/state] An error occurred during the auto commit process.\nYour vuex state may not be what you expected.`
      );
    }

    return response;
  };
