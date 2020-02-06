<template>
	<div>
		<table class="table table-responsive">
			<thead>
				<tr>
					<th>S/N</th>
					<th>ID</th>
					<th>Time</th>
					<th>Sender</th>
					<th>Message</th>
					<th>Entities</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="m in messages" :key="m.id">
					<td>{{ m.sn }}</td>
					<td>{{ m.id }}</td>
					<td>{{ m.created_timestamp }}</td>
					<td>{{ m.message_create.sender_id }}</td>
					<td>{{ m.message_create.message_data.text }}</td>
					<td v-html="getEntities(m.message_create.message_data.entities)"></td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				messages: [],
				theApp: this
			}
		},

		methods: {
			getMessages() {
				window.axios.get('/botcheck').then(response => {
					// console.log(JSON.stringify(response));
					// console.log(response);
					console.log(response.data);
					if (response.data.events) {
						this.messages = response.data.events;
					}
					let sn = 1;
					this.messages.map(o => {
						o.sn = sn;
						sn++;
					});
				});
			},

			getEntities(entities) {
				let str = "";
				if (entities.hashtags.length) {
					str += "Hashtags:<br />";
					entities.hashtags.forEach(o => {
						str += `${entities.hashtags.indexOf(o) + 1}. ${o}<br />`;
					});
					str += "<br />";
				}

				if (entities.user_mentions.length) {
					str += "Mentions:<br />";
					entities.user_mentions.forEach(o => {
						str += `${entities.user_mentions.indexOf(o) + 1}. @${o.screen_name}<br />`;
					});
					str += "<br />";
				}

				if (entities.urls.length) {
					str += "URLS:<br />";
					entities.urls.forEach(o => {
						str += `${entities.urls.indexOf(o) + 1}. ${o.expanded_url} (${this.getHandleFromURL(o.expanded_url)})`; 
					});
				}

				return str;
			},

			getHandleFromURL(url) {
				let handle = url.replace('https://twitter.com/', '');
				let ind = handle.indexOf('/');
				return handle.slice(0, ind);
			}
		},

		created() {
			this.getMessages();
		}
		
	}
</script>