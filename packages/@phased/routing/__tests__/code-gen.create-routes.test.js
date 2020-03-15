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

// Keep track of the previous URL, to avoid double fetching data
let lastPath;

const phaseBeforeEnter = async (to, from, next) => {
  try {
    if (from.name && !from.query.phase && lastPath !== to.fullPath) {
      // update last url pointer
      lastPath = to.fullPath;

      // retrieve data from controller. 'phase: true' breaks browser caching so going back won't display json
      const { request } = await axios.get(to.fullPath, { params: { phase: true }})

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
    const status = err && err.response && err.response.status
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

const routes = [{
  name: "Home",
  path: "/",
  beforeEnter: phaseBeforeEnter,
  component: ComponentName,
  meta: { middleware: ["web"] }
}];

export const followAllRedirects = theRouter => {
  if (typeof window === 'undefined') return;

  axios.interceptors.request.use(
      config => {
        config.redirect = config.redirect !== false
        if (config.redirect) {
          if (!config.params) {
            config.params = {}
          }
          config.params['phase'] = true
        }
        return config
      },
      error => Promise.reject(error)
  );

  allowRedirects(theRouter)
}

export const allowRedirects = theRouter => {
  if (typeof window === 'undefined') return;

  axios.interceptors.response.use(
      response => {
          const startUrl = window.location.pathname;
          const finalUrl = new URL(response.request.responseURL).pathname

          if (response.config.redirect && startUrl !== finalUrl && routes.some(r => r.path === finalUrl)) {

              // update last url pointer
              lastPath = finalUrl

              theRouter.push({
                  path: finalUrl,
              })
          }

          return response;
      },
      error => Promise.reject(error)
  );
}

export default routes;`,
        { parser: "babel" }
      )
    );
  });
});
