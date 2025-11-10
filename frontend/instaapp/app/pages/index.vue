<template>
  <div>
    <ResponsiveNavbar @post-created="loadFeed" />
    <div class="container mx-auto py-8">
      <div v-for="(post, index) in postData" :key="index">
        <FeedPostCard :post="post" />
      </div>
    </div>
  </div>
</template>

<script setup>
const { $apiFetch } = useNuxtApp()
const postData = ref([])

const loadFeed = async() => {
  const res = await $apiFetch('/posts', { "method": "GET" })
  if (res.status == "success") {
    postData.value = res.posts
  }
}

onMounted(async () => {
  await loadFeed()
})

</script>