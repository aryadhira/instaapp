<template>
  <div>
    <ResponsiveNavbar />
    <div class="container mx-auto py-8">
      <!-- Example usage of FeedPostCard component -->
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

// Example post data
const examplePost = {
  "post_id": "0746ffbb-5152-4543-b930-ca25b345a1b9",
  "user_id": "651d75b9-08e6-499a-aad0-c4203b6bc49f",
  "username": "admin",
  "content": "First Post #first",
  "image_url": "/post-images/651d75b9-08e6-499a-aad0-c4203b6bc49f_d889f180-f8a1-4b41-9f02-9aa29f37b8b2_1762663396.png",
  "likes": [
    {
      "user_id": "651d75b9-08e6-499a-aad0-c4203b6bc49f",
      "username": "admin"
    }
  ],
  "comments": [
    {
      "user_id": "651d75b9-08e6-499a-aad0-c4203b6bc49f",
      "username": "admin",
      "content": "update first comment"
    }
  ]
}
</script>