import { PHPConfiguration } from "@phased/routing";
import prettier from "prettier";

export const codeGen = (
  imports: string,
  routes: string,
  config: PHPConfiguration
) => {
  return prettier.format(
    `${imports}

const redirects = ${JSON.stringify(config.redirects)}

const phaseBeforeEnter = async (to, from, next) => {
  try {
    if (from) {
      // retrieve only data from controller
      const { request } = await axios.get(to.fullPath)
    }

    // check for server side redirects
    const finalUrl = new URL(request.responseURL).pathname

    // follow redirects (if any)
    if (to.path !== finalUrl) {
      return next({
        path: finalUrl,
        query: { redirect: to.fullPath }
      })
    }

    // proceed to next page as usual
    return next()

  } catch (err) {
    if (err && err.response && err.response.status && redirects[err.response.status]) {
      return next({
        name: redirects[err.response.status],
        query: { redirect: to.fullPath }
      })
    }
  }
}

export default [${routes}]
`,
    { parser: "babel" }
  );
};
