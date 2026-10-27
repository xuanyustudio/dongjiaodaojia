<template>
	<div>
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="$store.getters.device !== 'mobile'?'16%':'90px'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="类型" prop="type">
							<el-radio-group v-model="form.type">
								<el-radio :label="0">用户端</el-radio>
								<el-radio :label="1">技术端</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="图片" prop="image">
							<ImagesUpload size="small"   fileType="image" :image.sync="form.image"></ImagesUpload>
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
						<el-form-item label="创建时间" prop="create_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.create_time" clearable placeholder="请输入创建时间"></el-date-picker>
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
import { update  } from '@/api/admin/banner'
import { parseTime } from '@/utils/common'
import ImagesUpload from '@/components/common/ImagesUpload.vue'
export default {
	name:'bannerupdate',
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
				type:0,
				image:'',
				create_time:'',
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
