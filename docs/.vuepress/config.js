const versions = require("./versions");
const latest = versions[0];

module.exports = {
  debug: true,
  title: "EasyWeChat",
  description: "一个 PHP 微信开发 SDK",
  head: [
    [
      "link",
      {
        rel: "icon",
        href: "/favicon.png",
      },
    ],
  ],
  themeConfig: {
    defaultTheme: "light",
    logo: "/logo.svg",
    editLinks: true, //  "Edit this page" at the bottom of each page
    repo: "w7corp/EasyWeChat", //  Github repo
    docsDir: "docs/", //  Github repo docs folder
    latest,
    navbar: [
      {
        text: "首页",
        link: '/',
      },
      {
        text: "文档",
        children: versions.map((version) => ({
          text: version,
          link: `/${version}/`,
        })),
      },
      {
        text: "讨论",
        link: "https://github.com/w7corp/easywechat/discussions",
      },
    ],
    sidebar: versions.reduce(
      (sidebars, version) => ({
        ...sidebars,
        [`/${version}/`]: require(`../${version}/sidebar.js`),
      }),
      {}
    ),
  },
  plugins: [
    // [
    //   "@vuepress/search",
    //   {
    //     searchMaxSuggestions: 10,
    //     test: `/${latest}/`,
    //   },
    // ],
  ],
};
