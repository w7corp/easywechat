---
layout: page
title: EasyWeChat - 一个 PHP 微信开发 SDK
---

<script lang="ts" setup>
import versions from '@theme/../versions'
import {withBase} from 'vitepress'

let latestVersion = versions[0]

</script>

<section class="text-center flex flex-col flex-1 px-4 md:px-12">
  <div class="flex-1 flex flex-col items-center justify-center space-y-8">
    <div class="flex justify-center">
      <h1
        class="tagline md:py-28 text-center text-3xl md:text-7xl xl:text-8xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-400"
      >
        微信开发，可以更简单
      </h1>
    </div>
    <p class="py-4 md:py-6 md:text-2xl dark:text-gray-500">
      每一个细节，都经过精心打磨，只为了提供更好的开发体验。
    </p>
    <div
      class="space-y-2 md:space-y-0 xl:flex justify-center"
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
  </div>
  <div
    class="py-4 md:py-12 md:flex items-center md:space-x-6 xl:space-x-12 xl:flex justify-center"
  >
    <a
      href="https://github.com/overtrue"
      target="_blank"
      class="flex items-center group hover:bg-gray-200 dark:hover:bg-indigo-500 xl:w-64 px-6 py-2 rounded-lg"
    >
      <div
        class="h-12 w-12 rounded-full border-2 border-blue-200 group-hover:border-blue-400 dark:group-hover:border-gray-300 p-0.5"
      >
        <img src="/overtrue.jpg" alt="overtrue" class="rounded-full"/>
      </div>
      <div class="px-2 text-left text-gray-400 dark:group-hover:text-gray-300">
        <div class="text-gray-700 dark:text-gray-300 dark:group-hover:text-white font-semibold">overtrue</div>
        <small>核心开发者</small>
      </div>
    </a>
    <a
      href="https://www.w7.cc/"
      target="_blank"
      class="flex items-center group hover:bg-gray-200 dark:hover:bg-indigo-500 xl:w-64 px-6 py-2 rounded-lg"
    >
      <div
        class="h-12 w-12 rounded-full border-2 border-blue-200 group-hover:border-blue-400 dark:group-hover:border-gray-300 p-0.5"
      >
        <img src="/w7team.jpg" alt="overtrue" class="rounded-full"/>
      </div>
      <div class="px-2 text-left text-gray-400 dark:group-hover:text-gray-300">
        <div class="text-gray-700  dark:text-gray-300 dark:group-hover:text-white font-semibold">微擎</div>
        <small>开源 Saas 平台提供商</small>
      </div>
    </a>
  </div>
</section>
