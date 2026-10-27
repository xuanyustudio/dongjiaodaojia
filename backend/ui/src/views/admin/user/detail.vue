<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">推荐人id：</td>
						<td>
							{{form.pid}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">昵称：</td>
						<td>
							{{form.nickname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">用户名：</td>
						<td>
							{{form.username}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">真名：</td>
						<td>
							{{form.realname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">性别：</td>
						<td>
							<span v-if="form.sex == '0'">保密</span>
							<span v-if="form.sex == '1'">男</span>
							<span v-if="form.sex == '2'">女</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">手机号码：</td>
						<td>
							{{form.mobile}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">头像：</td>
						<td>
							<el-image v-if="form.avatar" class="table_list_pic" :src="form.avatar"  :preview-src-list="[form.avatar]"></el-image>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">状态：</td>
						<td>
							<span v-if="form.status == '0'">禁止</span>
							<span v-if="form.status == '1'">正常</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">1推广人：</td>
						<td>
							{{form.is_promoter}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">余额：</td>
						<td>
							{{form.balance}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">vip：</td>
						<td>
							<span v-if="form.is_vip == '0'">非会员</span>
							<span v-if="form.is_vip == '1'">会员</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">注册时间：</td>
						<td>
							{{parseTime(form.create_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">好评率：</td>
						<td>
							{{form.praise}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/user'
export default {
	name:'Userdetail',
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
