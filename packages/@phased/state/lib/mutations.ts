
import { VuexModule, VuexStore, VuexcellentOptions, IPhaseLogger } from '@phased/state'

/**
 *
 * @param {VuexcellentOptions} options Vuexcellent options
 */
export const mutantGenerator = ({ mutationPrefix = "X_SET", logger }: { mutationPrefix?: string; logger?: IPhaseLogger; } = {}): {
  createMutant(mod: VuexStore): VuexStore;
  getMutation(key: string, ns?: string | null): string;
} => {
  /** Retrieves mutation name base on key */
  const getMutation = (key: string, ns: string | null = null): string => {
    if (!key) {
      logger?.critical('[Phase] Failed to generate mutation', { key, namespace: ns })
      throw "Could not generate proper mutation";
    }

    const namespace = ns
      ? `${ns}/${mutationPrefix}_${key.toUpperCase()}`
      : `${mutationPrefix}_${key.toUpperCase()}`;

    logger?.debug(`[Phase] Namespace generated`, { namespace });
    return namespace
  };

  /** Default mutation. Nukes state and replaces */
  const _newMutation = (key: string): Function => {
    return new Function("state", "val", `state.${key} = val`);
  };

  /** Creates mutations based in state keys */
  const _generateMutations = ({
    state = {},
    mutations = {}
  }: VuexModule): object => {
    return Object.keys(state).reduce(
      (acc: object, key: string): object => ({
        ...acc,
        [getMutation(key)]: _newMutation(key)
      }),
      mutations
    );
  };

  /**
   * Recursive (through modules) mutation creation
   *
   * @param {Object} mod vuex module
   *
   * @return {Object} updated vuex module
   */
  const createMutant = (mod: VuexModule): VuexModule => {
    mod.mutations = _generateMutations(mod);

    Object.entries(mod.modules || {}).forEach(([key]): void => {
      if (mod.modules && mod.modules[key]) {
        const { [key]: data } = mod.modules;
        mod.modules[key].mutations = _generateMutations(data);
        mod.modules[key] = createMutant(data);
      }
    });

    return mod;
  };

  return { createMutant, getMutation };
};
