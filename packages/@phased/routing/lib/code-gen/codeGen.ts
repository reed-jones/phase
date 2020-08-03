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
const __DEV__ = process.env.NODE_ENV !== 'production'

const phaseBeforeEnter = async (to, from, next) => {
  try {
    if (from.name) {

      // retrieve data from controller. 'phase: true' breaks browser caching so going back won't display json
      const { request } = await axios.get(to.fullPath, { params: { phase: true }})

      // check for server side redirects
      const finalUrl = new URL(request.responseURL).pathname

      // follow redirects (if any)
      if (to.path !== finalUrl) {
        if (__DEV__) { console.warn('[Phase] axios request followed redirect.') }
        return next({ path: finalUrl })
      }
    }

    // proceed to next page as usual
    return next()
  } catch (err) {

    const status = err && err.response && err.response.status

    if (status && redirects[status]) {

      if (__DEV__) { console.warn(\`[Phase] Phase redirect configured. Status code \${status} redirects to \${redirects[status]}\`) }

      return next({
        name: redirects[status],
        query: { redirect: to.fullPath }
      })
    }

    if (__DEV__) { console.error(err) }
    return next()
  }
}

export default [${routes}];
`,
    { parser: "babel" }
  );
};
