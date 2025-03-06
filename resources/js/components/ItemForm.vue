<template>
    <div class="bg-white rounded-md">
      <form @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input
              v-model="item.name"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Enter item name"
              required
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select
              v-model="item.status"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="Allowed">Allowed</option>
              <option value="Prohibited">Prohibited</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
            <input
              v-model="item.comments"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Add comments (optional)"
            >
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-2">
          <button
            v-if="editing"
            type="button"
            @click="$emit('cancel')"
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition duration-200"
            :disabled="isSubmitting"
          >
            Cancel
          </button>

          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200 flex items-center"
            :disabled="isSubmitting"
          >
            <span v-if="isSubmitting" class="mr-2">
              <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ editing ? 'Update Item' : 'Add Item' }}
          </button>
        </div>
      </form>
    </div>
  </template>

  <script>
  export default {
    name: 'ItemForm',
    props: {
      item: {
        type: Object,
        required: true
      },
      editing: {
        type: Boolean,
        default: false
      },
      isSubmitting: {
        type: Boolean,
        default: false
      }
    },
    emits: ['submit', 'update', 'cancel'],
    methods: {
      submitForm() {
        if (this.editing) {
          this.$emit('update');
        } else {
          this.$emit('submit');
        }
      }
    }
  };
  </script>
