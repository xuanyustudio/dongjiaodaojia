<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">正面：</td>
						<td>
							{{form.front}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">反面：</td>
						<td>
							{{form.back}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">用户id：</td>
						<td>
							{{form.technician_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">0未认证；1认证;2拒绝：</td>
						<td>
							{{form.status}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">注册时间：</td>
						<td>
							{{form.create_time}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">姓名：</td>
						<td>
							{{form.realname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">身份证号码：</td>
						<td>
							{{form.cardsn}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/technicianidentity'
export default {
	name:'Technicianidentitydetail',
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
