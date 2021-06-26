import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify';
import vueRouter from 'vue-router';
import axios from 'axios'
import route from './routes.js';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

Vue.use(Toast, {
  transition: "Vue-Toastification__bounce",
  maxToasts: 20,
  newestOnTop: true,
  position: "top-right",
  timeout: 5000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: true,
  closeButton: "button",
  icon: true,
  rtl: false
});

Vue.use(vueRouter);

axios.defaults.baseURL = "http://127.0.0.1:8000/api/"

Vue.prototype.$http = axios;

Vue.config.productionTip = false

new Vue({
  vuetify,
  router: route,
  render: h => h(App)
}).$mount('#app')
