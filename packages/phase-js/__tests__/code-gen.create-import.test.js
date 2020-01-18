import { createImport } from "@/code-gen/createImport";

const TEST_PREFIX = "users";

const BasicRoute = {
  name: "name",
  prefix: "",
  componentName: "ComponentName",
  file_path: "resources/js/ComponentName"
};

const BasicChildren = [
  {
    name: "child",
    prefix: "",
    componentName: "ChildComponentName",
    file_path: "resources/js/ChildComponentName"
  }
];

const PrefixedChildren = BasicChildren.map(c => ({
  ...c,
  prefix: TEST_PREFIX
}));

const PrefixedRoute = {
  ...BasicRoute,
  prefix: TEST_PREFIX
};

const BasicRouteWithChildren = {
  ...BasicRoute,
  children: BasicChildren
};
const PrefixedRouteWithPrefixedChildren = {
  ...PrefixedRoute,
  children: PrefixedChildren
};

describe("code-gen creates imports", () => {
  it("it creates static imports by default", () => {
    const importGenerator = createImport();

    expect(importGenerator(BasicRoute)).toBe(
      `import ComponentName from '../../../resources/js/ComponentName'`
    );
  });

  it("it creates dynamic imports", () => {
    const importGenerator = createImport(true);

    expect(importGenerator(BasicRoute)).toBe(
      `const ComponentName = () => import('../../../resources/js/ComponentName')`
    );
  });

  it("it creates static imports, ignoring prefixes", () => {
    const importGenerator = createImport();

    expect(importGenerator(PrefixedRoute)).toBe(
      `import ComponentName from '../../../resources/js/ComponentName'`
    );
  });

  it("it creates dynamic imports, grouping prefixes", () => {
    const importGenerator = createImport(true);

    expect(importGenerator(PrefixedRoute)).toBe(
      `const ComponentName = () => import(/* webpackChunkName: "${TEST_PREFIX}" */ '../../../resources/js/ComponentName')`
    );
  });

  it("it creates static imports by default with children", () => {
    const importGenerator = createImport();

    expect(importGenerator(BasicRouteWithChildren)).toBe(
      `import ComponentName from '../../../resources/js/ComponentName'` +
        "\n" +
        `import ChildComponentName from '../../../resources/js/ChildComponentName'`
    );
  });

  it("it creates dynamic imports with children", () => {
    const importGenerator = createImport(true);

    expect(importGenerator(BasicRouteWithChildren)).toBe(
      `const ComponentName = () => import('../../../resources/js/ComponentName')` +
        "\n" +
        `const ChildComponentName = () => import('../../../resources/js/ChildComponentName')`
    );
  });

  it("it creates dynamic imports, grouping prefixes, with children", () => {
    const importGenerator = createImport(true);

    expect(importGenerator(PrefixedRouteWithPrefixedChildren)).toBe(
      `const ComponentName = () => import(/* webpackChunkName: "users" */ '../../../resources/js/ComponentName')` +
        "\n" +
        `const ChildComponentName = () => import(/* webpackChunkName: "users" */ '../../../resources/js/ChildComponentName')`
    );
  });
});
