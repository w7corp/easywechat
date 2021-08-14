import { defineClientAppEnhance } from '@vuepress/client'

import home from './components/home'

export default defineClientAppEnhance(({ app, router, siteData }) => {
  app.component('home', home)

  router.beforeEach((to, from, next) => {
    const pathFragments = to.path.split("/");
    const version = pathFragments[1];
    const rest = pathFragments.splice(2).join("/");

    // Used in the `Get Started` link of the index page
    if (version === "latest") {
      return next({ path: `/${siteData.themeConfig.latest}/${rest}` });
    }

    return next();
  });
})
