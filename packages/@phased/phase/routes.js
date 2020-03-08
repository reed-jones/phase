import HomePage from "../../../resources/js/pages/PhaseController/HomePage.vue";
import AboutPage from "../../../resources/js/pages/PhaseController/AboutPage.vue";
import LoginPage from "../../../resources/js/pages/Auth/LoginPage.vue";
import Logout from "../../../resources/js/pages/Auth/Logout.vue";
import RegisterPage from "../../../resources/js/pages/Auth/RegisterPage.vue";

const redirects = { "401": "Auth.LoginPage" };

const phaseBeforeEnter = async (to, from, next) => {
  try {
    if (from.name) {
      // retrieve data from controller
      const { request } = await axios.get(to.fullPath);

      // check for server side redirects
      const finalUrl = new URL(request.responseURL).pathname;

      // follow redirects (if any)
      if (to.path !== finalUrl) {
        return next({
          path: finalUrl
        });
      }
    }

    // proceed to next page as usual
    return next();
  } catch (err) {
    const status = err && err.response && err.response.status;
    if (status && redirects[status]) {
      return next({
        name: redirects[status],
        query: { redirect: to.fullPath }
      });
    }

    if (process.env.NODE_ENV !== "production") {
      console.error(err);
    }
    return next();
  }
};

export default [
  {
    name: "PhaseController@HomePage",
    path: "/",
    beforeEnter: phaseBeforeEnter,
    component: HomePage,
    meta: { middleware: ["web"] }
  },
  {
    name: "PhaseController@AboutPage",
    path: "/about",
    beforeEnter: phaseBeforeEnter,
    component: AboutPage,
    meta: { middleware: ["web"] }
  },
  {
    name: "Auth.LoginPage",
    path: "/login",
    beforeEnter: phaseBeforeEnter,
    component: LoginPage,
    meta: { middleware: ["web"] }
  },
  {
    name: "Auth.Logout",
    path: "/logout",
    beforeEnter: phaseBeforeEnter,
    component: Logout,
    meta: { middleware: ["web"] }
  },
  {
    name: "Auth.RegisterPage",
    path: "/register",
    beforeEnter: phaseBeforeEnter,
    component: RegisterPage,
    meta: { middleware: ["web"] }
  }
];
