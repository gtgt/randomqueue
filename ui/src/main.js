import Vue from 'vue';
import VueMaterial from 'vue-material';
import VueResource from 'vue-resource';

import App from './App.vue';

import 'vue-material/dist/vue-material.min.css';
import 'vue-material/dist/theme/default.css';

Vue.config.productionTip = false;

Vue.use(VueMaterial);
Vue.use(VueResource);

new Vue({
  el: '#app',
  components: { App },
  template: '<App />'
});
