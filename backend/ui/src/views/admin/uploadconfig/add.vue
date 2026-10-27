<template>
	<div>
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="配置名称" prop="title">
							<el-input  v-model="form.title" autoComplete="off" clearable  placeholder="请输入配置名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="覆盖原图" prop="upload_replace">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.upload_replace"></el-switch>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="生成缩略图" prop="thumb_status">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.thumb_status"></el-switch>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="缩略图宽" prop="thumb_width">
							<el-input  v-model="form.thumb_width" autoComplete="off" clearable  placeholder="请输入缩略图宽"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="缩略图高" prop="thumb_height">
							<el-input  v-model="form.thumb_height" autoComplete="off" clearable  placeholder="请输入缩略图高"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="缩放类型" prop="type">
							<el-select style="width:100%" v-model="form.thumb_type" filterable clearable placeholder="请选择">
								<el-option key="0" label="等比例缩放" :value="1"></el-option>
								<el-option key="1" label="缩放后填充" :value="2"></el-option>
								<el-option key="2" label="居中裁剪" :value="3"></el-option>
								<el-option key="3" label="左上角裁剪" :value="4"></el-option>
								<el-option key="4" label="右下角裁剪" :value="5"></el-option>
								<el-option key="5" label="固定尺寸缩放" :value="6"></el-option>
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
import { add  } from '@/api/admin/uploadconfig'
export default {
	name:'uploadconfigadd',
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
				upload_replace:1,
				thumb_status:1,
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
