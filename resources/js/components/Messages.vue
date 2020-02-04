<template>
	
</template>

<script>
	export default {
		data() {

		},

		methods: {
			getMessages() {
				window.axios.get('/botmessages').then(response => {
					console.log(response);
				})
			}
		},

		created() {
			this.getMessages();
		}
		
	}
</script>