<template>
	<div>
		<el-dialog title="添加" width="600px" class="icon-dialog" :model-value="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="姓名" prop="realname">
							<el-input  v-model="form.realname" autoComplete="off" clearable  placeholder="请输入姓名"></el-input>
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
						<el-form-item label="手机号码" prop="mobile">
							<el-input  v-model="form.mobile" autoComplete="off" clearable  placeholder="请输入手机号码"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="年龄" prop="age">
							<el-input  v-model="form.age" autoComplete="off" clearable  placeholder="请输入年龄"></el-input>
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
						<el-form-item label="区域" prop="city">
							<el-input  v-model="form.city" autoComplete="off" clearable  placeholder="请输入区域"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户ID" prop="user_id">
							<el-input  v-model="form.user_id" autoComplete="off" clearable  placeholder="请输入用户ID"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="状态" prop="type">
							<el-select style="width:100%" v-model="form.status" filterable clearable placeholder="请选择">
								<el-option key="0" label="已入驻" :value="1"></el-option>
								<el-option key="1" label="申请中" :value="0"></el-option>
							</el-select>
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
import { add  } from '@/api/admin/technicianapply'
import ImagesUpload from '@/components/common/ImagesUpload.vue'
export default {
	name:'technicianapplyadd',
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
				realname:'',
				mobile:'',
				age:'',
				avatar:'',
				create_time:'',
				update_time:'',
				city:'',
				user_id:'',
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
