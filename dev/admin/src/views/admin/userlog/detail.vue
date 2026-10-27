<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :model-value="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">用户id：</td>
						<td>
							{{form.user_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">技师id：</td>
						<td>
							{{form.technician_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">1用户；2技师：</td>
						<td>
							{{form.user_type}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">订单号：</td>
						<td>
							{{form.order_sn}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">支付金额：</td>
						<td>
							{{form.money}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">1下单；2退款；3会员；4充值；5佣金；6结算；7提现；8提现驳回：</td>
						<td>
							{{form.type}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">1收入；2支出：</td>
						<td>
							{{form.income}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">1余额；2微信；3支付宝：</td>
						<td>
							{{form.payment}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">create_time：</td>
						<td>
							{{form.create_time}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">update_time：</td>
						<td>
							{{form.update_time}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/userlog'
export default {
	name:'Userlogdetail',
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
