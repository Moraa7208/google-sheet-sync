<template>
    <div v-if="fetchOutput" class="bg-gray-50 p-4 rounded-lg border border-gray-200 overflow-x-auto">
      <h2 class="text-lg font-semibold text-gray-800 mb-2">Fetch Output</h2>
      <div class="bg-white p-3 rounded border border-gray-200 text-sm font-mono">
        <pre class="whitespace-pre-wrap break-words text-gray-700">{{ fetchOutput }}</pre>
      </div>
    </div>
  </template>

  <script>
  export default {
    name: 'FetchOutput',
    props: {
      fetchOutput: {
        type: String,
        default: ''
      }
    }
  };
  </script>
