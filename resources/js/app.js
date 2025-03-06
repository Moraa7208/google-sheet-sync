require('./bootstrap');

import Vue from 'vue';
import ItemApp from './components/ItemApp.vue';

Vue.component('item-app', ItemApp);

new Vue({
    el: '#app',
});
