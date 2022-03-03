/// <reference types="vitepress/client" />
/// <reference types="vue/macros-global" />

declare module '@overtrue/easywechat-theme/config' {
  import { UserConfig } from 'vitepress'
  const config: () => Promise<UserConfig>
  export default config
}

declare module '@overtrue/easywechat-theme/highlight' {
  const createHighlighter: () => Promise<(input: string) => string>
  export default createHighlighter
}
