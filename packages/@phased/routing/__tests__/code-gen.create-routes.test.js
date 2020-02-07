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
    expect(createRoutes([BasicRoute], { redirects: [] })).toBe(
      prettier.format(
        `import ComponentName from "../../../resources/js/ComponentName";

const redirects = [];

const phaseBeforeEnter = async (to, from, next) => {
  try {
    if (from.name) {
      // retrieve data from controller
      const { request } = await axios.get(to.fullPath)

      // check for server side redirects
      const finalUrl = new URL(request.responseURL).pathname

      // follow redirects (if any)
      if (to.path !== finalUrl) {
        return next({
          path: finalUrl
        })
      }
    }

    // proceed to next page as usual
    return next();
  } catch (err) {
    const status = err?.response?.status
    if (status && redirects[status]) {
      return next({
        name: redirects[status],
        query: { redirect: to.fullPath }
      });
    }

    if (process.env.NODE_ENV !== 'production') {
      console.error(err)
    }
    return next()
  }
};

export default [{
  name: "Home",
  path: "/",
  beforeEnter: phaseBeforeEnter,
  component: ComponentName,
  meta: { middleware: ["web"] }
}];`,
        { parser: "babel" }
      )
    );
  });
});
