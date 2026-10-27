<template>
	<div>
		<el-row :gutter="10">
			<el-col :span="24">
				<el-card shadow="never" class="card">
					<div id="charts_one" style="width:100%;min-height:80vh">
						<Search size='small' :searchVisible="searchVisible" v-model:searchData="searchData"  :searchForm = "searchForm" @refesh_list="index"></Search>
						<div class="btn-group" style="margin-bottom:11px;">
							<div>
								<el-button type="success"  size="small"  @click="add(ids)" v-if="checkPermission('/admin/Uploadconfig/add.html')" ><i class="el-icon-plus"></i>添加</el-button>
								<el-button type="primary" :disabled="single" size="small"  @click="update(ids)" v-if="checkPermission('/admin/Uploadconfig/update.html')" ><i class="el-icon-edit"></i>修改</el-button>
								<el-button type="danger" :disabled="multiple" size="small"  @click="del(ids)" v-if="checkPermission('/admin/Uploadconfig/delete.html')" ><i class="el-icon-delete"></i>删除</el-button>
								<el-button type="info" :disabled="single" size="small"  @click="detail(ids)" v-if="checkPermission('/admin/Uploadconfig/detail.html')" ><i class="el-icon-view"></i>查看详情</el-button>
							</div>
							<table-tool v-model:searchVisible="searchVisible"  :fieldList="fieldList" @refesh_list="index" :searchTool="true"></table-tool>
						</div>
						<el-table :row-class-name="rowClass" @selection-change="selection"  @row-click="handleRowClick"  row-key="id"  :header-cell-style="{ background: '#eef1f6', color: '#606266' }" v-loading="loading"  ref="multipleTable" border class="eltable" :data="list"  style="width: 100%">
							<el-table-column align="center" type="selection" width="42"></el-table-column>
							<el-table-column align="center" type = '' v-if="fieldList[0].checked" property="id" label="编号" show-overflow-tooltip width="70">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[1].checked" property="title" label="配置名称" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[2].checked" property="upload_replace" label="覆盖原图" show-overflow-tooltip width="">
								<template #default="scope">
									<el-switch @change="updateExt(scope.row,'upload_replace')" :active-value="1" :inactive-value="0" v-model="scope.row.upload_replace"></el-switch>
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[3].checked" property="thumb_status" label="生成缩略图" show-overflow-tooltip width="">
								<template #default="scope">
									<el-switch @change="updateExt(scope.row,'thumb_status')" :active-value="1" :inactive-value="0" v-model="scope.row.thumb_status"></el-switch>
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[4].checked" property="thumb_width" label="缩略图宽" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[5].checked" property="thumb_height" label="缩略图高" show-overflow-tooltip width="">
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[6].checked" property="thumb_type" label="缩放类型" show-overflow-tooltip width="">
								<template #default="scope">
									<span v-if="scope.row.thumb_type == '1'" >等比例缩放</span>
									<span v-if="scope.row.thumb_type == '2'" >缩放后填充</span>
									<span v-if="scope.row.thumb_type == '3'" >居中裁剪</span>
									<span v-if="scope.row.thumb_type == '4'" >左上角裁剪</span>
									<span v-if="scope.row.thumb_type == '5'" >右下角裁剪</span>
									<span v-if="scope.row.thumb_type == '6'" >固定尺寸缩放</span>
								</template>
							</el-table-column>
							<el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" label="操作" align="center" width="160">
								<template #default="scope">
									<div v-if="scope.row.id">
										<el-button size="small" type="primary" @click="update(scope.row)"  v-if="checkPermission('/admin/Uploadconfig/update.html')"><i class="el-icon-edit" />修改</el-button>
										<el-button size="small" type="danger" @click="del(scope.row)"  v-if="checkPermission('/admin/Uploadconfig/delete.html')"><i class="el-icon-delete" />删除</el-button>
									</div>
								</template>
							</el-table-column>
						</el-table>
						<Page :total="page_data.total" v-model:page="page_data.page" v-model:limit="page_data.limit" @pagination="index" />
					</div>
				</el-card>
			</el-col>
		</el-row>
		<!--添加-->
		<Add  v-model:show="dialog.addDialogStatus" size="small" @refesh_list="index"></Add>
		<!--修改-->
		<Update :info="updateInfo" v-model:show="dialog.updateDialogStatus" size="small" @refesh_list="index"></Update>
		<!--查看详情-->
		<Detail :info="detailInfo" v-model:show="dialog.detailDialogStatus" size="small" @refesh_list="index"></Detail>
	</div>
</template>
<script>
	import {
		index,
		updateExt,
		getInfo,
		del,
	} from '@/api/admin/uploadconfig'
	import Search from '@/components/common/Search.vue'
	import Page from '@/components/common/Page.vue'
	import TableTool from '@/components/common/TableTool.vue'
	import Add from '@/views/admin/uploadconfig/add.vue'
	import Update from '@/views/admin/uploadconfig/update.vue'
	import Detail from '@/views/admin/uploadconfig/detail.vue'
	import { confirm } from '@/utils/common'
	export default {
		name:'uploadconfig',
		components: {
			Search,
			Page,
			TableTool,
			Add,
			Update,
			Detail,
		},
		data(){
			return {
				dialog: {
					addDialogStatus : false,
					updateDialogStatus : false,
					detailDialogStatus : false,
				},
				fieldList:[
					{lable:'编号',checked:true},
					{lable:'配置名称',checked:true},
					{lable:'覆盖原图',checked:true},
					{lable:'生成缩略图',checked:true},
					{lable:'缩略图宽',checked:true},
					{lable:'缩略图高',checked:true},
					{lable:'缩放类型',checked:true},
				],
				ids: [],
				single:true,
				multiple:true,
				list: [],
				updateInfo:{},
				detailInfo:{},
				loading: false,
				page_data: {
					limit: 20,
					page: 1,
					total:20,
				},
				searchVisible:true,
				searchForm:[],
				searchData:{},
			}
		},
		methods: {
			index(){
				let param = {limit:this.page_data.limit,page:this.page_data.page}
				if(this.searchData) param = Object.assign(param, this.searchData)
				this.loading = true
				index(param).then(res => {
					this.list = res.data.data
					this.page_data.total = res.data.total
					this.loading = false
					if(this.page_data.page == 1){
						this.searchForm = [
							{type:'Select',label:'覆盖原图',prop:'upload_replace',data:[{"key":"开启","val":"1"},{"key":"关闭","val":"0"}],width:'150px'},
							{type:'Select',label:'生成缩略图',prop:'thumb_status',data:[{"key":"开启","val":"1"},{"key":"关闭","val":"0"}],width:'150px'},
							{type:'Select',label:'缩放类型',prop:'thumb_type',data:[{"key":"等比例缩放","val":"1"},{"key":"缩放后填充","val":"2"},{"key":"居中裁剪","val":"3"},{"key":"左上角裁剪","val":"4"},{"key":"右下角裁剪","val":"5"},{"key":"固定尺寸缩放","val":"6"}],width:'150px'},
						]
					}
				})
			},
			updateExt(row,field){
				if(row.id){
					updateExt({id:row.id,[field]:row[field]}).then(res => {
						this.$message({message: '操作成功', type: 'success'})
					})
				}
			},
			add(){
				this.dialog.addDialogStatus = true
			},
			update(row){
				let id = row.id ? row.id : this.ids.join(',')
				getInfo({id:id}).then(res => {
					if(res.status == 200){
						this.dialog.updateDialogStatus = true
						this.updateInfo = res.data
					}
				})
			},
			del(row){
				confirm({content:'确定要操作吗'}).then(() => {
					let ids = row.id ? row.id : this.ids.join(',')
					del({id:ids}).then(res => {
						this.$message({message: res.msg, type: 'success'})
						this.index()
					})
				}).catch(() => {})
			},
			detail(){
				this.dialog.detailDialogStatus = true
				this.detailInfo = {id:this.ids.join(',')}
			},
			selection(selection) {
				this.ids = selection.map(item => item.id)
				this.single = selection.length != 1
				this.multiple = !selection.length
			},
			handleRowClick(row, rowIndex,event){
				if(event.target.className !== 'el-input__inner'){
					this.$refs.multipleTable.toggleRowSelection(row)
				}
			},
			rowClass ({ row, rowIndex }) {
				for(let i=0;i<this.ids.length;i++) {
					if (row.id === this.ids[i]) {
						return 'rowLight'
					}
				}
			},
		},
		mounted(){
			this.index()
		}
	}
</script>
<style  lang="scss">
@import '@/assets/scss/common.scss'
</style>