import { createRoute } from "@/code-gen/createRoute";

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
    const routeGenerator = createRoute({});
    const route = routeGenerator(BasicRoute);

    expect(route).toBe(
      `    {
        name: 'Home',
        path: '/',
        beforeEnter: phaseBeforeEnter,
        component: ComponentName,
        meta: { middleware: ["web"] }
    }`
    );
  });
});
