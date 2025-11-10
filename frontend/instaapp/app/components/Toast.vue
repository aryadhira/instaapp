<template>
  <div 
    v-if="show"
    class="fixed top-4 right-4 transform transition-all duration-300 z-50 max-w-sm w-full"
    :class="{ 
      'opacity-100 translate-y-0': visible, 
      'opacity-0 translate-y-4': !visible,
      'pointer-events-none': !visible
    }"
  >
    <div 
      class="flex items-center p-4 rounded-lg shadow-lg"
      :class="toastClasses"
    >
      <span 
        class="material-icons mr-3"
        :class="iconClasses"
      >
        {{ icon }}
      </span>
      <span class="text-white font-medium">{{ message }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: (value) => ['success', 'error', 'warning'].includes(value)
  },
  message: {
    type: String,
    required: true
  },
  show: {
    type: Boolean,
    default: false
  }
})

const visible = ref(false)

// Determine classes based on toast type
const toastClasses = computed(() => {
  switch (props.type) {
    case 'success':
      return 'bg-green-500'
    case 'error':
      return 'bg-red-500'
    case 'warning':
      return 'bg-yellow-500'
    default:
      return 'bg-gray-500'
  }
})

const iconClasses = computed(() => {
  switch (props.type) {
    case 'success':
      return 'text-green-200'
    case 'error':
      return 'text-red-200'
    case 'warning':
      return 'text-yellow-200'
    default:
      return 'text-gray-200'
  }
})

const icon = computed(() => {
  switch (props.type) {
    case 'success':
      return 'check_circle'
    case 'error':
      return 'error'
    case 'warning':
      return 'warning'
    default:
      return 'info'
  }
})

watch(() => props.show, (newShow) => {
  if (newShow) {
    visible.value = true
    setTimeout(() => {
      visible.value = false
      setTimeout(() => {
      }, 300)
    }, 3000)
  } else {
    visible.value = false
  }
})

onMounted(() => {
  if (props.show) {
    visible.value = true
  }
})
</script>