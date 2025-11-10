<template>
    <div class="flex flex-col">
        <Toast :type="toastType" :message="toastMessage" :show="showToast" />
        <div class="flex justify-center mt-50">
            <div class="flex flex-col gap-5 p-3 rounded-xl shadow-xl">
                <div class="font-bold text-xl">Register</div>
                <div class=" font-medium text-sm">Create your account to start sharing.</div>
                <div class="">
                    <input v-model="username" placeholder="Username" class="w-100 p-2 rounded-lg bg-gray-100"></input>
                </div>
                <div class="">
                    <input v-model="email" placeholder="Email" class="w-100 p-2 rounded-lg bg-gray-100"></input>
                </div>
                <div class="">
                    <input v-model="password" placeholder="Password" type="password"
                        class="w-100 p-2 rounded-lg bg-gray-100"></input>
                </div>
                <div class="">
                    <input v-model="confirmPassword" placeholder="Confirm Password" type="password"
                        class="w-100 p-2 rounded-lg bg-gray-100"></input>
                </div>
                <button @click="register" class="bg-[#ff4d6d] w-100 p-3 rounded-lg text-white cursor-pointer">Create Account</button>
            </div>
        </div>
    </div>


</template>

<script setup>

definePageMeta({ auth: false })
const { $apiFetch } = useNuxtApp()

const username = ref("")
const email = ref("")
const password = ref("")
const confirmPassword = ref("")
const showToast = ref(false)
const toastType = ref("")
const toastMessage = ref("")

// Function to show toast notification
const showToastMessage = (type, message) => {
    toastType.value = type
    toastMessage.value = message
    showToast.value = true
    
    setTimeout(() => {
        showToast.value = false
    }, 3000)
}

const register = async () => {
    // Reset toast state
    showToast.value = false
    
    // Validation checks
    if (!username.value.trim()) {
        showToastMessage('error', 'Please enter a username')
        return
    }
    
    if (!email.value.trim()) {
        showToastMessage('error', 'Please enter an email')
        return
    }
    
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
    if (!emailRegex.test(email.value)) {
        showToastMessage('error', 'Please enter a valid email address')
        return
    }
    
    if (!password.value) {
        showToastMessage('error', 'Please enter a password')
        return
    }
    
    if (password.value.length < 6) {
        showToastMessage('error', 'Password must be at least 6 characters long')
        return
    }
    
    if (password.value !== confirmPassword.value) {
        showToastMessage('error', 'Passwords do not match')
        return
    }
    
    try {
        const response = await $apiFetch('/register', {
            method: 'POST',
            body: {
                username: username.value,
                email: email.value,
                password: password.value
            }
        })
        
        showToastMessage('success', 'Registration successful! Welcome to InstaApp.')
        
        setTimeout(() => {
            navigateTo('/login')
        }, 2000)
        
    } catch (error) {
        const errorMessage = error.data?.message || 'Registration failed. Please try again.'
        showToastMessage('error', errorMessage)
    }
}
</script>