<template>
    <div class="bg-white shadow rounded-lg p-6 mt-4">
        <h2 class="text-xl font-semibold text-gray-700">{{ title }}</h2>
        <p class="mt-2 text-gray-600">{{ message }}</p>
        <button @click="changeMessage" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
            Change Message
        </button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            title: 'Example Vue Component',
            message: 'This component is using Tailwind CSS with Vue.js and Laravel!'
        }
    },
    methods: {
        changeMessage() {
            this.message = 'The message has been changed!';
        }
    }
}
</script>
