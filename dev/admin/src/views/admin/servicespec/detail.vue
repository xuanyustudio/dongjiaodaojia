<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :model-value="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">名称：</td>
						<td>
							{{form.name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">价格：</td>
						<td>
							{{form.price}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">时长：</td>
						<td>
							{{form.duration}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">排序：</td>
						<td>
							{{form.sort}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">服务类型：</td>
						<td>
							{{form.name}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/servicespec'
export default {
	name:'Servicespecdetail',
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
