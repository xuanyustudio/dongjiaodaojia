<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">类型：</td>
						<td>
							<span v-if="form.type == '0'">用户端</span>
							<span v-if="form.type == '1'">技术端</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">图片：</td>
						<td>
						<div v-if="form.image && form.image.indexOf('[{') != -1" class="demo-image__preview">
							<el-image style="margin-right:5px" v-for="(item,i) in JSON.parse(form.image)"  class="table_list_pic" :src="item.url" :key="i"  :preview-src-list="[item.url]"></el-image>
						</div>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">排序：</td>
						<td>
							{{form.sort}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">创建时间：</td>
						<td>
							{{parseTime(form.create_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">更新时间：</td>
						<td>
							{{parseTime(form.update_time)}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/banner'
export default {
	name:'Bannerdetail',
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
