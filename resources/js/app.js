require('./bootstrap');

window.Vue = require('vue').default;

// Register components
Vue.component('example-component', require('./components/ExampleComponent.vue').default);

// Create Vue instance
const app = new Vue({
    el: '#app',
});
