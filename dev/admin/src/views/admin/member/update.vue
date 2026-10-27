<template>
	<div>
		<el-dialog title="修改" width="600px" class="icon-dialog" :model-value="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="会员名称" prop="name">
							<el-input  v-model="form.name" autoComplete="off" clearable  placeholder="请输入会员名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="原价" prop="original_price">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.original_price" clearable :min="0" placeholder="原价"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="优惠价" prop="price">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.price" clearable :min="0" placeholder="优惠价"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="有效期（月）" prop="month">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.month" clearable :min="0" placeholder="有效期（月）"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="排序" prop="sort">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.sort" clearable :min="0" placeholder="排序"/>
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
import { update  } from '@/api/admin/member'
export default {
	name:'memberupdate',
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
				status:1,
				update_time:'',
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
