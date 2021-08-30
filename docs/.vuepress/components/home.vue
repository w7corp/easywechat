<template>
  <div>
    <div class="bg-gray-800 relative overflow-hidden h-screen">
      <header class="text-gray-100 body-font relative z-10">
        <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
          <a href="/" class="flex title-font font-medium items-center text-gray-200 mb-4 md:mb-0">
            <img src="/logo-white.svg" class="h-8" alt="EasyWeChat" />
            <span class="ml-3 text-xl hidden">EasyWeChat</span>
          </a>
          <nav class="md:ml-auto flex flex-wrap items-center text-base justify-center">
            <a class="mr-5 text-gray-400 hover:text-gray-300" :href="$withBase(`/${latestVersion}/`)"> 文档 </a>
            <a class="mr-5 text-gray-400 hover:text-gray-300" target="_blank" href="https://github.com/w7corp/easywechat/discussions"> 论坛 </a>
            <a class="mr-5 text-gray-400 hover:text-gray-300" target="_blank" href="https://github.com/w7corp/easywechat/"> 源码 </a>
          </nav>
        </div>
      </header>

      <section class="text-gray-600 body-font">
        <div class="z-10 relative container mx-auto flex px-5 py-12 md:py-24 md:flex-row flex-col items-center">
          <div class="lg:flex-1 md:w-2/3 lg:pr-12 md:pr-16 flex flex-col md:items-start md:text-left mb-16 md:mb-0 items-center text-center">
            <h1
              class="text-3xl md:text-5xl xl:text-6xl font-extrabold leading-tighter tracking-tighter mb-4 aos-init aos-animate bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-green-400"
            >
              微信开发，也可以更简单
            </h1>
            <p class="mb-2 md:mb-8 leading-relaxed text-gray-500">每一个细节，都经过精心打磨，只为了提供更好的开发体验</p>
            <div class="mt-2 md:mt-8 space-y-2 md:space-y-0 xl:flex justify-center">
              <button
                class="hidden xl:block mr-4 inline-flex items-center space-around text-gray-700 bg-gray-100 border-0 py-2 px-6 focus:outline-none hover:bg-gray-200 rounded text-lg"
                data-clipboard-text="composer require w7corp/easywechat"
                @click="copy"
              >
                <code class="text-gray-700 bg-transparent flex items-center cursor-pointer">
                  $ composer require w7corp/easywechat
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                :href="$withBase(`/${latestVersion}/`)"
                class="inline-flex items-center space-around text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg"
              >
                <span>立即开始</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </a>
            </div>
            <div class="hidden mt-10 md:flex items-center space-x-6 xl:space-x-8">
              <a href="https://github.com/overtrue" target="_blank" class="py-6 flex items-center">
                <img :src="$withBase('/overtrue.png')" alt="overtrue" class="h-12 w-12 rounded-full border-2 border-blue-200 p-0.5" />
                <div class="px-2 text-left text-gray-400">
                  <div class="text-white font-semibold">overtrue</div>
                  <small>核心开发者</small>
                </div>
              </a>
              <a href="https://www.w7.cc/" target="_blank" class="py-6 flex items-center">
                <img :src="$withBase('/w7team.png')" alt="overtrue" class="h-12 w-12 rounded-full border-2 border-blue-200 p-0.5" />
                <div class="px-2 text-left text-gray-400">
                  <div class="text-white font-semibold">微擎</div>
                  <small>开源 Saas 平台提供商</small>
                </div>
              </a>
            </div>
            <div class="mt-10">
              <a href="https://www.aliyundrive.com/s/6CwgtkiBqFV" target="_blank" class="flex items-center rounded bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2">
                🎁 免费下载 5.x 全部视频教程
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </a>
            </div>
          </div>
          <div class="w-1/3 lg:max-w-lg xl:w-1/2">
            <img :src="$withBase('/3d-blocks.svg')" alt="" class="py-4" />
          </div>
        </div>
      </section>
      <div class="stars-bg absolute inset-0 z-0">
        <div class="stars"></div>
        <div class="stars2"></div>
        <div class="stars3"></div>
      </div>

      <HomeFooter class="z-10"></HomeFooter>
    </div>
  </div>
</template>

<script>
import Clipboard from "clipboard";
import { defineComponent, ref } from "vue";
const versions = require("../versions");
const latestVersion = versions[0];

export default defineComponent({
  data() {
    return {
      latestVersion: latestVersion,
    };
  },
  setup() {
    var clipboard = new Clipboard("[data-clipboard-text]");
    clipboard.on("success", (e) => {
      console.log("复制成功");
      // 释放内存
      clipboard.destroy();
    });
    clipboard.on("error", (e) => {
      // 不支持复制
      alert("该浏览器不支持自动复制，请手动选择复制");
      // 释放内存
      clipboard.destroy();
    });
  },
});
</script>
