import Vue from 'vue';
import NiftyLayouts, { layoutRequire } from '@j0nz/nifty-layouts'
Vue.use(NiftyLayouts)

export const layout = new NiftyLayouts({
    currentLayout() {
        return this.$route.name.startsWith('Auth') ? 'AuthLayout' : 'MainLayout';
    },
    layouts: layoutRequire(require.context('./', true, /\.vue$/)),
})

// render function/entry point
export const renderLayout = h => h('NiftyLayout', {
    attrs: {
      id: 'app',
      layoutTransitionName: "layout-transition",
      layoutTransitionMode:"out-in",
      routeTransitionName:"route-transition",
      routeTransitionMode:"out-in"
    }
  })
