import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify';
import vueRouter from 'vue-router';
import axios from 'axios'
import route from './route.js';
Vue.use(vueRouter);

axios.defaults.baseURL = "http://127.0.0.1:8000/api/"

Vue.config.productionTip = false

new Vue({
  vuetify,
  router: route,
  render: h => h(App)
}).$mount('#app')
