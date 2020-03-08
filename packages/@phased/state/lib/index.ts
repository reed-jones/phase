import { VuexcellentOptions, VuexStore } from "@phased/state";
import { objectMerge } from "./objectMerge";
import { mutantGenerator } from "./mutations";
import { VuexcellentAutoCommitter } from "./committer";
import { AxiosInstance } from "axios";
declare var context: {
  __PHASE_STATE__: object,
  axios: AxiosInstance
};

const defaultOptions = <VuexcellentOptions>{
  generateMutations: true,
  axios: null,
  mutationPrefix: `X_SET`
};

const isBrowser = typeof window !== 'undefined'
const isServer = typeof window === 'undefined' && typeof context !== 'undefined'

export const hydrate = (vuexState: VuexStore, options: VuexcellentOptions = defaultOptions) => {
  options = {
    ...defaultOptions,
    ...options
  }

  let INITIAL = isBrowser
    ? (window.__PHASE_STATE__ || {})
    : isServer
      ? (context.__PHASE_STATE__ || {})
        : {}

  // PHP Converts the empty array to a... empty array, booo
  if (Array.isArray(INITIAL) && !INITIAL.length) {
    INITIAL = {}
  }

  // Currently not running actions/mutations on page load
  let { mutations, actions, ...phaseState } = <VuexStore>INITIAL

  // merge incoming (store) options with window.__PHASE_STATE__
  const mergedState = <VuexStore>(
    objectMerge(vuexState, phaseState)
  );

  // generate mutations
  const { createMutant, getMutation } = mutantGenerator(options);
  const newState = options.generateMutations
    ? <VuexStore>createMutant(mergedState)
    : mergedState;

  // inject VuexcellentAutoCommitter into store.plugins
  const axios: AxiosInstance | null = options.axios
    || <AxiosInstance | null>(isBrowser ? (window.axios || null) : (context.axios || null));

  if (axios && options.generateMutations && isBrowser) {
    // prepare plugin
    const VuexcellentPlugin = VuexcellentAutoCommitter(
      axios,
      newState,
      getMutation
    );

    // inject plugin
    newState.plugins = newState.plugins
      ? [
        VuexcellentPlugin,
        ...newState.plugins
      ] : [
        VuexcellentPlugin
      ];

  } else if (options.generateMutations) {
    console.error(
      "[Phase] It appears that auto-mutate could not be initialized.\nAn instance of axios could not be found. Make sure window.axios is available"
    );
  }

  return newState;
};
