<script lang="ts" setup>
// import ClipboardJS from 'clipboard'
import { defineComponent, ref } from 'vue'
import versions from '../../versions'
import { withBase } from 'vitepress'

let latestVersion = versions[0]

// var clipboard = new ClipboardJS('[data-clipboard-text]')
// clipboard.on('success', (e) => {
//   console.log('复制成功')
//   // 释放内存
//   clipboard.destroy()
// })
// clipboard.on('error', (e) => {
//   // 不支持复制
//   alert('该浏览器不支持自动复制，请手动选择复制')
//   // 释放内存
//   clipboard.destroy()
// })
</script>

<template>
  <section class="text-center">
    <h1
      class="tagline my-12 text-center text-3xl md:text-5xl xl:text-6xl font-extrabold leading-tighter tracking-tighter aos-init aos-animate bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-green-400"
    >
      微信开发，可以更简单
    </h1>
    <p class="description">
      每一个细节，都经过精心打磨，只为了提供更好的开发体验。
    </p>
    <div
      class="my-20 md:my-20 space-y-2 md:space-y-0 xl:flex justify-center"
    >
      <button
        class="hidden xl:block mr-4 items-center space-around text-gray-700 bg-gray-100 border-0 py-2 px-6 focus:outline-none hover:bg-gray-200 rounded lg:text-lg"
        data-clipboard-text="composer require w7corp/easywechat"
        @click="copy"
      >
        <code
          class="text-gray-700 bg-transparent flex items-center cursor-pointer"
        >
          $ composer require w7corp/easywechat
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 ml-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
            />
          </svg>
        </code>
      </button>
      <a
        :href="withBase(`/${latestVersion}/`)"
        class="inline-flex items-center space-around text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg"
      >
        <span>立即开始</span>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 ml-2"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M17 8l4 4m0 0l-4 4m4-4H3"
          />
        </svg>
      </a>
    </div>

    <div
      class="my-20 md:flex items-center md:space-x-6 xl:space-x-12 xl:flex justify-center"
    >
      <a
        href="https://github.com/overtrue"
        target="_blank"
        class="py-6 flex items-center group"
      >
        <div
          class="h-12 w-12 rounded-full border-2 border-blue-200 group-hover:border-blue-400 p-0.5"
        >
          <img src="/overtrue.jpg" alt="overtrue" class="rounded-full" />
        </div>
        <div class="px-2 text-left text-gray-400">
          <div class="text-gray-700 font-semibold">overtrue</div>
          <small>核心开发者</small>
        </div>
      </a>
      <a
        href="https://www.w7.cc/"
        target="_blank"
        class="py-6 flex items-center group"
      >
        <div
          class="h-12 w-12 rounded-full border-2 border-blue-200 group-hover:border-blue-400 p-0.5"
        >
          <img src="/w7team.jpg" alt="overtrue" class="rounded-full" />
        </div>
        <div class="px-2 text-left text-gray-400">
          <div class="text-gray-700 font-semibold">微擎</div>
          <small>开源 Saas 平台提供商</small>
        </div>
      </a>
    </div>
  </section>
</template>

<style scoped>
section {
  padding: 42px 32px;
}

.tagline {
  font-size: 76px;
  line-height: 1.5;
  font-weight: 900;
  letter-spacing: -1.5px;
}

html:not(.dark) .accent,
.dark .tagline {
  background: -webkit-linear-gradient(315deg, #42d392 25%, #647eff);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.description {
  max-width: 960px;
  line-height: 1.5;
  color: var(--vt-c-text-2);
  transition: color 0.5s;
  font-size: 22px;
  margin: 24px auto 40px;
}

.actions a {
  font-size: 16px;
  display: inline-block;
  background-color: var(--vt-c-bg-mute);
  padding: 8px 18px;
  font-weight: 500;
  border-radius: 8px;
  transition: background-color 0.5s, color 0.5s;
}

.actions .get-started {
  margin-right: 18px;
}

.actions .icon {
  display: inline;
  position: relative;
  top: -1px;
  margin-left: 2px;
  fill: currentColor;
  transition: transform 0.2s;
}

.actions .get-started:hover {
  transition-duration: 0.2s;
}

.actions .get-started:hover .icon {
  transform: translateX(2px);
}

.actions .get-started,
.actions .setup {
  color: var(--vt-c-text-code);
}

.actions .get-started:hover,
.actions .setup:hover {
  background-color: var(--vt-c-gray-light-4);
  transition-duration: 0.2s;
}

.dark .actions .get-started:hover,
.dark .actions .setup:hover {
  background-color: var(--vt-c-gray-dark-3);
}

.dark #special-sponsor img {
  filter: grayscale(1) invert(1);
}

@media (max-width: 960px) {
  .tagline {
    font-size: 64px;
    letter-spacing: -0.5px;
  }
  .description {
    font-size: 18px;
    margin-bottom: 48px;
  }
}

@media (max-width: 768px) {
  .tagline {
    font-size: 48px;
    letter-spacing: -0.5px;
  }
}

@media (max-width: 576px) {
  #hero {
    padding: 64px 32px;
  }
  .description {
    font-size: 16px;
    margin: 18px 0 30px;
  }
  #special-sponsor img {
    display: block;
    margin: 2px auto 1px;
  }
  #highlights h3 {
    margin-bottom: 0.6em;
  }
  #highlights .vt-box {
    padding: 20px 36px;
  }
  .actions a {
    margin: 0.5em 0;
  }
}

@media (max-width: 370px) {
  .tagline {
    font-size: 36px;
  }
}
</style>
