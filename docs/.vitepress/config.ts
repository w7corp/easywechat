import fs from 'fs'
import path from 'path'
import { defineConfigWithTheme } from 'vitepress'
import type { Config as ThemeConfig } from '@overtrue/easywechat-theme'
import baseConfig from '@overtrue/easywechat-theme/config'
import { headerPlugin } from './headerMdPlugin'
import versions from './versions'

const latest = versions[0]

const nav = [
  {
    text: '首页',
    link: '/'
  },
  {
    text: '文档',
    activeMatch: `^/([0-9]\.x)/`,
    items: versions.map((version) => ({
      text: version,
      link: `/${version}/`
    }))
  },
  {
    text: '视频教程',
    link: 'https://wiki.w7.cc/college/collectiondetail/3'
  },
  {
    text: '讨论',
    link: 'https://github.com/w7corp/easywechat/discussions'
  },
  {
    text: '赞助',
    link: 'https://github.com/sponsors/overtrue'
  }
]

export const sidebar = versions.reduce(
  (sidebars, version) => ({
    ...sidebars,
    [`/${version}/`]: require(path.join(
      __dirname,
      `../src/${version}/sidebar`
    ))
  }),
  {}
)

export default defineConfigWithTheme<ThemeConfig>({
  extends: baseConfig,

  lang: 'zh-CN',
  title: 'EasyWeChat',
  description: '一个 PHP 微信开发 SDK',
  srcDir: 'src',
  srcExclude: [],
  scrollOffset: 'header',

  head: [
    ['link', { rel: 'icon', href: '/favicon.svg' }],
    ['meta', { name: 'twitter:site', content: '@easywechat' }],
    ['meta', { name: 'twitter:card', content: 'summary' }],
    [
      'meta',
      {
        name: 'twitter:image',
        content: 'https://easywechat/logo.png'
      }
    ]
  ],

  themeConfig: {
    nav,
    sidebar,

    algolia: {
      indexName: 'easywechat',
      appId: 'BH4D9OD16A',
      apiKey: '981093e0ff3b2e187aea7c340bc4d339'
      // searchParameters: {
      //   facetFilters: ['version:v3']
      // }
    },

    // carbonAds: {
    //   code: '',
    //   placement: ''
    // },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/w7corp/easywechat/' },
      { icon: 'twitter', link: 'https://twitter.com/overtrue' }
      // { icon: 'weibo', link: 'https://weibo.com/44294631' }
    ],

    editLink: {
      repo: 'w7corp/EasyWeChat#6.x',
      dir: 'docs/',
      text: '帮助我们改善此页面！'
    },

    footer: {
      license: {
        text: 'MIT License',
        link: 'https://opensource.org/licenses/MIT'
      },
      copyright: `Copyright © 2013-${new Date().getFullYear()} 微擎`
    }
  },

  markdown: {
    config(md) {
      md.use(headerPlugin)
    }
  },

  vite: {
    define: {
      __VUE_OPTIONS_API__: false
    },
    optimizeDeps: {
      include: ['gsap', 'dynamics.js'],
      exclude: []
    },
    // @ts-ignore
    ssr: {
      external: []
    },
    server: {
      host: true,
      fs: {
        // for when developing with locally linked theme
        allow: ['../..']
      }
    },
    build: {
      minify: 'terser',
      chunkSizeWarningLimit: Infinity
    },
    json: {
      stringify: true
    }
  },

  vue: {
    reactivityTransform: true
  }
})
