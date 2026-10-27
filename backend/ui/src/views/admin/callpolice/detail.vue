<template>
	<div>
		<el-dialog title="查看详情" width="600px" @open="open" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">纬度：</td>
						<td>
							{{form.lat}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">经度：</td>
						<td>
							{{form.lng}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">地址：</td>
						<td>
							{{form.address}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">时间：</td>
						<td>
							{{parseTime(form.create_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">录音文件：</td>
						<td>
							{{form.audio}}
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
import { detail } from '@/api/admin/callpolice'
export default {
	name:'Callpolicedetail',
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
