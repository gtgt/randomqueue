import Vue from 'vue';
import VueMaterial from 'vue-material';
import VueResource from 'vue-resource';
import TreeView from 'vue-json-tree-view';

import App from './App';

import 'vue-material/dist/vue-material.min.css';
import 'vue-material/dist/theme/default.css';


Vue.use(VueMaterial);
Vue.use(VueResource);
Vue.use(TreeView);

Vue.config.productionTip = false;
Vue.http.options.emulateJSON = true;

new Vue({
  el: '#app',
  components: { App },
  template: '<App />'
});
