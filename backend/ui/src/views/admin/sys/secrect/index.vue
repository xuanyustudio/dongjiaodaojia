<template>
	<div>
		<el-card shadow="never" class="card">
			<el-form size="small" ref="form" :model="form" :rules="rules" label-width="90px" >
				<el-row >
					<el-col :span="24">
						<el-form-item label="appid" prop="appid">
							<el-input  v-model="form.appid" autoComplete="off" clearable  placeholder="请输入appid"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="secrect" prop="secrect">
							<el-input  v-model="form.secrect" autoComplete="off" clearable  placeholder="请输入secrect"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-form-item>
					<el-button size="small" type="primary" @click="submit">保存设置</el-button>
				</el-form-item>
			</el-form>
		</el-card>
	</div>
</template>
<script>
import { index  ,getInfo} from '../sys'
export default {
	name:'sys_secrectindex',
	components: {
	},
	data(){
		return {
			form: {
			},
			loading:false,
			rules: {
				appid:[
					{required: true, message: 'appid不能为空', trigger: 'blur'},
				],
				secrect:[
					{required: true, message: 'secrect不能为空', trigger: 'blur'},
				],
			}
		}
	},
	mounted(){
		getInfo().then(res => {
			if(res.status == 200){
				this.form = JSON.stringify(res.data) == '[]' ? {} : res.data
			}
		})
	},
	methods: {
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					index(this.form).then(res => {
						if(res.status == 200){
							this.$message({message: '操作成功', type: 'success'})
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
	}
}
</script>
<style scoped lang="scss">
@import '@/assets/scss/common.scss'
</style>
