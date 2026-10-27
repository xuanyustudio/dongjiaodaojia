<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :model-value="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">订单号：</td>
						<td>
							{{form.order_sn}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">用户id：</td>
						<td>
							{{form.user_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">支付金额：</td>
						<td>
							{{form.pay_money}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">支付状态：</td>
						<td>
							<span v-if="form.pay_status == '0'">待支付</span>
							<span v-if="form.pay_status == '1'">支付</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">会员名称：</td>
						<td>
							{{form.member_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开通时间长（月）：</td>
						<td>
							{{form.month}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开通时间：</td>
						<td>
							{{parseTime(form.create_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">支付方式：</td>
						<td>
							<span v-if="form.payment == '1'">余额</span>
							<span v-if="form.payment == '2'">微信</span>
							<span v-if="form.payment == '3'">支付宝</span>
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/memberorder'
export default {
	name:'Memberorderdetail',
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
