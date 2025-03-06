<template>
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Google Sheet Configuration</h2>
      <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-grow">
          <label for="sheet-url" class="sr-only">Google Sheet URL</label>
          <input
            id="sheet-url"
            v-model="localSheetUrl"
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Enter your Google Sheet URL"
            :disabled="isLoading"
          >
        </div>
        <button
          @click="save"
          class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md transition duration-200 flex items-center justify-center min-w-[100px]"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="mr-2">
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          Save URL
        </button>
      </div>
      <p class="mt-2 text-sm text-gray-500">
        Enter the URL of the Google Sheet you want to sync with your items.
      </p>
    </div>
  </template>

  <script>
  export default {
    name: 'SheetUrlInput',
    props: {
      sheetUrl: {
        type: String,
        default: ''
      },
      isLoading: {
        type: Boolean,
        default: false
      }
    },
    emits: ['save-sheet-url'],
    data() {
      return {
        localSheetUrl: this.sheetUrl
      };
    },
    watch: {
      sheetUrl(newVal) {
        this.localSheetUrl = newVal;
      }
    },
    methods: {
      save() {
        if (!this.localSheetUrl.trim()) {
          alert('Please enter a Google Sheet URL');
          return;
        }

        if (!this.isValidUrl(this.localSheetUrl)) {
          alert('Please enter a valid URL');
          return;
        }

        this.$emit('save-sheet-url', this.localSheetUrl);
      },
      isValidUrl(string) {
        try {
          new URL(string);
          return true;
        } catch (_) {
          return false;
        }
      }
    }
  };
  </script>
