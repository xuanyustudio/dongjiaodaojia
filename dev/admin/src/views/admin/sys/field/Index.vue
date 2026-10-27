<template>
  <div>
    <el-card shadow="never" class="card">
      <template #header><div class="clearfix">
        <span style="font-size:14px;">{{menu_title}} 字段列表</span>
        <el-button size="small" @click="jump" type="primary" style="float:right"><i class="el-icon-back"></i>返回</el-button>
      </div></template>
      <div v-if="this.$route.query.app_type == 1" id="charts_one" style="width:100%;min-height:500px">
          <!--按钮开始-->
          <div class="btn-group" style="margin-bottom:10px; margin-left:-3px">
            <div>
                <el-button type="success" size="small"  @click="addAdmin()"><i class="el-icon-plus"></i>新增</el-button>
                <!--<el-button type="primary" size="small"  @click="defaultField"><i class="el-icon-plus"></i>内置字段</el-button>-->
                <el-button type="danger" size="small"  :disabled="multiple" @click="deleteField(ids)"><i class="el-icon-delete"></i>删除</el-button>
                <el-button type="info" size="small"  @click="create"><i class="el-icon-edit-outline"></i>生成代码</el-button>
            </div>
            <table-tool :fieldList="fieldList" @refesh_list="loadlist" :searchTool="false"></table-tool>
          </div>
          <!--表格数据开始-->
          <el-table row-key="id" :header-cell-style="{ background: '#eef1f6', color: '#606266' }" :row-class-name="rowClass" v-loading="loading" ref="multipleTable" border class="eltable" :data="list" @selection-change="selection"  @row-click="handleRowClick" style="width: 100%"  >
            <el-table-column align="center" type="selection" width="42"></el-table-column>
            <el-table-column v-if="fieldList[0].checked" property="id" label="编号" width="70"></el-table-column>
            <el-table-column v-if="fieldList[1].checked" property="title"  label="字段标题">
              <template #default="scope">
                <el-input  size="small" placeholder="字段标题" @blur.stop="updateFieldExt(scope.row,'title')" v-model="scope.row.title"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[2].checked" property="field" label="字段名称">
              <template #default="scope">
                <el-input size="small" placeholder="字段名称" @blur.stop="updateField(scope.row,'field')" v-model="scope.row.field"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[3].checked" property="type" label="字段类型">
              <template #default="scope">
                <el-select v-model="scope.row.type" size="small" @change="updateFieldExt(scope.row,'type')" filterable placeholder="请选择">
                  <el-option v-for="(item,i) in field" :key="i" :label="item.name" :value="item.type"></el-option>
                </el-select>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[4].checked" property="list_show" label="显示位置/状态">
              <template #default="scope">
                <el-select v-model="scope.row.list_show" @change="updateFieldExt(scope.row,'list_show')" size="small" filterable placeholder="请选择">
                  <el-option-group label="显示状态">
                    <el-option key="2" label="不显示" :value="0"></el-option>
                    <el-option key="3" label="隐藏" :value="1"></el-option>
                  </el-option-group>
                  <el-option-group label="显示位置">
                    <el-option key="1" label="居中" :value="2"></el-option>
                    <el-option key="2" label="居左" :value="3"></el-option>
                    <el-option key="3" label="居右" :value="4"></el-option>
                  </el-option-group>
                </el-select>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[5].checked" align="left" property="search_type" label="搜索状态/模式">
              <template #default="scope">
                <el-select v-model="scope.row.search_type" @change="updateFieldExt(scope.row,'search_type')" size="small" placeholder="请选择">
                  <el-option-group label="是否搜索">
                    <el-option key="1" label="否" :value="0"></el-option>
                  </el-option-group>
                  <el-option-group label="搜索模式">
                    <el-option key="1" label="=" :value="1"></el-option>
                    <el-option key="2" label="like" :value="2"></el-option>
                  </el-option-group>
                </el-select>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[7].checked" width="80" align="center" property="create_table_field" label="创建字段">
              <template #default="scope">
                <el-tag size="small" @blur.stop="updateFieldExt(scope.row,'create_table_field')" :type="scope.row.create_table_field=='1'?'primary':'info'" effect="dark">{{ scope.row.create_table_field == '1' ? '是' : '否' }}</el-tag>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[7].checked" width="80" align="center" property="post_status" label="录入状态">
              <template #default="scope">
                <el-tag size="small" @blur.stop="updateFieldExt(scope.row,'post_status')" :type="scope.row.post_status=='1'?'primary':'info'" effect="dark">{{ scope.row.post_status == '1' ? '是' : '否' }}</el-tag>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[8].checked" width="100" align="center" property="width" label="单元格宽度">
              <template #default="scope">
                <el-input style="width:60px" @blur.stop="updateFieldExt(scope.row,'width')" size="small" placeholder="宽度" v-model="scope.row.width"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[9].checked" width="90" align="center" property="sortid" label="排序">
              <template #default="scope">
                <el-input style="width:60px;" @blur.stop="updateFieldExt(scope.row,'sortid')"  size="small" placeholder="排序" v-model="scope.row.sortid"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[10].checked" align="center" property="datatyp" label="数据结构">
              <template #default="scope">
                {{scope.row.datatype}}({{scope.row.length}})
              </template>
            </el-table-column>
            <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" label="操作" align="center" width="130">
              <template #default="scope">
                <el-button size="small" style="padding:7px 5px" type="primary" @click="editorAdmin(scope.row)"><i class="el-icon-edit-outline" />修改</el-button>
                <el-button size="small" style="padding:7px 5px" type="danger" @click="deleteField(scope.row)" ><i class="el-icon-delete" />删除</el-button>
              </template>
            </el-table-column>
          </el-table> 
          <Page :total="page_data.total" v-model:page="page_data.page" v-model:limit="page_data.limit" @pagination="loadlist" />
      </div>

      <div v-if="this.$route.query.app_type == 2" id="charts_one" style="width:100%;min-height:500px">
          <!--按钮开始-->
          <div class="btn-group" style="margin-bottom:10px; margin-left:-3px">
            <div>
                <el-button type="success" size="small"  @click="addApi()"><i class="el-icon-plus"></i>新增</el-button>
                <el-button type="danger" size="small"  :disabled="multiple" @click="deleteField(ids)"><i class="el-icon-delete"></i>删除</el-button>
                <el-button type="info" size="small"  @click="create"><i class="el-icon-edit-outline"></i>生成代码</el-button>
            </div>
            <table-tool :fieldList="fieldList" @refesh_list="loadlist" :searchTool="false"></table-tool>
          </div>
          <!--表格数据开始-->
          <el-table row-key="id" :header-cell-style="{ background: '#eef1f6', color: '#606266' }" :row-class-name="rowClass" v-loading="loading" ref="multipleTable" border class="eltable" :data="list" @selection-change="selection"  @row-click="handleRowClick" style="width: 100%"  >
            <el-table-column align="center" type="selection" width="42"></el-table-column>
            <el-table-column v-if="fieldList[0].checked" property="id" label="编号" width="70"></el-table-column>
            <el-table-column v-if="fieldList[1].checked" property="title"  label="字段标题">
              <template #default="scope">
                <el-input  size="small" placeholder="字段标题" @blur.stop="updateFieldExt(scope.row,'title')" v-model="scope.row.title"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[2].checked" property="field" label="字段名称">
              <template #default="scope">
                <el-input size="small" placeholder="字段名称" @blur.stop="updateField(scope.row,'field')" v-model="scope.row.field"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[3].checked" property="type" label="字段类型">
              <template #default="scope">
                <el-select v-model="scope.row.type" size="small" @change="updateFieldExt(scope.row,'type')" filterable placeholder="请选择">
                  <el-option v-for="(item,i) in field" :key="i" :label="item.name" :value="item.type"></el-option>
                </el-select>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[5].checked" align="left" property="search_type" label="搜索状态/模式">
              <template #default="scope">
                <el-select v-model="scope.row.search_type" @change="updateFieldExt(scope.row,'search_type')" size="small" placeholder="请选择">
                  <el-option-group label="是否搜索">
                    <el-option key="1" label="否" :value="0"></el-option>
                  </el-option-group>
                  <el-option-group label="搜索模式">
                    <el-option key="1" label="=" :value="1"></el-option>
                    <el-option key="2" label="like" :value="2"></el-option>
                  </el-option-group>
                </el-select>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[5].checked" align="center" property="list_show" label="是否显示">
              <template #default="scope">
                <el-switch @change="updateFieldExt(scope.row,'list_show')" :active-value="1" :inactive-value="0" v-model="scope.row.list_show"></el-switch>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[7].checked" width="80" align="center" property="create_table_field" label="创建字段">
              <template #default="scope">
                <el-tag size="small" @blur.stop="updateFieldExt(scope.row,'create_table_field')" :type="scope.row.create_table_field=='1'?'primary':'info'" effect="dark">{{ scope.row.create_table_field == '1' ? '是' : '否' }}</el-tag>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[7].checked" width="80" align="center" property="post_status" label="录入状态">
              <template #default="scope">
                <el-tag size="small" @blur.stop="updateFieldExt(scope.row,'post_status')" :type="scope.row.post_status=='1'?'primary':'info'" effect="dark">{{ scope.row.post_status == '1' ? '是' : '否' }}</el-tag>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[9].checked" width="90" align="center" property="sortid" label="排序">
              <template #default="scope">
                <el-input style="width:60px;" @blur.stop="updateFieldExt(scope.row,'sortid')"  size="small" placeholder="排序" v-model="scope.row.sortid"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[10].checked" align="center" property="datatyp" label="数据结构">
              <template #default="scope">
                {{scope.row.datatype}}({{scope.row.length}})
              </template>
            </el-table-column>
            <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" label="操作" align="center" width="130">
              <template #default="scope">
                <el-button size="small" style="padding:7px 5px" type="primary" @click="editorApi(scope.row)"><i class="el-icon-edit-outline" />修改</el-button>
                <el-button size="small" style="padding:7px 5px" type="danger" @click="deleteField(scope.row)" ><i class="el-icon-delete" />删除</el-button>
              </template>
            </el-table-column>
          </el-table> 
          <Page :total="page_data.total" v-model:page="page_data.page" v-model:limit="page_data.limit" @pagination="loadlist" />
      </div>

      <div v-if="this.$route.query.app_type == 3" id="charts_one" style="width:100%;min-height:500px">
          <!--按钮开始-->
          <div class="btn-group" style="margin-bottom:10px; margin-left:-3px">
            <div>
                <el-button type="success" size="small"  @click="addCms()"><i class="el-icon-plus"></i>新增</el-button>
                <el-button type="danger" size="small"  :disabled="multiple" @click="deleteField(ids)"><i class="el-icon-delete"></i>删除</el-button>
            </div>
            <table-tool :fieldList="fieldList" @refesh_list="loadlist" :searchTool="false"></table-tool>
          </div>
          <!--表格数据开始-->
          <el-table row-key="id" :header-cell-style="{ background: '#eef1f6', color: '#606266' }" :row-class-name="rowClass" v-loading="loading" ref="multipleTable" border class="eltable" :data="list" @selection-change="selection"  @row-click="handleRowClick" style="width: 100%"  >
            <el-table-column align="center" type="selection" width="42"></el-table-column>
            <el-table-column align="center" property="id" label="编号" width="70"></el-table-column>
            <el-table-column v-if="fieldList[1].checked" property="title"  label="字段标题">
              <template #default="scope">
                <el-input  size="small" placeholder="字段标题" @blur.stop="updateFieldExt(scope.row,'title')" v-model="scope.row.title"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[2].checked" property="field" label="字段名称">
              <template #default="scope">
                <el-input size="small" placeholder="字段名称" @blur.stop="updateField(scope.row,'field')" v-model="scope.row.field"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[3].checked" property="type" label="字段类型">
              <template #default="scope">
                <el-select v-model="scope.row.type" size="small" @change="updateFieldExt(scope.row,'type')" filterable placeholder="请选择">
                  <el-option v-for="(item,i) in field" :key="i" :label="item.name" :value="item.type"></el-option>
                </el-select>
              </template>
            </el-table-column>
            <el-table-column align="center" v-if="fieldList[4].checked" property="list_show" label="状态">
              <template #default="scope">
                <el-switch :active-value="2" :inactive-value="0" @change="updateFieldExt(scope.row,'list_show')" v-model="scope.row.list_show"></el-switch>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[9].checked" width="90" align="center" property="sortid" label="排序">
              <template #default="scope">
                <el-input style="width:60px;" @blur.stop="updateFieldExt(scope.row,'sortid')"  size="small" placeholder="排序" v-model="scope.row.sortid"></el-input>
              </template>
            </el-table-column>
            <el-table-column v-if="fieldList[10].checked" align="center" property="datatyp" label="数据结构">
              <template #default="scope">
                {{scope.row.datatype}}({{scope.row.length}})
              </template>
            </el-table-column>
            <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false" label="操作" align="center" width="130">
              <template #default="scope">
                <el-button size="small" style="padding:7px 5px" type="primary" @click="editorCms(scope.row)"><i class="el-icon-edit-outline" />修改</el-button>
                <el-button size="small" style="padding:7px 5px" type="danger" @click="deleteField(scope.row)" ><i class="el-icon-delete" />删除</el-button>
              </template>
            </el-table-column>
          </el-table> 
          <Page :total="page_data.total" v-model:page="page_data.page" v-model:limit="page_data.limit" @pagination="loadlist" />
      </div>
    </el-card>

    <!--添加-->
    <AdminAdd v-model:show="dialog.addAdminDialogStatus" :field="field" :itemList="itemList" size="small" @refesh_list="loadlist"></AdminAdd>
    <CmsAdd v-model:show="dialog.addCmsDialogStatus" :field="field" :itemList="itemList" size="small" @refesh_list="loadlist"></CmsAdd>
    <ApiAdd v-model:show="dialog.addApiDialogStatus" :field="field" :itemList="itemList" size="small" @refesh_list="loadlist"></ApiAdd>

    <!--修改-->
    <AdminUpdate v-model:show="dialog.updateAdminDialogStatus" :info="info" :field="field" :itemList="itemList" size="small" @refesh_list="loadlist"></AdminUpdate>
    <CmsUpdate v-model:show="dialog.updateCmsDialogStatus" :info="info" :field="field" :itemList="itemList" size="small" @refesh_list="loadlist"></CmsUpdate>
    <ApiUpdate v-model:show="dialog.updateApiDialogStatus" :info="info" :field="field" :itemList="itemList" size="small" @refesh_list="loadlist"></ApiUpdate>

   
  </div>
</template>
<script>
import Sortable from 'sortablejs'
import {fieldList,updateFieldExt,getFieldInfo,deleteField,updateFieldSort,create} from '../sys'
import { confirm} from '@/utils/common'
import Page from '@/components/common/Page.vue'
import TableTool from '@/components/common/TableTool.vue'
import AdminAdd from './admin/Add.vue'
import AdminUpdate from './admin/Update.vue'
import CmsAdd from './cms/Add.vue'
import CmsUpdate from './cms/Update.vue'
import ApiAdd from './api/Add.vue'
import ApiUpdate from './api/Update.vue'
export default {
  name:'Filed',
  components: { Page,AdminAdd,AdminUpdate,CmsAdd,CmsUpdate,ApiAdd,ApiUpdate,TableTool,Sortable},
  data() {
    return {
      dialog: {
        addAdminDialogStatus : false,
        updateAdminDialogStatus : false,
        addCmsDialogStatus : false,
        updateCmsDialogStatus : false,
        addApiDialogStatus : false,
        updateApiDialogStatus : false,
      },
      fieldList:[
        {lable:'编号',checked:false},
        {lable:'字段标题',checked:true},
        {lable:'字段名称',checked:true},
        {lable:'字段类型',checked:true},
        {lable:'显示位置/状态',checked:true},
        {lable:'搜索',checked:true},
        {lable:'验证方式',checked:true},
        {lable:'创建字段',checked:true},
        {lable:'单元格宽度',checked:true},
        {lable:'排序',checked:true},
        {lable:'数据结构',checked:true},
      ],
      menu_title:'',
      ids: {},
      field_titles:[],
      single:true,
      multiple: true,
      list: [],
      field:[],
      itemList:[],
      info:{},
      loading: false,
      page_data: {
        limit: 20,
        page: 1,
        total: 50,
      },
    };
  },
  methods: {
    addAdmin() {
       this.dialog.addAdminDialogStatus = true
    },
    editorAdmin(row) {
      let id = row.id ? row.id : this.ids.join(',')
      getFieldInfo({id:id}).then(res => {
          this.dialog.updateAdminDialogStatus = true
          this.info = res.data
      })
    },
    addCms() {
       this.dialog.addCmsDialogStatus = true
    },
    editorCms(row) {
      let id = row.id ? row.id : this.ids.join(',')
      getFieldInfo({id:id}).then(res => {
          this.dialog.updateCmsDialogStatus = true
          this.info = res.data
      })
    },
    addApi() {
       this.dialog.addApiDialogStatus = true
    },
    editorApi(row) {
      let id = row.id ? row.id : this.ids.join(',')
      getFieldInfo({id:id}).then(res => {
          this.dialog.updateApiDialogStatus = true
          this.info = res.data
      })
    },
    selection(selection) {
       this.ids = selection.map(item => item.id)
       this.single = selection.length != 1
       this.multiple = !selection.length
    },

    handleRowClick(row, rowIndex){
       this.$refs.multipleTable.toggleRowSelection(row);
    },
    rowClass ({ row, rowIndex }) {
      for(let i=0;i<this.ids.length;i++) {
          if (row.id === this.ids[i]) {
              return 'rowLight'
          }
      }
    },
    rowDrop() {
      const table = this.$refs.multipleTable
      const el = table && table.$el && table.$el.querySelectorAll('.el-table__body-wrapper > table > tbody')[0]
      if (!el) return
      this.sortable = Sortable.create(el, {
        ghostClass: 'sortable-ghost',
        setData: function (dataTransfer) {
          dataTransfer.setData('Text', '')
        },
        onEnd: e => {
        const targetRow = this.list.splice(e.oldIndex, 1)[0]
        this.list.splice(e.newIndex, 0, targetRow)
        let currentId = this.list[e.newIndex].id
        let preId,nextId
        if( this.list[e.newIndex-1]){
          preId = this.list[e.newIndex-1].id
        }else {
          preId = ""
        }
        if( this.list[e.newIndex+1]){
            nextId = this.list[e.newIndex+1].id
        }else {
            nextId = ""
        }
        updateFieldSort({currentId:currentId,preId:preId,nextId:nextId,menu_id:this.$route.query.menu_id}).then(res => {
            this.loadlist()
            this.$message({message: '操作成功', type: 'success'})
        })
        }
      })
    },
    jump(){
      this.$router.push({path:'/admin/Menu/index.html',query: {
        app_id:this.$route.query.app_id,
        app_type:this.$route.query.app_type,
      }})
    },
    deleteField(row) {
        confirm({content:'确定删除字段吗'}).then(() => {
            let ids = row.id ? row.id : this.ids
            deleteField({id:ids,menu_id:this.$route.query.menu_id}).then(res => {
                this.$message({message: res.pk_status ? '操作成功,其中主键无法删除' :'操作成功', type: 'success'})
                this.loadlist()
            })
        }).catch(() => {})
    },
    updateFieldExt(row,field){
      updateFieldExt({id:row.id,[field]:row[field]}).then(res => {
        this.$message({message: '操作成功', type: 'success'})
      })
    },
    updateField(row){
      updateFieldExt(row).then(res => {
        this.$message({message: '操作成功', type: 'success'})
      })
    },
    create(){
      confirm({content:'确定生成吗?'}).then(() => {
        create({menu_id:this.$route.query.menu_id}).then(res => {
            this.$message({message: '操作成功', type: 'success'})
        })
      }).catch(() => {})
    },
    loadlist() { 
      let param = {
        limit:this.page_data.limit,
        page:this.page_data.page,
        menu_id:this.$route.query.menu_id
      }
      this.loading = true
      fieldList(param).then(res => {
        this.menu_title = res.menu_title
        this.list = res.data.data
        this.field =  res.typeField
        this.itemList = res.itemList
        this.page_data.total = res.data.total
        this.loading = false
      })
    },
    defaultField(){
      this.dialog.fieldDialogStatus = true
    }
  },
  mounted() {
    this.loadlist()
    this.rowDrop()
  },
};
</script>
<style  lang="scss">
@import '@/assets/scss/common.scss'
</style>