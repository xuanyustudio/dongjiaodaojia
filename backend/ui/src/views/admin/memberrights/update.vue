<template>
	<div>
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="权益名称" prop="name">
							<el-input  v-model="form.name" autoComplete="off" clearable  placeholder="请输入权益名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="图标" prop="icon">
							<ImagesUpload size="small"   fileType="image" :image.sync="form.icon"></ImagesUpload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="简介" prop="profile">
							<el-input  type="textarea" autoComplete="off" v-model="form.profile"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入简介"/>
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
						<el-form-item label="类型" prop="type">
							<el-radio-group v-model="form.type">
								<el-radio :label="1">优惠券</el-radio>
								<el-radio :label="2">折扣</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.type==2">
					<el-col :span="24">
						<el-form-item label="折扣" prop="value">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.value" clearable :min="0" placeholder="折扣"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.type==1">
					<el-col :span="24">
						<el-form-item label="优惠券ID" prop="value">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.value" clearable :min="0" placeholder="优惠券ID"/>
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
import { update  } from '@/api/admin/memberrights'
import ImagesUpload from '@/components/common/ImagesUpload.vue'
export default {
	name:'memberrightsupdate',
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
				name:'',
				icon:'',
				profile:'',
				status:1,
				type:1,
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
