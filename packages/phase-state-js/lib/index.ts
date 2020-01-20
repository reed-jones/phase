import { VuexcellentOptions, VuexStore } from "vuexcellent";
import { objectMerge } from "./objectMerge";
import { mutantGenerator } from "./mutations";
import { VuexcellentAutoCommitter } from "./committer";
import { AxiosInstance } from "axios";

declare global {
  interface Window {
    __PHASE_STATE__: object;
    axios: object;
  }
}

const defaultOptions = <VuexcellentOptions>{
  generateMutations: true,
  axios: null,
  mutationPrefix: `X_SET`
};

export const hydrate = (vuexState: VuexStore, options: VuexcellentOptions = defaultOptions) => {
  options = {
    ...defaultOptions,
    ...options
  }
  // merge incoming (store) options with window.__PHASE_STATE__
  const mergedState = <VuexStore>(
    objectMerge(vuexState, window.__PHASE_STATE__ || {})
  );

  // generate mutations
  const { createMutant, getMutation } = mutantGenerator(options);
  const newState = options.generateMutations
    ? <VuexStore>createMutant(mergedState)
    : mergedState;

  // inject VuexcellentAutoCommitter into store.plugins
  const axios: AxiosInstance | null = options.axios || <AxiosInstance | null>window.axios;
  if (axios && options.generateMutations) {
    // prepare plugin
    const VuexcellentPlugin = VuexcellentAutoCommitter(
      axios,
      newState,
      getMutation
    );

    // inject plugin
    newState.plugins = newState.plugins
      ? [VuexcellentPlugin, ...newState.plugins]
      : [VuexcellentPlugin];

  } else if (options.generateMutations) {
    console.error(
      "[Vuexcellent] It appears that auto-mutate could not be initialized.\nAn instance of axios could not be found."
    );
  }

  return newState;
};
