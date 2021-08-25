<template>
  <div>
    <div class="bg-gray-800 relative overflow-hidden">
      <header class="text-gray-100 body-font relative z-10">
        <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
          <a href="/" class="flex title-font font-medium items-center text-gray-200 mb-4 md:mb-0">
            <img src="/logo-icon.svg" class="h-8 w-8" alt="" />
            <span class="ml-3 text-xl">EasyWeChat</span>
          </a>
          <nav class="md:ml-auto flex flex-wrap items-center text-base justify-center">
            <a class="mr-5 text-gray-400 hover:text-gray-300" href="/latest"> 文档 </a>
            <a class="mr-5 text-gray-400 hover:text-gray-300" target="_blank" href="https://github.com/w7corp/easywechat/discussions"> 论坛 </a>
            <a class="mr-5 text-gray-400 hover:text-gray-300" target="_blank" href="https://github.com/w7corp/easywechat/"> 源码 </a>
          </nav>
        </div>
      </header>

      <section class="text-gray-600 body-font h-screen relative overflow-hidden">
        <div class="z-10 relative container mx-auto flex px-5 py-24 md:flex-row flex-col items-center">
          <div class="lg:flex-grow md:w-1/2 lg:pr-24 md:pr-16 flex flex-col md:items-start md:text-left mb-16 md:mb-0 items-center text-center">
            <h1
              class="sm:text-4xl text-5xl md:text-6xl font-extrabold leading-tighter tracking-tighter mb-4 aos-init aos-animate bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-green-400"
            >
              微信开发，从未如此简单
            </h1>
            <p class="mb-8 leading-relaxed text-gray-500">每一个功能设计，都经过精心打磨，只为了提供更好的开发体验</p>
            <div class="flex justify-center">
              <button
                class="mr-4 inline-flex items-center space-around text-gray-700 bg-gray-100 border-0 py-2 px-6 focus:outline-none hover:bg-gray-200 rounded text-lg"
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
              <a href="/latest/" class="inline-flex items-center space-around text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                <span>立即开始</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </a>
            </div>
          </div>
          <div class="lg:max-w-lg lg:w-full md:w-1/2 w-5/6">
            <img src="/3d-blocks.svg" alt="" class="py-4" />
          </div>
        </div>
      </section>
      <div class="stars-bg absolute inset-0 z-0">
        <div class="stars"></div>
        <div class="stars2"></div>
        <div class="stars3"></div>
      </div>

      <footer class="relative z-10"></footer>
    </div>
  </div>
</template>

<script>
import Clipboard from "clipboard";
import { defineComponent, ref } from "vue";
export default defineComponent({
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
