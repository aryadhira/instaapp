<template>
  <div>
    <ResponsiveNavbar />
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

onMounted(async () => {
  const res = await $apiFetch('/posts', { "method": "GET" })
  console.log(res)
  if (res.status == "success") {
    console.log("test")
    postData.value = res.posts
  }
})

</script>