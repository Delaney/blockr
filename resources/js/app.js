require('./bootstrap');

window.Vue = require('vue');

Vue.config.devtools = true;
vue.config.performance = true;

import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(Vue);
Vue.use(VueRouter);

import App from './App.vue';
import Messages from './components/Messages.vue';

const router = new VueRouter({
	mode: 'history',
	routes: [
		{
			path: '/bot',
			name: 'app',
			component: Messages
		}
	]
});

const app = new Vue({
	el: '#app',
	components: { App },
	router
});