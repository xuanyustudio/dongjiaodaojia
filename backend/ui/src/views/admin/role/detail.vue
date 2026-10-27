<template>
	<div>
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" :before-close="closeForm" append-to-body>
			<table class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:50px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">角色名称：</td>
						<td>
							{{info.name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">状态：</td>
						<td>
							<span v-if="info.status == '1'">正常</span>
							<span v-if="info.status == '0'">禁用</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">描述：</td>
						<td>
							{{info.description}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	</div>
</template>
<script>
	export default {
		name:'Roledetail',
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
			}
		},
		methods: {
			closeForm(){
				this.$emit('update:show', false)
			}
		}
	}
</script>
<style  lang="scss">
@import '@/assets/scss/common.scss'
</style>
