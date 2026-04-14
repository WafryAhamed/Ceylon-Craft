import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import Toast from './components/Toast.vue';

const app = createApp(App);

// Initialize Pinia for state management
app.use(createPinia());

// Register router
app.use(router);

// Register global components
app.component('Toast', Toast);

// Mount application
app.mount('#app');
