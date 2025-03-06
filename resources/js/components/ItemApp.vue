// ItemApp.vue
<template>
  <div class="p-6 max-w-5xl mx-auto bg-white shadow-lg rounded-lg">
    <header class="border-b pb-4 mb-6">
      <h1 class="text-3xl font-bold text-blue-700">Item Management System</h1>
      <p class="text-gray-600 mt-1">Manage and sync your items with Google Sheets</p>
    </header>

    <sheet-url-input
      :sheet-url="sheetUrl"
      :is-loading="isSheetUrlLoading"
      @save-sheet-url="saveSheetUrl"
      class="mb-6"
    />

    <div class="mb-8 space-y-6">
      <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
          {{ editingItem ? 'Edit Item' : 'Add New Item' }}
        </h2>
        <item-form
          :item="editingItem || newItem"
          :editing="!!editingItem"
          :is-submitting="isSubmitting"
          @submit="createItem"
          @update="updateItem"
          @cancel="cancelEdit"
        />
      </div>

      <div class="flex flex-wrap gap-3">
        <button
          @click="fetchFromSheet"
          class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-md flex items-center transition duration-200"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="mr-2">
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Fetch from Sheet
        </button>

        <button
          @click="generateItems"
          class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-md flex items-center transition duration-200"
          :disabled="isLoading"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
          </svg>
          Generate 1000 Rows
        </button>

        <button
          @click="clearItems"
          class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-md flex items-center transition duration-200"
          :disabled="isLoading"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          Clear Table
        </button>
      </div>

      <fetch-output :fetch-output="fetchOutput" />

      <div class="mt-6">
        <div class="flex justify-between items-center mb-3">
          <h2 class="text-xl font-semibold text-gray-800">Items List</h2>
          <div class="text-sm text-gray-500">
            {{ items.length }} items total
          </div>
        </div>
        <item-list
          :items="items"
          :is-loading="isItemsLoading"
          @edit="startEdit"
          @delete="confirmDelete"
        />
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Confirm Delete</h3>
        <p class="mb-6 text-gray-700">Are you sure you want to delete this item? This action cannot be undone.</p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
            Cancel
          </button>
          <button @click="executeDelete" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ItemList from './ItemList.vue';
import ItemForm from './ItemForm.vue';
import SheetUrlInput from './SheetUrlInput.vue';
import FetchOutput from './FetchOutput.vue';

export default {
  name: 'ItemApp',
  components: {
    ItemList,
    ItemForm,
    SheetUrlInput,
    FetchOutput
  },
  data() {
    return {
      items: [],
      newItem: { name: '', status: 'Allowed', comments: '' },
      editingItem: null,
      sheetUrl: '',
      fetchOutput: '',
      isLoading: false,
      isItemsLoading: false,
      isSubmitting: false,
      isSheetUrlLoading: false,
      showDeleteModal: false,
      itemToDelete: null
    };
  },
  mounted() {
    this.fetchItems();
    this.fetchSheetUrl();
  },
  methods: {
    fetchItems() {
      this.isItemsLoading = true;
      fetch('/api/items')
        .then(response => response.json())
        .then(data => {
          this.items = data;
          this.isItemsLoading = false;
        })
        .catch(error => {
          console.error('Error fetching items:', error);
          this.isItemsLoading = false;
        });
    },
    createItem() {
      if (!this.newItem.name.trim()) {
        alert('Item name is required');
        return;
      }

      this.isSubmitting = true;
      fetch('/api/items', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(this.newItem)
      })
      .then(response => {
        if (!response.ok) throw new Error(`Failed to create item: ${response.status}`);
        return response.json();
      })
      .then(() => {
        this.fetchItems();
        this.newItem = { name: '', status: 'Allowed', comments: '' };
        this.isSubmitting = false;
      })
      .catch(error => {
        console.error('Error creating item:', error);
        this.isSubmitting = false;
        alert('Failed to create item. Please try again.');
      });
    },
    confirmDelete(id) {
      this.itemToDelete = id;
      this.showDeleteModal = true;
    },
    executeDelete() {
      if (!this.itemToDelete) return;

      this.isLoading = true;
      fetch(`/api/items/${this.itemToDelete}`, { method: 'DELETE' })
        .then(response => {
          if (!response.ok) throw new Error(`Delete failed: ${response.status}`);
          this.fetchItems();
          this.showDeleteModal = false;
          this.itemToDelete = null;
          this.isLoading = false;
        })
        .catch(error => {
          console.error('Error deleting item:', error);
          this.isLoading = false;
          alert('Failed to delete item. Please try again.');
        });
    },
    startEdit(item) {
      this.editingItem = { ...item };
    },
    updateItem() {
      if (!this.editingItem.name.trim()) {
        alert('Item name is required');
        return;
      }

      this.isSubmitting = true;
      fetch(`/api/items/${this.editingItem.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(this.editingItem)
      })
      .then(response => {
        if (!response.ok) throw new Error(`Update failed: ${response.status}`);
        return response.json();
      })
      .then(() => {
        this.fetchItems();
        this.editingItem = null;
        this.isSubmitting = false;
      })
      .catch(error => {
        console.error('Error updating item:', error);
        this.isSubmitting = false;
        alert('Failed to update item. Please try again.');
      });
    },
    generateItems() {
      if (!confirm("This will generate 1000 random items. Are you sure?")) return;

      this.isLoading = true;
      fetch('/api/items/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
      })
      .then(response => response.json())
      .then(data => {
        this.items = data;
        this.isLoading = false;
      })
      .catch(error => {
        console.error('Error generating items:', error);
        this.isLoading = false;
        alert('Failed to generate items. Please try again.');
      });
    },
    clearItems() {
      if (!confirm("This will delete ALL items from the database. Are you sure?")) return;

      this.isLoading = true;
      fetch('/api/items/clear', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
      })
      .then(() => {
        this.fetchItems();
        this.isLoading = false;
      })
      .catch(error => {
        console.error('Error clearing items:', error);
        this.isLoading = false;
        alert('Failed to clear items. Please try again.');
      });
    },
    fetchSheetUrl() {
      this.isSheetUrlLoading = true;
      fetch('/api/items/sheet-url', {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        if (!response.ok) throw new Error(`Fetch failed with status: ${response.status}`);
        return response.json();
      })
      .then(data => {
        this.sheetUrl = data.google_sheet_url || '';
        this.isSheetUrlLoading = false;
      })
      .catch(error => {
        console.error('Fetch Sheet URL Error:', error);
        this.isSheetUrlLoading = false;
      });
    },
    saveSheetUrl(url) {
      this.isSheetUrlLoading = true;
      fetch('/api/items/sheet-url', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ google_sheet_url: url })
      })
      .then(response => {
        if (!response.ok) throw new Error(`Save failed with status: ${response.status}`);
        return response.json();
      })
      .then(() => {
        this.fetchSheetUrl();
        this.isSheetUrlLoading = false;
      })
      .catch(error => {
        console.error('Save Sheet URL Error:', error);
        this.isSheetUrlLoading = false;
        alert('Failed to save sheet URL. Please try again.');
      });
    },
    fetchFromSheet() {
      this.isLoading = true;
      this.fetchOutput = 'Loading data from Google Sheet...';

      fetch('/api/fetch')
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          this.fetchOutput = data.output;
          this.fetchItems();
          this.isLoading = false;
        })
        .catch(error => {
          this.fetchOutput = 'Error: ' + error.message;
          this.isLoading = false;
        });
    },
    cancelEdit() {
      this.editingItem = null;
    }
  }
};
</script>
