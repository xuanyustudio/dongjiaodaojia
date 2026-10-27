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
						<td class="title" width="100">封面：</td>
						<td>
							<el-image v-if="form.cover" class="table_list_pic" :src="form.cover"  :preview-src-list="[form.cover]"></el-image>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">销量：</td>
						<td>
							{{form.sales}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">排序：</td>
						<td>
							{{form.sort}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">评分：</td>
						<td>
							{{form.praise}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">是否显示：</td>
						<td>
							<span v-if="form.show == '1'">显示</span>
							<span v-if="form.show == '0'">隐藏</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">原价：</td>
						<td>
							{{form.original_price}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">现价：</td>
						<td>
							{{form.price}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">折扣：</td>
						<td>
							{{form.discount}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">时长（分）：</td>
						<td>
							{{form.duration}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/service'
export default {
	name:'Servicedetail',
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
