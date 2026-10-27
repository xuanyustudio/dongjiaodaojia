<template>
	<div>
		<el-dialog title="修改" width="600px" class="icon-dialog" :model-value="show" @open="open" :before-close="closeForm" append-to-body>
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
						<el-form-item label="状态" prop="type">
							<el-select style="width:100%" v-model="form.status" filterable clearable placeholder="请选择">
								<el-option key="0" label="禁止" :value="0"></el-option>
								<el-option key="1" label="正常" :value="1"></el-option>
							</el-select>
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
import { update  } from '@/api/admin/user'
import ImagesUpload from '@/components/common/ImagesUpload.vue'
export default {
	name:'userupdate',
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
				mobile:'',
				avatar:'',
				praise:'',
			},
			loading:false,
			rules: {
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
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
