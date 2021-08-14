<template>
  <div>
    <div class="bg-gray-800">
      <header class="text-gray-100 body-font">
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
            <svg
              viewBox="0 0 126 131"
              class="h-8 w-8"
              version="1.1"
              xmlns="http://www.w3.org/2000/svg"
              xmlns:xlink="http://www.w3.org/1999/xlink"
            >
              <g
                id="Page-1"
                stroke="none"
                stroke-width="1"
                fill="none"
                fill-rule="evenodd"
              >
                <g id="Brand" transform="translate(-182.000000, -30.000000)">
                  <g id="icon" transform="translate(150.000000, 0.000000)">
                    <polygon
                      id="Rectangle-Copy-3"
                      fill-opacity="0.882189764"
                      fill="#5477F8"
                      points="32.1818182 63 96 95 96 161 32 128.818182"
                    ></polygon>
                    <polygon
                      id="Rectangle"
                      fill="#A4C4FA"
                      opacity="0.6"
                      points="96 94.9393382 158 62 158 128.060662 96 161"
                    ></polygon>
                    <polygon
                      id="Rectangle-Copy"
                      fill="#F5F5F5"
                      points="65 78.9393382 127 46 127 112.060662 65 145"
                    ></polygon>
                    <polygon
                      id="Rectangle-Copy-2"
                      fill-opacity="0.882189764"
                      fill="#5477F8"
                      points="32.1818182 63.2422018 96 30 96 95.9394495 32 129"
                    ></polygon>
                  </g>
                </g>
              </g>
            </svg>
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

      <section class="text-gray-600 body-font">
        <div
          class="
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
              <a
                href="/docs"
                class="
                  inline-flex
                  items-center
                  space-around
                  text-white
                  bg-indigo-500
                  border-0
                  py-2
                  px-6
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
              <button
                class="
                  ml-4
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
            </div>
          </div>
          <div class="lg:max-w-lg lg:w-full md:w-1/2 w-5/6">
            <img
              class="object-cover object-center rounded"
              alt="hero"
              src="https://dummyimage.com/720x600"
            />
          </div>
        </div>
      </section>
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
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                class="w-6 h-6"
                viewBox="0 0 24 24"
              >
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
              </svg>
            </div>
            <div class="flex-grow pl-6">
              <h2 class="text-gray-900 text-lg title-font font-medium mb-2">
                Shooting Stars
              </h2>
              <p class="leading-relaxed text-base">
                Blue bottle crucifix vinyl post-ironic four dollar toast vegan
                taxidermy. Gastropub indxgo juice poutine, ramps microdosing
                banh mi pug VHS try-hard ugh iceland kickstarter tumblr
                live-edge tilde.
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
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                class="w-6 h-6"
                viewBox="0 0 24 24"
              >
                <circle cx="6" cy="6" r="3"></circle>
                <circle cx="6" cy="18" r="3"></circle>
                <path
                  d="M20 4L8.12 15.88M14.47 14.48L20 20M8.12 8.12L12 12"
                ></path>
              </svg>
            </div>
            <div class="flex-grow pl-6">
              <h2 class="text-gray-900 text-lg title-font font-medium mb-2">
                The Catalyzer
              </h2>
              <p class="leading-relaxed text-base">
                Blue bottle crucifix vinyl post-ironic four dollar toast vegan
                taxidermy. Gastropub indxgo juice poutine, ramps microdosing
                banh mi pug VHS try-hard ugh iceland kickstarter tumblr
                live-edge tilde.
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
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                class="w-6 h-6"
                viewBox="0 0 24 24"
              >
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>
            <div class="flex-grow pl-6">
              <h2 class="text-gray-900 text-lg title-font font-medium mb-2">
                Neptune
              </h2>
              <p class="leading-relaxed text-base">
                Blue bottle crucifix vinyl post-ironic four dollar toast vegan
                taxidermy. Gastropub indxgo juice poutine, ramps microdosing
                banh mi pug VHS try-hard ugh iceland kickstarter tumblr
                live-edge tilde.
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
            flex
            title-font
            font-medium
            items-center
            md:justify-start
            justify-center
            text-gray-900
          "
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            class="w-10 h-10 text-white p-2 bg-indigo-500 rounded-full"
            viewBox="0 0 24 24"
          >
            <path
              d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
            ></path>
          </svg>
          <span class="ml-3 text-xl">EasyWeChat</span>
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
          EasyWeChat 是一个基于 MIT 协议开源的非官方微信 SDK。 © 2021 微擎
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