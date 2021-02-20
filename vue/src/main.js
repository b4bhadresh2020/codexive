import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify';
import vueRouter from 'vue-router';
import axios from 'axios'
Vue.use(vueRouter);

axios.defaults.baseURL = "http://decd265b3d6d.ngrok.io/api/"

Vue.config.productionTip = false

new Vue({
  vuetify,
  render: h => h(App)
}).$mount('#app')
