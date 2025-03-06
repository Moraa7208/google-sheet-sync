<template>
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
      <div v-if="isLoading" class="flex justify-center items-center p-12">
        <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>

      <div v-else-if="items.length === 0" class="p-8 text-center text-gray-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <p class="text-lg">No items found</p>
        <p class="text-sm mt-2">Add a new item or fetch data from Google Sheets</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-50 text-left">
              <th class="px-4 py-3 text-sm font-semibold text-gray-600">ID</th>
              <th class="px-4 py-3 text-sm font-semibold text-gray-600">Name</th>
              <th class="px-4 py-3 text-sm font-semibold text-gray-600">Status</th>
              <th class="px-4 py-3 text-sm font-semibold text-gray-600">Comments</th>
              <th class="px-4 py-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in items"
              :key="item.id"
              class="border-t border-gray-200 hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3 text-sm text-gray-500">{{ item.id }}</td>
              <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ item.name }}</td>
              <td class="px-4 py-3 text-sm">
                <span
                  :class="[
                    'inline-flex px-2 py-1 text-xs font-medium rounded-full',
                    item.status === 'Allowed'
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ item.status }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-gray-500">{{ item.comments || '-' }}</td>
              <td class="px-4 py-3 text-sm">
                <div class="flex space-x-2">
                  <button
                    @click="$emit('edit', item)"
                    class="text-blue-600 hover:text-blue-800 transition-colors"
                    title="Edit"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="$emit('delete', item.id)"
                    class="text-red-600 hover:text-red-800 transition-colors"
                    title="Delete"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>

  <script>
  export default {
    name: 'ItemList',
    props: {
      items: {
        type: Array,
        required: true
      },
      isLoading: {
        type: Boolean,
        default: false
      }
    },
    emits: ['edit', 'delete']
  };
  </script>
