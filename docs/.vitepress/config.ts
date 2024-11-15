import path from 'path'
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
    text: '视频',
    link: 'https://wiki.w7.cc/college/collectiondetail/3'
  },
  {
    text: '讨论',
    link: 'https://github.com/w7corp/easywechat/discussions'
  },
  {
    text: '赞助',
    link: 'https://github.com/sponsors/overtrue'
  },
  {
    text: '蓝P站',
    link: 'https://wechatpay.im/'
  },
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

export default {
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
        content: 'https://easywechat.com/logo.svg'
      }
    ],
    // google analytics, without tracing dev
    ...(process?.argv?.[2] === 'dev' ? [] : [
      [
        'script',
        { async: '', src: 'https://www.googletagmanager.com/gtag/js?id=G-ZVHYZEP1SR' }
      ],
      [
        'script',
        {},
        `window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-ZVHYZEP1SR');`
      ],
    ]),
    // end google analytics
  ],

  themeConfig: {
    nav,
    sidebar,

    logo: '/logo-icon.svg',

    algolia: {
      placeholder: '搜索文档',
      translations: {
        button: {
          buttonText: '搜索文档',
          buttonAriaLabel: '搜索文档'
        },
        modal: {
          searchBox: {
            resetButtonTitle: '清除查询条件',
            resetButtonAriaLabel: '清除查询条件',
            cancelButtonText: '取消',
            cancelButtonAriaLabel: '取消'
          },
          startScreen: {
            recentSearchesTitle: '搜索历史',
            noRecentSearchesText: '没有搜索历史',
            saveRecentSearchButtonTitle: '保存至搜索历史',
            removeRecentSearchButtonTitle: '从搜索历史中移除',
            favoriteSearchesTitle: '收藏',
            removeFavoriteSearchButtonTitle: '从收藏中移除'
          },
          errorScreen: {
            titleText: '无法获取结果',
            helpText: '你可能需要检查你的网络连接'
          },
          footer: {
            selectText: '选择',
            navigateText: '切换',
            closeText: '关闭',
            searchByText: '搜索提供者'
          },
          noResultsScreen: {
            noResultsText: '无法找到相关结果',
            suggestedQueryText: '你可以尝试查询',
            reportMissingResultsText: '你认为该查询应该有结果？',
            reportMissingResultsLinkText: '点击反馈'
          },
        },
      },
      indexName: 'easywechat',
      appId: 'X3KJL5SQXD',
      apiKey: '5c5ba71b35c48411f245bef4c695fc36'
      // searchParameters: {
      //   facetFilters: ['version:v3']
      // }
    },

    returnToTopLabel: '回到顶部',
    sidebarMenuLabel: '菜单',
    darkModeSwitchLabel: '主题模式',
    lightModeSwitchTitle: '浅色模式',
    darkModeSwitchTitle: '深色模式',

    outline: {
      level: [2, 3],
      label: '页面导航',
    },

    docFooter: {
      prev: '上一页',
      next: '下一页'
    },

    notFound: {
      title: '未找到',
      quote: '您所访问的页面未找到，或者已失效',
      linkLabel: '返回首页',
      linkText: '返回首页',
    },

    // carbonAds: {
    //   code: '',
    //   placement: ''
    // },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/w7corp/easywechat' },
      { icon: 'twitter', link: 'https://twitter.com/overtrue666' },
      {
        icon: {
          svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0,0,256,256">
                <g transform="translate(-7.68,-7.68) scale(1.06,1.06)"><g fill="currentColor" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(4,4)"><path d="M46.846,7.021c-1.357,-0.069 -2.725,0.03 -4.063,0.315c-1.24,0.266 -2.061,1.506 -1.797,2.756c0.264,1.25 1.494,2.077 2.735,1.811c3.819,-0.817 7.979,0.345 10.782,3.465c2.793,3.13 3.545,7.441 2.344,11.182c-0.391,1.221 0.273,2.52 1.484,2.914c1.201,0.394 2.5,-0.285 2.891,-1.496c1.68,-5.266 0.664,-11.27 -3.281,-15.67c-2.96,-3.298 -7.013,-5.08 -11.095,-5.277zM26.142,13.951c-4.19,0.453 -10.04,3.74 -15.235,8.977c-5.655,5.708 -8.907,11.782 -8.907,17.008c0,10.001 12.706,16.064 25.158,16.064c16.319,0 27.189,-9.577 27.189,-17.166c0,-4.587 -3.819,-7.195 -7.266,-8.268c-0.85,-0.256 -1.455,-0.374 -1.016,-1.496c0.957,-2.431 1.113,-4.567 0.078,-6.063c-1.943,-2.805 -7.334,-2.658 -13.438,-0.079c0,0 -1.895,0.906 -1.406,-0.63c0.938,-3.041 0.762,-5.611 -0.703,-7.087c-1.036,-1.044 -2.55,-1.467 -4.454,-1.26zM46.455,15.211c-0.664,-0.03 -1.299,0.02 -1.953,0.157c-1.074,0.226 -1.797,1.358 -1.563,2.441c0.234,1.073 1.279,1.732 2.344,1.496c1.279,-0.276 2.735,0.138 3.672,1.181c0.938,1.043 1.182,2.451 0.781,3.701c-0.332,1.043 0.215,2.175 1.25,2.52c1.035,0.335 2.168,-0.217 2.5,-1.26c0.82,-2.559 0.283,-5.492 -1.641,-7.638c-1.434,-1.604 -3.397,-2.51 -5.39,-2.598zM28.486,28.518c8.301,0.295 14.981,4.488 15.548,10.237c0.645,6.575 -6.866,12.717 -16.798,13.701c-9.932,0.984 -18.575,-3.582 -19.22,-10.157c-0.645,-6.575 6.944,-12.717 16.876,-13.701c1.24,-0.129 2.412,-0.119 3.594,-0.08zM24.579,33.4c-3.594,0.345 -6.973,2.441 -8.516,5.591c-2.09,4.282 -0.088,9.075 4.688,10.63c4.951,1.604 10.782,-0.886 12.813,-5.512c2.002,-4.518 -0.498,-9.124 -5.391,-10.394c-1.181,-0.305 -2.392,-0.433 -3.594,-0.315zM26.845,38.913c0.156,0 0.244,0.02 0.391,0.079c0.606,0.226 0.879,0.896 0.547,1.496c-0.352,0.6 -1.113,0.876 -1.719,0.63c-0.596,-0.246 -0.801,-0.906 -0.469,-1.496c0.264,-0.444 0.781,-0.7 1.25,-0.709zM22.235,40.33c0.42,0.01 0.869,0.069 1.25,0.236c1.553,0.669 2.041,2.461 1.094,4.016c-0.957,1.545 -2.979,2.284 -4.531,1.575c-1.524,-0.699 -1.973,-2.51 -1.016,-4.016c0.713,-1.122 1.953,-1.831 3.203,-1.811z"></path></g></g></g>
                </svg>`
        },
        ariaLabel: 'Weibo',
        link: 'https://weibo.com/44294631'
      }
    ],

    editLink: {
      pattern:
        'https://github.com/w7corp/EasyWeChat/edit/6.x/docs/src/:path',
      text: '帮助我们改善此页面！'
    },

    license: {
      text: 'MIT License',
      link: 'https://opensource.org/licenses/MIT'
    },
    copyright: `Copyright © 2013-${new Date().getFullYear()} 微擎 <a class="ml-4" href="https://beian.miit.gov.cn/" target="_blank">皖ICP备19002904号-6</a>`
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
}
