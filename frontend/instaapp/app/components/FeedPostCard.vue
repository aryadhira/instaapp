<template>
  <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 max-w-lg mx-auto">
    <!-- Post Header -->
    <div class="flex items-center p-3">
      <img :src="`https://avatar.iran.liara.run/public/10`" alt="User Avatar"
        class="w-10 h-10 rounded-full object-cover mr-3" />
      <div class="flex-1">
        <div class="font-semibold text-gray-900">{{ post.username }}</div>
        <div class="text-xs text-gray-500">few minutes ago</div>
      </div>
    </div>

    <!-- Post Image -->
    <div class="w-full">
      <img :src="config.public.baseUrl + post.image_url" :alt="`Post by ${post.username}`" class="w-full max-h-96" />
    </div>

    <!-- Post Actions -->
    <div class="p-3">
      <div class="flex items-center space-x-4 mb-2">
        <button class="focus:outline-none" @click="toggleLike">
          <span class="material-icons text-2xl" :class="{ 'text-red-500': isLiked, 'text-gray-700': !isLiked }">
            {{ isLiked ? 'favorite' : 'favorite_border' }}
          </span>
        </button>
        <button class="focus:outline-none" @click="openComments = true">
          <span class="material-icons text-2xl text-gray-700">chat_bubble_outline</span>
        </button>

      </div>

      <!-- Likes Count -->
      <div class="text-sm font-semibold text-gray-900 mb-1">
        {{ post.likes.length }} likes
      </div>

      <!-- Caption -->
      <div class="text-sm mb-2">
        <span class="font-semibold text-gray-900 mr-2">{{ post.username }}</span>
        {{ post.content }}
      </div>

      <!-- View Comments Button -->
      <button v-if="post.comments.length > 0" class="text-xs text-gray-500 mb-1" @click="openComments = true">
        View all {{ post.comments.length }} comments
      </button>
    </div>

    <!-- Comment Modal -->
    <div v-if="openComments" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4"
      @click="closeComments">
      <div class="bg-white rounded-lg w-full max-w-md max-h-[80vh] flex flex-col" @click.stop>
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b">
          <span class="font-semibold">Comments</span>
          <button @click="closeComments" class="text-gray-500">
            <span class="material-icons">close</span>
          </button>
        </div>

        <!-- Comments List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
          <div v-for="(comment, index) in post.comments" :key="index" class="flex gap-2 items-start">
            <img :src="`https://avatar.iran.liara.run/public/10`" alt="User Avatar"
              class="w-8 h-8 rounded-full object-cover mr-2" />
            <div class="flex-1">
              <div class="bg-gray-100 rounded-lg p-2">
                <div class="font-semibold text-sm">{{ comment.username }}</div>
                <div class="text-sm">{{ comment.content }}</div>
              </div>
            </div>
            <span @click="deleteComment(comment.id)" v-if="comment.username == username"
              class="material-icons text-red-500 cursor-pointer">delete</span>
          </div>
        </div>

        <!-- Comment Input -->
        <div class="p-4 border-t flex items-center">
          <input v-model="newComment" type="text" placeholder="Add a comment..."
            class="flex-1 border-0 focus:ring-0 p-2 mr-5 text-sm" @keyup.enter="addComment" />
          <button :disabled="!newComment.trim()"
            :class="{ 'text-blue-500': newComment.trim(), 'text-gray-400': !newComment.trim() }" @click="addComment"
            class="font-semibold">
            Post
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const { $apiFetch } = useNuxtApp()

const props = defineProps({
  post: {
    type: Object,
    required: true
  }
})

const username = useCookie("username")
const userId = useCookie("user_id")

const config = useRuntimeConfig()
const openComments = ref(false)
const newComment = ref('')
const isLiked = ref(false)

// Check if current user has liked the post
const toggleLike = async () => {
  isLiked.value = !isLiked.value
  if (isLiked.value) {
    props.post.likes.push({
      user_id: userId.value,
      username: username.value
    })
  } else {
    const idx = props.post.likes.findIndex(obj => obj.user_id == props.post.user_id)
    if (idx !== -1) {
      props.post.likes.splice(idx, 1)
    }
  }
  const param = { post_id: props.post.post_id }
  await $apiFetch("/likes", {
    method: 'POST',
    body: param,
  }
  )

}

const closeComments = () => {
  openComments.value = false
  newComment.value = ''
}

const addComment = async () => {
  if (newComment.value.trim()) {
    props.post.comments.unshift({
      username: username.value,
      content: newComment.value
    })
    const param = { post_id: props.post.post_id, content: newComment.value }
    await $apiFetch("/comments", {
      method: 'POST',
      body: param,
    }
    )
    newComment.value = ''
  }
}

const deleteComment = async (commentId) => {
  const param = { comment_id: commentId }
  await $apiFetch("/comments", {
    method: 'DELETE',
    body: param,
  }
  )
  const idx = props.post.comments.findIndex(obj => obj.id == commentId)
  if (idx !== -1) {
    props.post.comments.splice(idx, 1)
  }
}

onMounted(() => {
  isLiked.value = props.post.likes.findIndex(obj => obj.user_id == userId.value) !== -1
})
</script>