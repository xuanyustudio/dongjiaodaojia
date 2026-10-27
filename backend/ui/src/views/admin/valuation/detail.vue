<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">名称：</td>
						<td>
							{{form.name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">起步价：</td>
						<td>
							{{form.starting_price}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">每公里均价：</td>
						<td>
							{{form.average_price}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">时间：</td>
						<td>
							{{form.create_time}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">起步公里：</td>
						<td>
							{{form.kilometre}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/valuation'
export default {
	name:'Valuationdetail',
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
