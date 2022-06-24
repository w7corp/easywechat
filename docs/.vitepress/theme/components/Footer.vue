<script lang="ts" setup>
import { useData } from 'vitepress'

const { theme } = useData()
</script>

<template>
  <div
    class="text-center border-t dark:border-black leading-loose py-6 text-xs"
  >
    <p v-if="theme.license" class="license">
      Released under the
      <a class="link" :href="theme.license.link" no-icon>
        {{ theme.license.text }} </a
      >.
    </p>

    <p
      v-if="theme.copyright"
      class="copyright"
      v-html="theme.copyright"
    ></p>
  </div>
</template>
