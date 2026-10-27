<template>
	<div>
		<el-dialog title="添加" width="600px" class="icon-dialog" :model-value="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="推荐人id" prop="pid">
							<el-input  v-model="form.pid" autoComplete="off" clearable  placeholder="请输入推荐人id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="二级推荐人id" prop="spid">
							<el-input  v-model="form.spid" autoComplete="off" clearable  placeholder="请输入二级推荐人id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="邮件" prop="email">
							<el-input  v-model="form.email" autoComplete="off" clearable  placeholder="请输入邮件"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
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
						<el-form-item label="真名" prop="realname">
							<el-input  v-model="form.realname" autoComplete="off" clearable  placeholder="请输入真名"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="性别" prop="type">
							<el-select style="width:100%" v-model="form.sex" filterable clearable placeholder="请选择">
								<el-option key="0" label="保密" :value="0"></el-option>
								<el-option key="1" label="男" :value="1"></el-option>
								<el-option key="2" label="女" :value="2"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="密码" prop="password">
							<el-input  v-model="form.password" autoComplete="off" clearable  placeholder="请输入密码"></el-input>
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
						<el-form-item label="QQ" prop="qq">
							<el-input  v-model="form.qq" autoComplete="off" clearable  placeholder="请输入QQ"></el-input>
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
							<ImagesUpload size="small"   fileType="image" v-model:image="form.avatar"></ImagesUpload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="状态" prop="status">
							<el-input  v-model="form.status" autoComplete="off" clearable  placeholder="请输入状态"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="1推广人" prop="is_promoter">
							<el-input  v-model="form.is_promoter" autoComplete="off" clearable  placeholder="请输入1推广人"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="余额" prop="balance">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.balance" clearable :min="0" placeholder="余额"/>
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
						<el-form-item label="推广码" prop="promo_code">
							<el-input  v-model="form.promo_code" autoComplete="off" clearable  placeholder="请输入推广码"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="qrcode" prop="qrcode">
							<el-input  v-model="form.qrcode" autoComplete="off" clearable  placeholder="请输入qrcode"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="token" prop="token">
							<el-input  v-model="form.token" autoComplete="off" clearable  placeholder="请输入token"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="vip" prop="type">
							<el-select style="width:100%" v-model="form.is_vip" filterable clearable placeholder="请选择">
								<el-option key="0" label="非会员" :value="0"></el-option>
								<el-option key="1" label="会员" :value="1"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="会员到期时间" prop="overdue_time">
							<el-input  v-model="form.overdue_time" autoComplete="off" clearable  placeholder="请输入会员到期时间"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="注册时间" prop="create_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.create_time" clearable placeholder="请输入注册时间"></el-date-picker>
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
						<el-form-item label="delete_time" prop="delete_time">
							<el-input  v-model="form.delete_time" autoComplete="off" clearable  placeholder="请输入delete_time"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="apply_time" prop="apply_time">
							<el-input  v-model="form.apply_time" autoComplete="off" clearable  placeholder="请输入apply_time"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="好评率" prop="praise">
							<el-input  v-model="form.praise" autoComplete="off" clearable  placeholder="请输入好评率"></el-input>
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
import { add  } from '@/api/admin/user'
import ImagesUpload from '@/components/common/ImagesUpload.vue'
export default {
	name:'useradd',
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
	},
	data(){
		return {
			form: {
				pid:'',
				spid:'',
				email:'',
				nickname:'',
				username:'',
				realname:'',
				password:'',
				last_login:'',
				login_num:'',
				last_ip:'',
				qq:'',
				mobile:'',
				avatar:'',
				status:'',
				is_promoter:'',
				brokerage:'',
				promo_code:'',
				qrcode:'',
				token:'',
				overdue_time:'',
				create_time:'',
				update_time:'',
				delete_time:'',
				apply_time:'',
				praise:'',
			},
			loading:false,
			rules: {
			}
		}
	},
	methods: {
		open(){
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					add(this.form).then(res => {
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
