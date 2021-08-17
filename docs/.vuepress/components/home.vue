<template>
  <div>
    <div class="bg-gray-800 relative overflow-hidden">
      <header class="text-gray-100 body-font z-10">
        <div
          class="
            container
            mx-auto
            flex flex-wrap
            p-5
            flex-col
            md:flex-row
            items-center
          "
        >
          <a
            href="/"
            class="
              flex
              title-font
              font-medium
              items-center
              text-gray-200
              mb-4
              md:mb-0
            "
          >
            <img src="/logo-icon.svg" class="h-8 w-8" alt="" />
            <span class="ml-3 text-xl">EasyWeChat</span>
          </a>
          <nav
            class="
              md:ml-auto
              flex flex-wrap
              items-center
              text-base
              justify-center
            "
          >
            <a class="mr-5 hover:text-gray-300" href="/latest">文档</a>
            <a
              class="mr-5 hover:text-gray-300"
              target="_blank"
              href="https://github.com/w7corp/easywechat/discussions"
              >论坛</a
            >
            <a
              class="mr-5 hover:text-gray-300"
              target="_blank"
              href="https://github.com/w7corp/easywechat/"
              >源码</a
            >
            <a class="mr-5 hover:text-gray-300">关于</a>
          </nav>
        </div>
      </header>

      <section
        class="text-gray-600 body-font h-screen relative overflow-hidden"
      >
        <div
          class="
            z-10
            relative
            container
            mx-auto
            flex
            px-5
            py-24
            md:flex-row
            flex-col
            items-center
          "
        >
          <div
            class="
              lg:flex-grow
              md:w-1/2
              lg:pr-24
              md:pr-16
              flex flex-col
              md:items-start
              md:text-left
              mb-16
              md:mb-0
              items-center
              text-center
            "
          >
            <h1
              class="
                sm:text-4xl
                text-5xl
                md:text-6xl
                font-extrabold
                leading-tighter
                tracking-tighter
                mb-4
                aos-init aos-animate
                bg-clip-text
                text-transparent
                bg-gradient-to-r
                from-blue-600
                to-green-400
              "
            >
              微信开发，从未如此简单
            </h1>
            <p class="mb-8 leading-relaxed text-gray-500">
              每一个功能设计，都经过精心打磨，只为了提供更好的开发体验
            </p>
            <div class="flex justify-center">
              <button
                class="
                  mr-4
                  inline-flex
                  items-center
                  space-around
                  text-gray-700
                  bg-gray-100
                  border-0
                  py-2
                  px-6
                  focus:outline-none
                  hover:bg-gray-200
                  rounded
                  text-lg
                "
                data-clipboard-text="composer require w7corp/easywechat"
                @click="copy"
              >
                <code class="text-gray-700 bg-transparent flex items-center">
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
                href="/latest/"
                class="
                  inline-flex
                  items-center
                  space-around
                  text-white
                  bg-indigo-500
                  border-0
                  py-2
                  px-8
                  focus:outline-none
                  hover:bg-indigo-600
                  rounded
                  text-lg
                "
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
    </div>
    <section class="text-gray-600 body-font">
      <div class="container px-5 py-24 mx-auto">
        <h1
          class="
            sm:text-3xl
            text-2xl
            font-medium
            title-font
            text-center text-gray-900
            mb-20
          "
        >
          为什么选择 EasyWeChat ?
        </h1>
        <div
          class="
            flex flex-wrap
            sm:-m-4
            -mx-4
            -mb-10
            -mt-4
            md:space-y-0
            space-y-6
          "
        >
          <div class="p-4 md:w-1/3 flex">
            <div
              class="
                w-12
                h-12
                inline-flex
                items-center
                justify-center
                rounded-full
                bg-indigo-100
                text-indigo-500
                mb-4
                flex-shrink-0
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"
                />
              </svg>
            </div>
            <div class="flex-grow pl-6">
              <h2 class="text-gray-900 text-lg title-font font-medium mb-2">
                开箱即用
              </h2>
              <p class="leading-relaxed text-base">
                掩盖了开发者不需要关心的诸多细节，节省更多的时间专注于业务逻辑的开发。
              </p>
            </div>
          </div>
          <div class="p-4 md:w-1/3 flex">
            <div
              class="
                w-12
                h-12
                inline-flex
                items-center
                justify-center
                rounded-full
                bg-indigo-100
                text-indigo-500
                mb-4
                flex-shrink-0
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"
                />
              </svg>
            </div>
            <div class="flex-grow pl-6">
              <h2 class="text-gray-900 text-lg title-font font-medium mb-2">
                方便集成
              </h2>
              <p class="leading-relaxed text-base">
                各模块设计完美适配 PSR 系列标准，可以更方面的集成到各种框架中。
              </p>
            </div>
          </div>
          <div class="p-4 md:w-1/3 flex">
            <div
              class="
                w-12
                h-12
                inline-flex
                items-center
                justify-center
                rounded-full
                bg-indigo-100
                text-indigo-500
                mb-4
                flex-shrink-0
              "
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                />
              </svg>
            </div>
            <div class="flex-grow pl-6">
              <h2 class="text-gray-900 text-lg title-font font-medium mb-2">
                快速上手
              </h2>
              <p class="leading-relaxed text-base">
                仅封装复杂难懂的对接部分和简化接口调用方式，不做过度封装，降低学习成本。
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer class="text-gray-600 body-font">
      <div
        class="
          container
          px-5
          py-8
          mx-auto
          flex
          items-center
          sm:flex-row
          flex-col
        "
      >
        <a
          class="
            font-medium
            items-center
            md:justify-start
            justify-center
            text-gray-900
          "
        >
          <img src="/logo.svg" class="h-8" alt="" />
        </a>
        <p
          class="
            text-sm text-gray-500
            sm:ml-4
            sm:pl-4
            sm:border-l-2 sm:border-gray-200
            sm:py-2
            sm:mt-0
            mt-4
          "
        >
          一个基于 MIT 协议开源的非官方微信 SDK。 © 2021 微擎
        </p>
        <span
          class="
            inline-flex
            sm:ml-auto
            sm:mt-0
            mt-4
            justify-center
            sm:justify-start
          "
        >
          <a class="text-gray-500">
            <svg
              fill="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              class="w-5 h-5"
              viewBox="0 0 24 24"
            >
              <path
                d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"
              ></path>
            </svg>
          </a>
          <a class="ml-3 text-gray-500">
            <svg
              fill="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              class="w-5 h-5"
              viewBox="0 0 24 24"
            >
              <path
                d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"
              ></path>
            </svg>
          </a>
          <a class="ml-3 text-gray-500">
            <svg
              fill="none"
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              class="w-5 h-5"
              viewBox="0 0 24 24"
            >
              <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
              <path
                d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"
              ></path>
            </svg>
          </a>
          <a class="ml-3 text-gray-500">
            <svg
              fill="currentColor"
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="0"
              class="w-5 h-5"
              viewBox="0 0 24 24"
            >
              <path
                stroke="none"
                d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"
              ></path>
              <circle cx="4" cy="4" r="2" stroke="none"></circle>
            </svg>
          </a>
        </span>
      </div>
    </footer>
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
      console.log("该浏览器不支持自动复制");
      // 释放内存
      clipboard.destroy();
    });
  },
});
</script>