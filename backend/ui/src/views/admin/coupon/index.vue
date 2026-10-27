<template>
  <div>
    <el-row :gutter="10">
      <el-col :span="24">
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
                           v-if="checkPermission('/admin/Coupon/add.html')">添加</el-button>
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
                               label="ID"
                               show-overflow-tooltip
                               width="70">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[1].checked"
                               property="name"
                               label="优惠券名称"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[2].checked"
                               property="exchange"
                               label="兑换码"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[3].checked"
                               property="type"
                               label="类型"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <span v-if="scope.row.type == '0'">满减券</span>
                  <span v-if="scope.row.type == '1'">折扣券</span>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[4].checked"
                               property="money"
                               label="优惠金额"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <span v-if="scope.row.type==0">{{scope.row.money}}</span>
                  <span v-else>--</span>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[5].checked"
                               property="discount"
                               label="折扣"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <span v-if="scope.row.type==1">{{scope.row.discount}}</span>
                  <span v-else>--</span>
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[6].checked"
                               property="condition"
                               label="使用门槛"
                               show-overflow-tooltip
                               width="">
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[7].checked"
                               property="start_time"
                               label="用券开始时间"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  {{parseTime(scope.row.start_time)}}
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[8].checked"
                               property="end_time"
                               label="截至时间"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  {{parseTime(scope.row.end_time)}}
                </template>
              </el-table-column>
              <el-table-column align="center"
                               v-if="fieldList[9].checked"
                               property="status"
                               label="状态"
                               show-overflow-tooltip
                               width="">
                <template slot-scope="scope">
                  <el-switch @change="updateExt(scope.row,'status')"
                             :active-value="1"
                             :inactive-value="0"
                             v-model="scope.row.status"></el-switch>
                </template>
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
                               v-if="checkPermission('/admin/Coupon/update.html')"><i class="el-icon-edit" />修改</el-button>
                    <el-button size="mini"
                               type="danger"
                               @click="del(scope.row)"
                               v-if="checkPermission('/admin/Coupon/delete.html')"><i class="el-icon-delete" />删除</el-button>
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
} from '@/api/admin/coupon'
import Search from '@/components/common/Search.vue'
import Page from '@/components/common/Page.vue'
import TableTool from '@/components/common/TableTool.vue'
import Add from '@/views/admin/coupon/add.vue'
import Update from '@/views/admin/coupon/update.vue'
import Detail from '@/views/admin/coupon/detail.vue'
import { confirm, param2Obj } from '@/utils/common'
export default {
  name: 'coupon',
  components: {
    Search,
    Page,
    TableTool,
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
        { lable: 'ID', checked: true },
        { lable: '优惠券名称', checked: true },
        { lable: '兑换码', checked: true },
        { lable: '类型', checked: true },
        { lable: '优惠金额', checked: true },
        { lable: '折扣', checked: true },
        { lable: '使用门槛', checked: true },
        { lable: '用券开始时间', checked: true },
        { lable: '截至时间', checked: true },
        { lable: '状态', checked: true },
      ],
      ids: [],
      single: true,
      multiple: true,
      list: [],
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
          this.searchForm = [
            { type: 'Input', label: '优惠券名称', prop: 'name', width: '150px' },
            { type: 'Select', label: '类型', prop: 'type', data: [{ "key": "满减券", "val": "0" }, { "key": "折扣券", "val": "1" }], width: '150px' },
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