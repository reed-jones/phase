import { createRoutes } from "@/code-gen/createRoutes";
import prettier from "prettier";

const BasicRoute = {
  name: "Home",
  uri: "/",
  prefix: "",
  middleware: "web",
  componentName: "ComponentName",
  file_path: "resources/js/ComponentName",
  children: undefined
};

describe("code-gen creates route definitions", () => {
  it("it creates the basic route definition", () => {
    const routesGen = createRoutes([BasicRoute], { redirects: [] });
    expect(routesGen).toEqual(expect.stringContaining('import ComponentName from "../../../resources/js/ComponentName";'))
    expect(routesGen).toEqual(expect.stringContaining('const redirects = [];'))
    expect(routesGen).toEqual(expect.stringContaining('beforeEnter: phaseBeforeEnter'))
    expect(routesGen).toEqual(expect.stringContaining('component: ComponentName'))
  });
});
