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
						<td class="title" width="100">优惠余额：</td>
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
						<td class="title" width="100">类型：</td>
						<td>
							<span v-if="form.type == '0'">满减券</span>
							<span v-if="form.type == '1'">折扣券</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">领取方式：</td>
						<td>
							<span v-if="form.get_type == '0'">直接领取</span>
							<span v-if="form.get_type == '1'">赠送</span>
							<span v-if="form.get_type == '2'">兑换</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">领取时间：</td>
						<td>
							{{parseTime(form.create_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开始使用时间：</td>
						<td>
							{{parseTime(form.start_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结束使用时间：</td>
						<td>
							{{parseTime(form.end_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">是否使用：</td>
						<td>
							<span v-if="form.use == '0'">未使用</span>
							<span v-if="form.use == '1'">使用</span>
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
import { detail } from '@/api/admin/couponlog'
export default {
	name:'Couponlogdetail',
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
