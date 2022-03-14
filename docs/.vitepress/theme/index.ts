import './styles/index.css'
import { h, App } from 'vue'
import { VPTheme } from '@overtrue/easywechat-theme'
import SponsorsAside from './components/SponsorsAside.vue'
import VersionTag from './components/VersionTag.vue'

export default Object.assign({}, VPTheme, {
  Layout: () => {
    // @ts-ignore
    return h(VPTheme.Layout, null, {
      // banner: () => h(Banner),
      'aside-mid': () => h(SponsorsAside)
    })
  },
  enhanceApp({ app }: { app: App }) {
    app.component('version-tag', VersionTag)
  }
})
