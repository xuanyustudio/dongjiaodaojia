<template>
	<div>
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="昵称" prop="nickname">
							<el-input  v-model="form.nickname" autoComplete="off" clearable  placeholder="请输入昵称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户名" prop="username">
							<el-input  v-model="form.username" autoComplete="off" clearable  placeholder="请输入用户名"></el-input>
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
						<el-form-item label="服务类目" prop="service">
							<el-input  v-model="form.service" autoComplete="off" clearable  placeholder="请输入服务类目"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="服务周期" prop="week">
							<el-input  v-model="form.week" autoComplete="off" clearable  placeholder="请输入服务周期"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="服务起始时间" prop="start_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.start_time" clearable placeholder="请输入服务起始时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="服务结束时间" prop="end_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.end_time" clearable placeholder="请输入服务结束时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="性别" prop="type">
							<el-select style="width:100%" v-model="form.sex" filterable clearable placeholder="请选择">
								<el-option key="0" label="男" :value="1"></el-option>
								<el-option key="1" label="女" :value="2"></el-option>
								<el-option key="2" label="保密" :value="0"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="openid" prop="openid">
							<el-input  v-model="form.openid" autoComplete="off" clearable  placeholder="请输入openid"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="最后登录时间" prop="last_login">
							<el-input  v-model="form.last_login" autoComplete="off" clearable  placeholder="请输入最后登录时间"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="登录次数" prop="login_num">
							<el-input  v-model="form.login_num" autoComplete="off" clearable  placeholder="请输入登录次数"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="最后登录ip" prop="last_ip">
							<el-input  v-model="form.last_ip" autoComplete="off" clearable  placeholder="请输入最后登录ip"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="手机号码" prop="mobile">
							<el-input  v-model="form.mobile" autoComplete="off" clearable  placeholder="请输入手机号码"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="头像" prop="avatar">
							<ImagesUpload size="small"   fileType="image" :image.sync="form.avatar"></ImagesUpload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="相册" prop="album">
							<ImagesUpload size="small"  fileType="images" :images.sync="form.album"></ImagesUpload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="状态" prop="type">
							<el-select style="width:100%" v-model="form.status" filterable clearable placeholder="请选择">
								<el-option key="0" label="认证" :value="1"></el-option>
								<el-option key="1" label="未认证" :value="0"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="余额" prop="balance">
							<el-input  v-model="form.balance" autoComplete="off" clearable  placeholder="请输入余额"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="好评" prop="praise">
							<el-input  v-model="form.praise" autoComplete="off" clearable  placeholder="请输入好评"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="信用" prop="credit">
							<el-input  v-model="form.credit" autoComplete="off" clearable  placeholder="请输入信用"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="综合评分" prop="composite">
							<el-input  v-model="form.composite" autoComplete="off" clearable  placeholder="请输入综合评分"></el-input>
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
						<el-form-item label="注册时间" prop="create_time">
							<el-input  v-model="form.create_time" autoComplete="off" clearable  placeholder="请输入注册时间"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="update_time" prop="update_time">
							<el-input  v-model="form.update_time" autoComplete="off" clearable  placeholder="请输入update_time"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="销量" prop="sales">
							<el-input  v-model="form.sales" autoComplete="off" clearable  placeholder="请输入销量"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="工作状态" prop="type">
							<el-select style="width:100%" v-model="form.work_status" filterable clearable placeholder="请选择">
								<el-option key="0" label="工作" :value="1"></el-option>
								<el-option key="1" label="休息" :value="0"></el-option>
								<el-option key="2" label="禁用" :value="2"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="签到地址" prop="address">
							<el-input  v-model="form.address" autoComplete="off" clearable  placeholder="请输入签到地址"></el-input>
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
						<el-form-item label="标签" prop="label">
							<el-input  v-model="form.label" autoComplete="off" clearable  placeholder="请输入标签"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="unionid" prop="unionid">
							<el-input  v-model="form.unionid" autoComplete="off" clearable  placeholder="请输入unionid"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="0未关注公众号" prop="subscribe">
							<el-input  v-model="form.subscribe" autoComplete="off" clearable  placeholder="请输入0未关注公众号"></el-input>
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
import { update  } from '@/api/admin/technician'
import ImagesUpload from '@/components/common/ImagesUpload.vue'
export default {
	name:'technicianupdate',
	components: {
		ImagesUpload,
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
				nickname:'',
				username:'',
				realname:'',
				service:'',
				week:'',
				start_time:'',
				end_time:'',
				openid:'',
				last_login:'',
				login_num:'',
				last_ip:'',
				mobile:'',
				avatar:'',
				album:[],
				balance:'',
				praise:'',
				credit:'',
				composite:'',
				lat:'',
				lng:'',
				create_time:'',
				update_time:'',
				sales:'',
				address:'',
				province:'',
				city:'',
				district:'',
				label:'',
				unionid:'',
				subscribe:'',
			},
			loading:false,
			rules: {
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.setDefaultVal('album')
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
		setDefaultVal(key){
			if(this.form[key] == null || this.form[key] == ''){
				this.form[key] = []
			}
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
