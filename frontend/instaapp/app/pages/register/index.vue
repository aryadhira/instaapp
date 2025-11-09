<template>
    <div class="flex flex-col">
        <div class="flex justify-center mt-50">
            <div class="flex flex-col gap-5 p-3 rounded-xl shadow-xl">
                <div class="font-bold text-xl">Register</div>
                <div class=" font-medium text-sm">Create your account to start sharing.</div>
                <div class="">
                    <input placeholder="Username" class="w-100 p-2 rounded-lg bg-gray-100"></input>
                </div>
                <div class="">
                    <input placeholder="Email" class="w-100 p-2 rounded-lg bg-gray-100"></input>
                </div>
                <div class="">
                    <input placeholder="Password" class="w-100 p-2 rounded-lg bg-gray-100"></input>
                </div>
                <div class="">
                    <input placeholder="Confirm Password" class="w-100 p-2 rounded-lg bg-gray-100"></input>
                </div>
                <button class="bg-[#ff4d6d] w-100 p-3 rounded-lg text-white">Create Account</button>
            </div>
        </div>
    </div>


</template>

<script setup>
definePageMeta({ auth: false })


</script>