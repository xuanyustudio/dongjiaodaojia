<template>
	<div>
		<el-dialog title="修改" width="600px" class="icon-dialog" :model-value="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="订单号" prop="order_sn">
							<el-input  v-model="form.order_sn" autoComplete="off" clearable  placeholder="请输入订单号"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="支付金额" prop="pay_money">
							<el-input  v-model="form.pay_money" autoComplete="off" clearable  placeholder="请输入支付金额"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="总金额" prop="total_money">
							<el-input  v-model="form.total_money" autoComplete="off" clearable  placeholder="请输入总金额"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="优惠金额" prop="discount">
							<el-input  v-model="form.discount" autoComplete="off" clearable  placeholder="请输入优惠金额"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="技师收入" prop="technician_money">
							<el-input  v-model="form.technician_money" autoComplete="off" clearable  placeholder="请输入技师收入"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="订单状态" prop="type">
							<el-select style="width:100%" v-model="form.order_status" filterable clearable placeholder="请选择">
								<el-option key="0" label="待付款" :value="0"></el-option>
								<el-option key="1" label="待接单" :value="1"></el-option>
								<el-option key="2" label="服务中" :value="2"></el-option>
								<el-option key="3" label="订单完成" :value="3"></el-option>
								<el-option key="4" label="已评价" :value="4"></el-option>
								<el-option key="5" label="退款" :value="5"></el-option>
								<el-option key="6" label="取消" :value="6"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="0待付款；1支付；2退款" prop="pay_status">
							<el-input  v-model="form.pay_status" autoComplete="off" clearable  placeholder="请输入0待付款；1支付；2退款"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户id" prop="user_id">
							<el-input  v-model="form.user_id" autoComplete="off" clearable  placeholder="请输入用户id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="技师id" prop="technician_id">
							<el-input  v-model="form.technician_id" autoComplete="off" clearable  placeholder="请输入技师id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="服务id" prop="service_id">
							<el-input  v-model="form.service_id" autoComplete="off" clearable  placeholder="请输入服务id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="规格id" prop="spec_id">
							<el-input  v-model="form.spec_id" autoComplete="off" clearable  placeholder="请输入规格id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="优惠券id" prop="coupon_id">
							<el-input  v-model="form.coupon_id" autoComplete="off" clearable  placeholder="请输入优惠券id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="姓名" prop="realname">
							<el-input  v-model="form.realname" autoComplete="off" clearable  placeholder="请输入姓名"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="电话" prop="mobile">
							<el-input  v-model="form.mobile" autoComplete="off" clearable  placeholder="请输入电话"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="省" prop="province">
							<el-input  v-model="form.province" autoComplete="off" clearable  placeholder="请输入省"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="市" prop="city">
							<el-input  v-model="form.city" autoComplete="off" clearable  placeholder="请输入市"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="区" prop="district">
							<el-input  v-model="form.district" autoComplete="off" clearable  placeholder="请输入区"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="详细地址" prop="address">
							<el-input  v-model="form.address" autoComplete="off" clearable  placeholder="请输入详细地址"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="创建时间" prop="create_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.create_time" clearable placeholder="请输入创建时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="预约时间" prop="appointment_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.appointment_time" clearable placeholder="请输入预约时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="时长" prop="duration">
							<el-input  v-model="form.duration" autoComplete="off" clearable  placeholder="请输入时长"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="车费" prop="fare">
							<el-input  v-model="form.fare" autoComplete="off" clearable  placeholder="请输入车费"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="退款金额" prop="refund">
							<el-input  v-model="form.refund" autoComplete="off" clearable  placeholder="请输入退款金额"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="支付方式" prop="type">
							<el-select style="width:100%" v-model="form.payment" filterable clearable placeholder="请选择">
								<el-option key="0" label="余额" :value="1"></el-option>
								<el-option key="1" label="微信" :value="2"></el-option>
								<el-option key="2" label="支付宝" :value="3"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="佣金" prop="brokerage">
							<el-input  v-model="form.brokerage" autoComplete="off" clearable  placeholder="请输入佣金"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="服务完成时间" prop="complete_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.complete_time" clearable placeholder="请输入服务完成时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="取消类型" prop="type">
							<el-select style="width:100%" v-model="form.cancel_type" filterable clearable placeholder="请选择">
								<el-option key="0" label="用户取消" :value="0"></el-option>
								<el-option key="1" label="技师取消" :value="1"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="纬度" prop="lat">
							<el-input  v-model="form.lat" autoComplete="off" clearable  placeholder="请输入纬度"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="经度" prop="lng">
							<el-input  v-model="form.lng" autoComplete="off" clearable  placeholder="请输入经度"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="取消原因" prop="cancel_reason">
							<el-input  v-model="form.cancel_reason" autoComplete="off" clearable  placeholder="请输入取消原因"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="接单时间" prop="receiving_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.receiving_time" clearable placeholder="请输入接单时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始服务时间" prop="service_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.service_time" clearable placeholder="请输入开始服务时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<template #footer><div class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div></template>
		</el-dialog>
	</div>
</template>
<script>
import { update  } from '@/api/admin/order'
import { parseTime } from '@/utils/common'
export default {
	name:'orderupdate',
	components: {
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
				order_sn:'',
				pay_money:'',
				total_money:'',
				discount:'',
				technician_money:'',
				pay_status:'',
				user_id:'',
				technician_id:'',
				service_id:'',
				spec_id:'',
				coupon_id:'',
				realname:'',
				mobile:'',
				province:'',
				city:'',
				district:'',
				address:'',
				create_time:'',
				appointment_time:'',
				duration:'',
				fare:'',
				refund:'',
				brokerage:'',
				complete_time:'',
				lat:'',
				lng:'',
				cancel_reason:'',
				receiving_time:'',
				service_time:'',
			},
			loading:false,
			rules: {
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.form.create_time = parseTime(this.form.create_time)
			this.form.appointment_time = parseTime(this.form.appointment_time)
			this.form.complete_time = parseTime(this.form.complete_time)
			this.form.receiving_time = parseTime(this.form.receiving_time)
			this.form.service_time = parseTime(this.form.service_time)
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					update(this.form).then(res => {
						if(res.status == 200){
							this.$message({message: '操作成功', type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
}
</script>
<style scoped lang="scss">
@import '@/assets/scss/common.scss'
</style>
