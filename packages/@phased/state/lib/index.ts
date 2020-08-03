import { VuexcellentOptions, VuexStore, VuexModule } from "@phased/state";
import { loggingMerge, objectMerge } from "./objectMerge";
import { mutantGenerator } from "./mutations";
import { VuexcellentAutoCommitter } from "./committer";
import { AxiosInstance } from "axios";
import { createLogger } from './logger'

declare var context: {
  __PHASE_STATE__?: VuexStore
  axios?: AxiosInstance
};

declare global {
    var __BROWSER__: boolean
    var __PHASE_STATE__: VuexStore
    var axios: AxiosInstance
}

globalThis.__BROWSER__ = typeof window !== 'undefined'
const isServer = typeof context !== 'undefined'
const globalBase = isServer ? context : globalThis

const defaultOptions = <VuexcellentOptions>{
  generateMutations: true,
  axios: globalBase.axios,
  mutationPrefix: `X_SET`,
  logLevel: 'emergency',
  logger: createLogger('emergency')
};

export const hydrate = (vuexState: VuexStore, options: VuexcellentOptions = defaultOptions) => {
  let __PHASE_STATE__ = globalBase.__PHASE_STATE__ ?? {}
  options = {
    ...defaultOptions,
    ...options
  }
  const logger = options.logger = createLogger(options.logLevel)
  logger.debug(`[Phase] Initiating Logger: ${options.logLevel}`)
  logger.debug(`[Phase] Initial Options:`, options)

  // PHP Converts the empty array to a... empty array, booo
  if (Array.isArray(__PHASE_STATE__) && !__PHASE_STATE__.length) {
    logger.debug('[Phase] Missing State. Forcing to Initial state to be empty object.')
    __PHASE_STATE__ = {}
  }

  // Currently not running actions/mutations on page load
  const { mutations, actions, ...phaseState } = __PHASE_STATE__

  // merge incoming (store) options with window.__PHASE_STATE__
  const mergedState = loggingMerge(logger, vuexState, <VuexStore>phaseState);

  // generate mutations
  const { createMutant, getMutation } = mutantGenerator(options);
  const newState = options.generateMutations
    ? createMutant(mergedState)
    : mergedState;

  logger.info(`[Phase] State Merged, Mutations Generated`, newState)

  if (options.axios && options.generateMutations && __BROWSER__) {
    // prepare plugin
    const VuexcellentPlugins = VuexcellentAutoCommitter(
      options,
      newState,
      getMutation
    );

    newState.plugins = [
        // inject plugin
        ...VuexcellentPlugins,
        ...(newState.plugins ?? [])
      ];

  } else if (options.generateMutations) {
    logger.error(
      "[Phase] It appears that auto-mutate could not be initialized.\nAn instance of axios could not be found. Make sure window.axios is available"
    );
  }

  return newState;
};
