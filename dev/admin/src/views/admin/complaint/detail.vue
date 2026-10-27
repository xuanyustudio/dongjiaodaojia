<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :model-value="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">订单id：</td>
						<td>
							{{form.order_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">技师id：</td>
						<td>
							{{form.technician_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">用户id：</td>
						<td>
							{{form.user_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">1用户；2技师：</td>
						<td>
							{{form.user_type}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">内容：</td>
						<td>
							{{form.content}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">图片：</td>
						<td>
							{{form.images}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">时间：</td>
						<td>
							{{form.create_time}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">audio：</td>
						<td>
							{{form.audio}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/complaint'
export default {
	name:'Complaintdetail',
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
