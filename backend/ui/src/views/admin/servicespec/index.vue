<template>
  <div>
    <el-row :gutter="10">
      <el-col :span="4"
              class="hidden-sm-and-down">
        <LeftTree :treeList="treeList"
                  :searchData.sync="searchData"
                  fieldName="service_id"
                  @refesh_list="index"></LeftTree>
      </el-col>
      <el-col :xs="24"
              :sm="24"
              :md="20"
              :lg="20">
        <el-card shadow="never"
                 class="card">
          <div id="charts_one"
               style="width:100%;min-height:80vh">
            <Search size='small'
                    :searchVisible="searchVisible"
                    :searchData.sync="searchData"
                    :searchForm="searchForm"
                    @refesh_list="index"></Search>
            <div class="btn-group"
                 style="margin-bottom:11px;">
              <div>
                <el-button type="success"
                           size="mini"
                           icon="el-icon-plus"
                           @click="add(ids)"
                           v-if="checkPermission('/admin/Servicespec/add.html')">添加</el-button>
                <el-button type="primary"
                           :disabled="single"
                           size="mini"
                           icon="el-icon-edit"
                           @click="update(ids)"
                           v-if="checkPermission('/admin/Servicespec/update.html')">修改</el-button>
                <el-button type="danger"
                           :disabled="multiple"
                           size="mini"
                           icon="el-icon-delete"
                           @click="del(ids)"
                           v-if="checkPermission('/admin/Servicespec/delete.html')">删除</el-button>
                <el-button type="info"
                           :disabled="single"
                           size="mini"
                           icon="el-icon-view"
                           @click="detail(ids)"
                           v-if="checkPermission('/admin/Servicespec/detail.html')">查看详情</el-button>
              </div>
              <table-tool :searchVisible.sync="searchVisible"
                          :fieldList="fieldList"
                          @refesh_list="index"
                          :searchTool="true"></table-tool>
            </div>
            <el-table :row-class-name="rowClass"
                      @selection-change="selection"
                      @row-click="handleRowClick"
                      row-key="id"
                      :header-cell-style="{ background: '#eef1f6', color: '#606266' }"
                      v-loading="loading"
                      ref="multipleTable"
                      border
                      class="eltable"
                      :data="list"
                      style="width: 100%">
              <el-table-column align="center"
                               type="selection"
                               width="42"></el-table-column>
              <el-table-column align="center"
                               type=''
                               v-if="fieldList[0].checked"
                               property="id"
                               label="表id"
                               show-overflow-tooltip
                               width="70">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[1].checked"
                               property="name"
                               label="名称"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[2].checked"
                               property="price"
                               label="价格"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[3].checked"
                               property="duration"
                               label="时长"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[4].checked"
                               property="sort"
                               label="排序"
                               show-overflow-tooltip
                               width="">
                <template class="sort"
                          slot-scope="scope">
                  <el-input @blur.stop="updateExt(scope.row,'sort')"
                            size="mini"
                            placeholder="排序"
                            v-model="scope.row.sort"></el-input>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[5].checked"
                               property="service.name"
                               label="服务类型"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               property="technician_rate"
                               label="技师分成"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column :fixed="$store.getters.device !== 'mobile'?'right':false"
                               label="操作"
                               align="center"
                               width="160">
                <template slot-scope="scope">
                  <div v-if="scope.row.id">
                    <el-button size="mini"
                               type="primary"
                               @click="update(scope.row)"
                               v-if="checkPermission('/admin/Servicespec/update.html')"><i class="el-icon-edit" />修改</el-button>
                    <el-button size="mini"
                               type="danger"
                               @click="del(scope.row)"
                               v-if="checkPermission('/admin/Servicespec/delete.html')"><i class="el-icon-delete" />删除</el-button>
                  </div>
                </template>
              </el-table-column>
            </el-table>
            <Page :total="page_data.total"
                  :page.sync="page_data.page"
                  :limit.sync="page_data.limit"
                  @pagination="index" />
          </div>
        </el-card>
      </el-col>
    </el-row>
    <!--添加-->
    <Add :show.sync="dialog.addDialogStatus"
         size="small"
         @refesh_list="index"></Add>
    <!--修改-->
    <Update :info="updateInfo"
            :show.sync="dialog.updateDialogStatus"
            size="small"
            @refesh_list="index"></Update>
    <!--查看详情-->
    <Detail :info="detailInfo"
            :show.sync="dialog.detailDialogStatus"
            size="small"
            @refesh_list="index"></Detail>
  </div>
</template>
<script>
import {
  index,
  updateExt,
  getUpdateInfo,
  del,
} from '@/api/admin/servicespec'
import Search from '@/components/common/Search.vue'
import Page from '@/components/common/Page.vue'
import TableTool from '@/components/common/TableTool.vue'
import LeftTree from '@/components/common/LeftTree.vue'
import Add from '@/views/admin/servicespec/add.vue'
import Update from '@/views/admin/servicespec/update.vue'
import Detail from '@/views/admin/servicespec/detail.vue'
import { confirm, param2Obj } from '@/utils/common'
export default {
  name: 'servicespec',
  components: {
    Search,
    Page,
    TableTool,
    LeftTree,
    Add,
    Update,
    Detail,
  },
  data () {
    return {
      dialog: {
        addDialogStatus: false,
        updateDialogStatus: false,
        detailDialogStatus: false,
      },
      fieldList: [
        { lable: '表id', checked: true },
        { lable: '名称', checked: true },
        { lable: '价格', checked: true },
        { lable: '时长', checked: true },
        { lable: '排序', checked: true },
        { lable: '服务类型', checked: true },
      ],
      ids: [],
      single: true,
      multiple: true,
      list: [],
      treeList: [],
      updateInfo: {},
      detailInfo: {},
      loading: false,
      page_data: {
        limit: 20,
        page: 1,
        total: 20,
      },
      searchVisible: true,
      searchForm: [],
      searchData: {},
    }
  },
  methods: {
    index () {
      let param = { limit: this.page_data.limit, page: this.page_data.page }
      Object.assign(param, this.searchData, param2Obj(this.$route.fullPath))
      this.loading = true
      index(param).then(res => {
        this.list = res.data.data
        this.page_data.total = res.data.total
        this.loading = false
        if (this.page_data.page == 1) {
          this.treeList = res.sql_field_data.service_ids
          this.searchForm = [
            { type: 'Input', label: '价格', prop: 'price', width: '150px' },
            { type: 'Input', label: '时长', prop: 'duration', width: '150px' },
            { type: 'Select', label: '类型类型', prop: 'service_id', data: res.sql_field_data.service_ids, width: '150px' },
          ]
        }
      })
    },
    updateExt (row, field) {
      if (row.id) {
        updateExt({ id: row.id, [field]: row[field] }).then(res => {
          this.$message({ message: '操作成功', type: 'success' })
        })
      }
    },
    add () {
      this.dialog.addDialogStatus = true
    },
    update (row) {
      let id = row.id ? row.id : this.ids.join(',')
      getUpdateInfo({ id: id }).then(res => {
        if (res.status == 200) {
          this.dialog.updateDialogStatus = true
          this.updateInfo = res.data
        }
      })
    },
    del (row) {
      confirm({ content: '确定要操作吗' }).then(() => {
        let ids = row.id ? row.id : this.ids.join(',')
        del({ id: ids }).then(res => {
          this.$message({ message: res.msg, type: 'success' })
          this.index()
        })
      }).catch(() => { })
    },
    detail (row) {
      this.dialog.detailDialogStatus = true
      this.detailInfo = { id: row.id ? row.id : this.ids.join(',') }
    },
    selection (selection) {
      this.ids = selection.map(item => item.id)
      this.single = selection.length != 1
      this.multiple = !selection.length
    },
    handleRowClick (row, rowIndex, event) {
      if (event.target.className !== 'el-input__inner') {
        this.$refs.multipleTable.toggleRowSelection(row)
      }
    },
    rowClass ({ row, rowIndex }) {
      for (let i = 0; i < this.ids.length; i++) {
        if (row.id === this.ids[i]) {
          return 'rowLight'
        }
      }
    },
  },
  mounted () {
    this.index()
  }
}
</script>
<style  lang="scss">
@import "@/assets/scss/common.scss";
</style>