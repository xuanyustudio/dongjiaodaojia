<template>
	<div>
		<el-dialog title="修改" width="600px" class="icon-dialog" :model-value="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户姓名" prop="name">
							<el-input  v-model="form.name" autoComplete="off" clearable  placeholder="请输入用户姓名"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="用户名" prop="user">
							<el-input  v-model="form.user" autoComplete="off" clearable  placeholder="请输入用户名"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属角色" prop="type">
							<el-select style="width:100%" v-model="form.role_id" filterable clearable placeholder="请选择">
								<el-option v-for="(item,i) in role_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="备注" prop="note">
							<el-input  v-model="form.note" autoComplete="off" clearable  placeholder="请输入备注"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="状态" prop="status">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.status"></el-switch>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="创建时间" prop="create_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="date" v-model="form.create_time" clearable placeholder="请输入创建时间"></el-date-picker>
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
import { update ,getFieldList } from '@/api/admin/adminuser'
import { parseTime } from '@/utils/common'
export default {
	name:'adminuserupdate',
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
				name:'',
				user:'',
				role_id:'',
				note:'',
				status:1,
				create_time:'',
			},
			role_ids:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				getFieldList().then(res => {
					if(res.status == 200){
						this.role_ids = res.data.role_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.form.create_time = parseTime(this.form.create_time)
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
