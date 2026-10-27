<template>
	<div>
		<el-dialog title="跳转链接" width="" @open="open" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<iframe :src="jump" frameborder="0" width="100%" height="600px"></iframe>
		</el-dialog>
	</div>
</template>
<script>
import { param } from '@/utils/common'
export default {
	name:'RolejumpaUrl',
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		info: {
			type: Object,
		},
	},
	data() {
		return {
			jump:''
		}
	},
	methods: {
		open(){
			let query = {}
			Object.assign(query, {dialog_url:1})
			Object.assign(query, {role_id:this.info.role_id})
			this.jump = 'http://127.0.0.1:3333/#/admin/Adminuser/index.html?' + param(query)
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
}
</script>
<style  lang="scss">
@import '@/assets/scss/common.scss'
</style>
