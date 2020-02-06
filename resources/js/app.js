require('./bootstrap');

// window.Vue = require('vue');


import Vue from 'vue';
import VueRouter from 'vue-router';

// Vue.config.devtools = true;
// vue.config.performance = true;
Vue.use(VueRouter);

import App from './App.vue';
import Messages from './components/Messages.vue';

const router = new VueRouter({
	mode: 'history',
	routes: [
		{
			path: '/',
			name: 'app',
			component: Messages
		},
		{
			path: '/bot',
			name: 'bot',
			component: Messages
		}
	]
});

const app = new Vue({
	el: '#app',
	components: { App },
	router
});