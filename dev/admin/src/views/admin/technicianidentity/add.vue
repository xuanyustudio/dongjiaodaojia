<template>
	<div>
		<el-dialog title="添加" width="600px" class="icon-dialog" :model-value="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="正面" prop="front">
							<el-input  v-model="form.front" autoComplete="off" clearable  placeholder="请输入正面"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="反面" prop="back">
							<el-input  v-model="form.back" autoComplete="off" clearable  placeholder="请输入反面"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户id" prop="technician_id">
							<el-input  v-model="form.technician_id" autoComplete="off" clearable  placeholder="请输入用户id"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="0未认证；1认证;2拒绝" prop="status">
							<el-input  v-model="form.status" autoComplete="off" clearable  placeholder="请输入0未认证；1认证;2拒绝"></el-input>
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
						<el-form-item label="姓名" prop="realname">
							<el-input  v-model="form.realname" autoComplete="off" clearable  placeholder="请输入姓名"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="身份证号码" prop="cardsn">
							<el-input  v-model="form.cardsn" autoComplete="off" clearable  placeholder="请输入身份证号码"></el-input>
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
import { add  } from '@/api/admin/technicianidentity'
export default {
	name:'technicianidentityadd',
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
	},
	data(){
		return {
			form: {
				front:'',
				back:'',
				technician_id:'',
				status:'',
				create_time:'',
				realname:'',
				cardsn:'',
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
