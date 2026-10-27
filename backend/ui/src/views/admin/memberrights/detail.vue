<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">权益名称：</td>
						<td>
							{{form.name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">图标：</td>
						<td>
							<el-image v-if="form.icon" class="table_list_pic" :src="form.icon"  :preview-src-list="[form.icon]"></el-image>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">简介：</td>
						<td>
							{{form.profile}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">状态：</td>
						<td>
							<span v-if="form.status == '1'">正常</span>
							<span v-if="form.status == '0'">禁用</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">类型：</td>
						<td>
							<span v-if="form.type == '1'">优惠券</span>
							<span v-if="form.type == '2'">折扣</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">折扣：</td>
						<td>
							{{form.value}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">优惠券ID：</td>
						<td>
							{{form.value}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/memberrights'
export default {
	name:'Memberrightsdetail',
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
