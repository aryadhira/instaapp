<template>
  <nav class="bg-white shadow-md">
    <Toast :type="toastType" :message="toastMessage" :show="showToast" />
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
            <button 
              type="button"
              :disabled="showModal"
              class="relative inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#ff4d6d] shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2"
              @click="showModal = true">
              <span class="material-icons mr-2 rotate-325">send</span>
              Create Post
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Create Post Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Create New Post</h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
              <span class="material-icons">close</span>
            </button>
          </div>
          
          <form @submit.prevent="submitPost">
            <div class="mb-4">
              <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
              <textarea
                id="content"
                v-model="content"
                placeholder="What's on your mind?"
                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff4d6d] focus:border-[#ff4d6d]"
                rows="3"
                :disabled="loading"
              ></textarea>
              <p v-if="errors.content" class="mt-1 text-sm text-red-600">{{ errors.content }}</p>
            </div>
            
            <div class="mb-4">
              <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
              <div class="flex items-center justify-center w-full">
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                  <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <span class="material-icons text-gray-400">cloud_upload</span>
                    <p class="mb-2 text-sm text-gray-500">
                      <span class="font-semibold">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF (MAX. 5MB)</p>
                  </div>
                  <input 
                    id="image" 
                    type="file" 
                    class="hidden" 
                    accept="image/*" 
                    @change="handleImageUpload"
                    :disabled="loading"
                  >
                </label>
              </div>
              <p v-if="errors.image" class="mt-1 text-sm text-red-600">{{ errors.image }}</p>
              
              <!-- Image Preview -->
              <div v-if="imagePreview" class="mt-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                <img :src="imagePreview" alt="Preview" class="max-h-48 rounded-lg object-contain" />
              </div>
            </div>
            
            <div class="flex justify-end space-x-3">
              <button 
                type="button" 
                @click="closeModal" 
                :disabled="loading"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ff4d6d] disabled:opacity-50"
              >
                Cancel
              </button>
              <button 
                type="submit" 
                :disabled="loading"
                class="px-4 py-2 text-sm font-medium text-white bg-[#ff4d6d] border border-transparent rounded-md shadow-sm hover:bg-[#e03a5c] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#ff4d6d] disabled:opacity-50 flex items-center"
              >
                <span v-if="loading" class="material-icons animate-spin mr-2">autorenew</span>
                <span>{{ loading ? 'Posting...' : 'Post' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
const { $apiFetch } = useNuxtApp()
const mobileMenuOpen = ref(false)
const showModal = ref(false)
const content = ref('')
const imageFile = ref(null)
const imagePreview = ref(null)
const loading = ref(false)
const errors = ref({
  content: '',
  image: ''
})

// Reactive variables for toast
const showToast = ref(false)
const toastType = ref('')
const toastMessage = ref('')

// Function to show toast notification
const showToastMessage = (type, message) => {
  toastType.value = type
  toastMessage.value = message
  showToast.value = true
  
  // Auto-hide toast after 3 seconds
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}

// Handle image upload and preview
const handleImageUpload = (event) => {
  const file = event.target.files[0]
  
  if (file) {
    // Check if file is an image
    if (!file.type.startsWith('image/')) {
      errors.value.image = 'Please select an image file (JPEG, PNG, GIF, etc.)'
      return
    }
    
    // Check file size (5MB = 5 * 1024 * 1024 bytes)
    if (file.size > 5 * 1024 * 1024) {
      errors.value.image = 'File size exceeds 5MB limit'
      return
    }
    
    // Clear previous errors
    errors.value.image = ''
    
    // Set the file and create preview
    imageFile.value = file
    
    // Create a preview URL
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

// Validate form
const validateForm = () => {
  let isValid = true
  errors.value.content = ''
  errors.value.image = ''
  
  if (!content.value.trim()) {
    errors.value.content = 'Content cannot be empty'
    isValid = false
  }
  
  if (!imageFile.value) {
    errors.value.image = 'Please select an image'
    isValid = false
  }
  
  return isValid
}

// Submit the post
const submitPost = async () => {
  if (!validateForm()) return
  
  loading.value = true
  
  try {
    const formData = new FormData()
    formData.append('content', content.value)
    formData.append('image', imageFile.value)
    
    // Send the post to the API
    await $apiFetch('/posts', {
      method: 'POST',
      body: formData
    })
    
    // Reset form
    content.value = ''
    imageFile.value = null
    imagePreview.value = null
    
    // Close modal
    closeModal()
    
    // Show success toast
    showToastMessage('success', 'Post created successfully!')
    
  } catch (error) {
    // Show error message
    const errorMessage = error.data?.message || 'Failed to create post. Please try again.'
    showToastMessage('error', errorMessage)
  } finally {
    loading.value = false
  }
}

// Close modal and reset form
const closeModal = () => {
  showModal.value = false
  content.value = ''
  imageFile.value = null
  imagePreview.value = null
  errors.value = {
    content: '',
    image: ''
  }
}
</script>