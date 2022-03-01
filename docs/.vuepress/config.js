const versions = require('./versions')
const path = require('path')
const latest = versions[0]

module.exports = {
  debug: true,
  title: 'EasyWeChat',
  description: '一个 PHP 微信开发 SDK',
  head: [
    [
      'link',
      {
        rel: 'icon',
        href: '/favicon.png',
      },
    ],
  ],
  themeConfig: {
    defaultTheme: 'light',
    logo: '/logo.svg',
    editLinks: true, //  "Edit this page" at the bottom of each page
    editLinkText: '帮助我们改善此页面！',
    repo: 'w7corp/EasyWeChat', //  Github repo
    docsDir: 'docs/', //  Github repo docs folder
    docsBranch: 'master', //  Github repo docs branch
    latest,
    navbar: [
      {
        text: '首页',
        link: '/',
      },
      {
        text: '文档',
        children: versions.map((version) => ({
          text: version,
          link: `/${version}/`,
        })),
      },
      {
        text: '视频教程',
        link: 'https://wiki.w7.cc/college/collectiondetail/3',
      },
      {
        text: '讨论',
        link: 'https://github.com/w7corp/easywechat/discussions',
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
    [
      '@vuepress/register-components',
      {
        componentsDir: path.resolve(__dirname, './components'),
      },
    ],
    ['@vuepress/google-analytics', { id: 'UA-64156348-1' }],
    [
      '@vuepress/docsearch',
      {
        apiKey: '981093e0ff3b2e187aea7c340bc4d339',
        indexName: 'easywechat',
      },
    ],
  ],
}
