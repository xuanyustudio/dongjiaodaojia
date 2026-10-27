<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :model-value="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">用户id：</td>
						<td>
							{{form.technician_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">头像：</td>
						<td>
							{{form.realname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">相册：</td>
						<td>
							{{form.mobile}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">注册时间：</td>
						<td>
							{{form.create_time}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/technicianali'
export default {
	name:'Technicianalidetail',
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
			form:{
			},
		}
	},
	methods: {
		open(){
			detail(this.info).then(res => {
				this.form = res.data
			})
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
