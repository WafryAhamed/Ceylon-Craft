import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import Toast from './components/Toast.vue';

const app = createApp(App);
app.use(router);
app.component('Toast', Toast);
app.mount('#app');
