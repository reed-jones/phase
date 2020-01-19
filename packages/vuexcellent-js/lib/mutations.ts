
import { VuexModule } from 'vuexcellent'

/**
 *
 * @param {Object} options Vuexcellent options
 * @param {String} mutationPrefix prefix mutations to avoid namespace collisions
 */
export const mutantGenerator = ({
  mutationPrefix = "X_SET"
}: {
  mutationPrefix?: string;
} = {}): {
  createMutant(mod: VuexModule): VuexModule;
  getMutation(key: string, ns?: string | null): string;
} => {
  /** Retrieves mutation name base on key */
  const getMutation = (key: string, ns: string | null = null): string => {
    if (!key) {
      throw "Could not generate proper mutation";
    }
    return ns
      ? `${ns}/${mutationPrefix}_${key.toUpperCase()}`
      : `${mutationPrefix}_${key.toUpperCase()}`;
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
