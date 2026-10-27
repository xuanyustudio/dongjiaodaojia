<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :model-value="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">提现单号：</td>
						<td>
							{{form.sn}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">用户id；技师id：</td>
						<td>
							{{form.user_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">真实姓名：</td>
						<td>
							{{form.real_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">账号：</td>
						<td>
							{{form.account}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">类型：</td>
						<td>
							<span v-if="form.type == '4'">支付宝</span>
							<span v-if="form.type == '2'">微信</span>
							<span v-if="form.type == '1'">钱包余额</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">用户类型：</td>
						<td>
							<span v-if="form.user_type == '1'">用户</span>
							<span v-if="form.user_type == '2'">技师</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">提现金额：</td>
						<td>
							{{form.money}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">实际获得金额：</td>
						<td>
							{{form.left_money}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">手续费：</td>
						<td>
							{{form.poundage}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">提现状态：</td>
						<td>
							<span v-if="form.status == '1'">待提现</span>
							<span v-if="form.status == '2'">提现成功</span>
							<span v-if="form.status == '3'">提现失败</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">申请时间：</td>
						<td>
							{{parseTime(form.create_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">技师姓名：</td>
						<td>
							{{form.realname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">用户昵称：</td>
						<td>
							{{form.nickname}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/withdrawapply'
export default {
	name:'Withdrawapplydetail',
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
