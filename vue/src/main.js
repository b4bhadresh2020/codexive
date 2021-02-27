import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify';
import vueRouter from 'vue-router';
import axios from 'axios'
import route from './route.js';
// import ApiRequests from './apiRequests/apiRequest.js'

// const plugin = {
//     install () {
//         Vue.ApiRequests = ApiRequests
//         Vue.prototype.$ApiRequests = ApiRequests
//     }
// }
// Vue.use(plugin);

Vue.use(vueRouter);

axios.defaults.baseURL = "http://127.0.0.1:8000/api/"

Vue.prototype.$http = axios;

Vue.config.productionTip = false

new Vue({
  vuetify,
  router: route,
  render: h => h(App)
}).$mount('#app')
