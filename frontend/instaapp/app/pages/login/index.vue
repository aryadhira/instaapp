<template>
    <div class="flex flex-col">
        <div class="flex justify-center items-center mt-50 font-extrabold text-5xl">InstaApp</div>
        <div class="flex justify-center mt-10">
            <div class="flex flex-col gap-5 p-3 rounded-xl shadow-xl">
                <div class="font-bold text-xl">Login</div>
                <div class=" font-medium text-sm">Welcome back, Enter your credentials to continue</div>
                <div>
                    <input placeholder="Username / Email" type="text" v-model="identifier" class="w-100 p-2 rounded-lg bg-gray-100"></input>
                </div>
                <div>
                    <input placeholder="Password" type="password" v-model="password" class="w-100 p-2 rounded-lg bg-gray-100"></input>
                </div>
                <button :disabled="loading" class="bg-[#ff4d6d] w-100 p-3 rounded-lg text-white cursor-pointer" @click="onSubmit">Login</button>
                <div class="flex gap-2 items-center">
                    <div class="font-medium text-sm">don't have account?</div>
                    <a href="/register" class="bg-[#0ea5a4] text-sm p-1 text-white rounded-sm cursor-pointer">Create Account</a>
                </div>
            </div>
        </div>
    </div>


</template>

<script setup>
definePageMeta({ auth: false })

const auth = useAuth()
const router = useRouter()
const identifier = ref('')
const password = ref('')

const loading = ref(false)

const onSubmit = async() =>{
    loading.value = true
    const param = {
        identifier: identifier.value,
        password: password.value
    }

    await auth.login(param)
    await navigateTo("/")
}


</script>