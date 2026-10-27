<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">内容：</td>
						<td>
							{{form.content}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">手机：</td>
						<td>
							{{form.mobile}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">图片：</td>
						<td>
						<div v-if="form.images && form.images.indexOf('[{') != -1" class="demo-image__preview">
							<el-image style="margin-right:5px" v-for="(item,i) in JSON.parse(form.images)"  class="table_list_pic" :src="item.url" :key="i"  :preview-src-list="[item.url]"></el-image>
						</div>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">用户类型：</td>
						<td>
							<span v-if="form.user_type == '1'">用户</span>
							<span v-if="form.user_type == '2'">技师</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">类型：</td>
						<td>
							<span v-if="form.type == '1'">bug</span>
							<span v-if="form.type == '2'">意见</span>
							<span v-if="form.type == '3'">吐槽</span>
							<span v-if="form.type == '4'">其他</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">时间：</td>
						<td>
							{{parseTime(form.create_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">用户昵称：</td>
						<td>
							{{form.nickname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">技师姓名：</td>
						<td>
							{{form.realname}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
import { detail } from '@/api/admin/opinion'
export default {
	name:'Opiniondetail',
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
