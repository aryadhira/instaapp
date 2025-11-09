<template>
  <nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <div class="shrink-0 flex items-center">
            <span class="material-icons mr-2">camera</span>
            <span class="text-2xl font-bold">InstaApp</span>
          </div>
        </div>
        <div class="flex items-center">
          <div class="shrink-0">
            <button type="button"
              class="relative inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#ff4d6d] shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2">
              <span class="material-icons mr-2 rotate-340">send</span>
              Create Post
            </button>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'

const mobileMenuOpen = ref(false)
</script>