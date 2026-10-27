<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">优惠券名称：</td>
						<td>
							{{form.name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">兑换码：</td>
						<td>
							{{form.exchange}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">类型：</td>
						<td>
							<span v-if="form.type == '0'">满减券</span>
							<span v-if="form.type == '1'">折扣券</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">优惠金额：</td>
						<td>
							{{form.money}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">折扣：</td>
						<td>
							{{form.discount}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">使用门槛：</td>
						<td>
							{{form.condition}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">用券开始时间：</td>
						<td>
							{{parseTime(form.start_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">截至时间：</td>
						<td>
							{{parseTime(form.end_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">状态：</td>
						<td>
							<span v-if="form.status == '1'">正常</span>
							<span v-if="form.status == '0'">禁用</span>
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/coupon'
export default {
	name:'Coupondetail',
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
