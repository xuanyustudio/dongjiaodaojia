<template>
	<div>
		<el-row :gutter="10">
			<el-col :span="24">
				<el-card shadow="never" class="card">
					<div id="charts_one" style="width:100%;min-height:80vh">
						<Search size='small' :searchVisible="searchVisible" :searchData.sync="searchData"  :searchForm = "searchForm" @refesh_list="index"></Search>
						<div class="btn-group" style="margin-bottom:11px;">
							<div>
								<el-button type="success"  size="mini" icon="el-icon-plus"  @click="add(ids)" v-if="checkPermission('/admin/Banner/add.html')" >添加</el-button>
								<el-button type="primary" :disabled="single" size="mini" icon="el-icon-edit"  @click="update(ids)" v-if="checkPermission('/admin/Banner/update.html')" >修改</el-button>
								<el-button type="danger" :disabled="multiple" size="mini" icon="el-icon-delete"  @click="del(ids)" v-if="checkPermission('/admin/Banner/delete.html')" >删除</el-button>
								<el-button type="warning"  size="mini" icon="el-icon-upload"  @click="importData(ids)" v-if="checkPermission('/admin/Banner/importData.html')" >导入</el-button>
								<el-button type="warning"  size="mini" icon="el-icon-download"  @click="dumpdata(ids)" v-if="checkPermission('/admin/Banner/dumpdata.html')" >导出</el-button>
							</div>
							<table-tool :searchVisible.sync="searchVisible"  :fieldList="fieldList" @refesh_list="index" :searchTool="true"></table-tool>
						</div>
						<el-table :row-class-name="rowClass" @selection-change="selection"  @row-click="handleRowClick"  row-key="id"  :header-cell-style="{ background: '#eef1f6', color: '#606266' }" v-loading="loading"  ref="multipleTable" border class="eltable" :data="list"  style="width: 100%">
							<el-table-column align="center" type="selection" width="42"></el-table-column>
							<el-table-column align="center"  v-if="fieldList[0].checked" property="type" label="类型" show-overflow-tooltip width="">
								<template slot-scope="scope">
									<span v-if="scope.row.type == '0'" >用户端</span>
									<span v-if="scope.row.type == '1'" >技术端</span>
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[1].checked" property="image" label="图片" show-overflow-tooltip width="">
								<template slot-scope="scope">
									<div class="demo-image__preview">
										<el-image v-if="scope.row.image" class="table_list_pic" :src="scope.row.image"  :preview-src-list="[scope.row.image]"></el-image>
									</div>
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[2].checked" property="sort" label="排序" show-overflow-tooltip width="">
								<template class="sort" slot-scope="scope">
									<el-input @blur.stop="updateExt(scope.row,'sort')"  size="mini" placeholder="排序" v-model="scope.row.sort"></el-input>
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[3].checked" property="create_time" label="创建时间" show-overflow-tooltip width="">
								<template slot-scope="scope">
									{{parseTime(scope.row.create_time)}}
								</template>
							</el-table-column>
							<el-table-column align="center"  v-if="fieldList[4].checked" property="update_time" label="更新时间" show-overflow-tooltip width="">
								<template slot-scope="scope">
									{{parseTime(scope.row.update_time)}}
								</template>
							</el-table-column>
							<el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" label="操作" align="center" width="160">
								<template slot-scope="scope">
									<div v-if="scope.row.id">
										<el-button size="mini" type="primary" @click="update(scope.row)"  v-if="checkPermission('/admin/Banner/update.html')"><i class="el-icon-edit" />修改</el-button>
										<el-button size="mini" type="danger" @click="del(scope.row)"  v-if="checkPermission('/admin/Banner/delete.html')"><i class="el-icon-delete" />删除</el-button>
									</div>
								</template>
							</el-table-column>
						</el-table>
						<Page :total="page_data.total" :page.sync="page_data.page" :limit.sync="page_data.limit" @pagination="index" />
					</div>
				</el-card>
			</el-col>
		</el-row>
		<!--添加-->
		<Add  :show.sync="dialog.addDialogStatus" size="small" @refesh_list="index"></Add>
		<!--修改-->
		<Update :info="updateInfo" :show.sync="dialog.updateDialogStatus" size="small" @refesh_list="index"></Update>
		<!--查看详情-->
		<Detail :info="detailInfo" :show.sync="dialog.detailDialogStatus" size="small" @refesh_list="index"></Detail>
		<!--导入-->
		<ImportData  :show.sync="dialog.importDataDialogStatus" size="small" @refesh_list="index"></ImportData>
		<el-dialog title="导出进度条" :visible="dumpshow" :before-close="closedialog" width="500px">
			<el-progress :percentage="percentage"></el-progress>
		</el-dialog>
	</div>
</template>
<script>
	import {
		index,
		updateExt,
		getUpdateInfo,
		del,
		dumpdata,
	} from '@/api/admin/banner'
	import Search from '@/components/common/Search.vue'
	import Page from '@/components/common/Page.vue'
	import TableTool from '@/components/common/TableTool.vue'
	import Add from '@/views/admin/banner/add.vue'
	import Update from '@/views/admin/banner/update.vue'
	import Detail from '@/views/admin/banner/detail.vue'
	import ImportData from '@/views/admin/banner/importData.vue'
	import { confirm,param2Obj } from '@/utils/common'
	import XLSX from "xlsx"
	export default {
		name:'banner',
		components: {
			Search,
			Page,
			TableTool,
			Add,
			Update,
			Detail,
			ImportData,
		},
		data(){
			return {
				dialog: {
					addDialogStatus : false,
					updateDialogStatus : false,
					detailDialogStatus : false,
					importDataDialogStatus : false,
				},
				fieldList:[
					{lable:'类型',checked:true},
					{lable:'图片',checked:true},
					{lable:'排序',checked:true},
					{lable:'创建时间',checked:true},
					{lable:'更新时间',checked:true},
				],
				ids: [],
				single:true,
				multiple:true,
				list: [],
				updateInfo:{},
				detailInfo:{},
				exceldata:[],
				dumppage:1,
				ws:{},
				dumpshow:false,
				percentage:'',
				filename:'',
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
				Object.assign(param, this.searchData,param2Obj(this.$route.fullPath))
				this.loading = true
				index(param).then(res => {
					this.list = res.data.data
					this.page_data.total = res.data.total
					this.loading = false
					if(this.page_data.page == 1){
						this.searchForm = [
							{type:'Select',label:'类型',prop:'type',data:[{"key":"用户端","val":"0"},{"key":"技术端","val":"1"}],width:'150px'},
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
				getUpdateInfo({id:id}).then(res => {
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
			detail(row){
				this.dialog.detailDialogStatus = true
				this.detailInfo = {id:row.id ? row.id : this.ids.join(',')}
			},
			importData(){
				this.dialog.importDataDialogStatus = true
			},
			dumpdata(){
				confirm({content:'确定要操作吗'}).then(() => {
					this.dumpshow = true
					this.confirmdumpdata()
				}).catch(() => {})
			},
			confirmdumpdata(){
				dumpdata({page:this.dumppage}).then(res => {
					if(res.data.length > 0){
						if(this.dumppage == 1){
							this.exceldata.push(res.header)
						}
						res.data.forEach((item) => {
							this.exceldata.push(Object.values(item))
						})
						this.percentage = res.percentage
						this.filename = res.filename
						this.ws = XLSX.utils.aoa_to_sheet(this.exceldata)
						this.dumppage = this.dumppage + 1
						this.confirmdumpdata()
					}else{
						let wb = XLSX.utils.book_new()
						XLSX.utils.book_append_sheet(wb, this.ws, '轮播图配置')
						XLSX.writeFile(wb, this.filename)
						this.dumpshow = false
					}
				})
			},
			closedialog(){
				this.dumpshow = false
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