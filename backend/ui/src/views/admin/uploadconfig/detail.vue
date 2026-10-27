<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">配置名称：</td>
						<td>
							{{form.title}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">覆盖原图：</td>
						<td>
							<span v-if="form.upload_replace == '1'">开启</span>
							<span v-if="form.upload_replace == '0'">关闭</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">生成缩略图：</td>
						<td>
							<span v-if="form.thumb_status == '1'">开启</span>
							<span v-if="form.thumb_status == '0'">关闭</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">缩略图宽：</td>
						<td>
							{{form.thumb_width}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">缩略图高：</td>
						<td>
							{{form.thumb_height}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">缩放类型：</td>
						<td>
							<span v-if="form.thumb_type == '1'">等比例缩放</span>
							<span v-if="form.thumb_type == '2'">缩放后填充</span>
							<span v-if="form.thumb_type == '3'">居中裁剪</span>
							<span v-if="form.thumb_type == '4'">左上角裁剪</span>
							<span v-if="form.thumb_type == '5'">右下角裁剪</span>
							<span v-if="form.thumb_type == '6'">固定尺寸缩放</span>
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/uploadconfig'
export default {
	name:'Uploadconfigdetail',
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		info: {
			type: Object,
		},
	},
	data() {
		return {
			form:{
			},
		}
	},
	methods: {
		open(){
			detail(this.info).then(res => {
				this.form = res.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
}
</script>
<style  lang="scss">
@import '@/assets/scss/common.scss'
</style>
