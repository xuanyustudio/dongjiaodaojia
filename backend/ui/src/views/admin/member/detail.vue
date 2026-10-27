<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">会员名称：</td>
						<td>
							{{form.name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">原价：</td>
						<td>
							{{form.original_price}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开通价格：</td>
						<td>
							{{form.price}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">月：</td>
						<td>
							{{form.month}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">排序：</td>
						<td>
							{{form.sort}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">状态：</td>
						<td>
							<span v-if="form.status == '0'">关闭</span>
							<span v-if="form.status == '1'">开启</span>
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/member'
export default {
	name:'Memberdetail',
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
