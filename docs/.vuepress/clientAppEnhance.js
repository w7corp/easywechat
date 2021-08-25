import { defineClientAppEnhance } from '@vuepress/client'

const versions = require("./versions");

export default defineClientAppEnhance(({ app, router, siteData }) => {

  router.beforeEach((to, from, next) => {
    const pathFragments = to.path.split("/");
    let version = pathFragments[1];
    const rest = pathFragments.splice(2).join("/") || 'index.html';

    // Used in the `Get Started` link of the index page
    if (version === "latest") {
      version = versions[0]
      return next({ path: `/${version}/${rest}` });
    }

    return next();
  });
})
