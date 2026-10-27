<template>
	<div>
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="原优惠券id" prop="primary_id">
							<el-input  v-model="form.primary_id" autoComplete="off" clearable  placeholder="请输入原优惠券id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="名称" prop="name">
							<el-input  v-model="form.name" autoComplete="off" clearable  placeholder="请输入名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="兑换码" prop="exchange">
							<el-input  v-model="form.exchange" autoComplete="off" clearable  placeholder="请输入兑换码"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="优惠余额" prop="money">
							<el-input  v-model="form.money" autoComplete="off" clearable  placeholder="请输入优惠余额"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="折扣" prop="discount">
							<el-input  v-model="form.discount" autoComplete="off" clearable  placeholder="请输入折扣"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="条件" prop="condition">
							<el-input  v-model="form.condition" autoComplete="off" clearable  placeholder="请输入条件"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="类型" prop="type">
							<el-select style="width:100%" v-model="form.type" filterable clearable placeholder="请选择">
								<el-option key="0" label="满减券" :value="0"></el-option>
								<el-option key="1" label="折扣券" :value="1"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="领取方式" prop="type">
							<el-select style="width:100%" v-model="form.get_type" filterable clearable placeholder="请选择">
								<el-option key="0" label="直接领取" :value="0"></el-option>
								<el-option key="1" label="赠送" :value="1"></el-option>
								<el-option key="2" label="兑换" :value="2"></el-option>
							</el-select>
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
						<el-form-item label="服务id" prop="service_id">
							<el-input  v-model="form.service_id" autoComplete="off" clearable  placeholder="请输入服务id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="领取时间" prop="create_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.create_time" clearable placeholder="请输入领取时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始使用时间" prop="start_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.start_time" clearable placeholder="请输入开始使用时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="结束使用时间" prop="end_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.end_time" clearable placeholder="请输入结束使用时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="0隐藏；1正常" prop="status">
							<el-input  v-model="form.status" autoComplete="off" clearable  placeholder="请输入0隐藏；1正常"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="是否使用" prop="type">
							<el-select style="width:100%" v-model="form.use" filterable clearable placeholder="请选择">
								<el-option key="0" label="未使用" :value="0"></el-option>
								<el-option key="1" label="使用" :value="1"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
	</div>
</template>
<script>
import { update  } from '@/api/admin/couponlog'
import { parseTime } from '@/utils/common'
export default {
	name:'couponlogupdate',
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
				primary_id:'',
				name:'',
				exchange:'',
				money:'',
				discount:'',
				condition:'',
				user_id:'',
				service_id:'',
				create_time:'',
				start_time:'',
				end_time:'',
				status:'',
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
			this.form.start_time = parseTime(this.form.start_time)
			this.form.end_time = parseTime(this.form.end_time)
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
