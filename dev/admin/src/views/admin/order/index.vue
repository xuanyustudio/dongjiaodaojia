<template>
  <div style="">
    <div style="margin-bottom: 11px">
      <el-row :gutter="12">
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box">
                <div class="card-left">
                  <div style="text-align: center; line-height: 70px">
                    <i
                      class="el-icon-s-goods"
                      style="color: #ffffff; font-size: 28px"
                    ></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num">{{ count.num1 }}</div>
                <div class="text">总订单数</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box" style="background: #fff3e0">
                <div class="card-left" style="background: #ffab2b">
                  <div style="text-align: center; line-height: 70px">
                    <i
                      class="el-icon-s-promotion"
                      style="color: #ffffff; font-size: 28px"
                    ></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num">{{ count.num2 }}</div>
                <div class="text">今日订单数</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box" style="background: #eaf9e1">
                <div class="card-left" style="background: #6dd230">
                  <div style="text-align: center; line-height: 70px">
                    <i
                      class="el-icon-s-marketing"
                      style="color: #ffffff; font-size: 28px"
                    ></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num">{{ count.num3 }}</div>
                <div class="text">总订单金额</div>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card shadow="hover">
            <div class="card-wrpper">
              <div class="card-box" style="background: #ffeaf4">
                <div class="card-left" style="background: #ff85c0">
                  <div style="text-align: center; line-height: 70px">
                    <i
                      class="el-icon-s-claim"
                      style="color: #ffffff; font-size: 28px"
                    ></i>
                  </div>
                </div>
              </div>
              <div class="card-right">
                <div class="num">{{ count.num4 }}</div>
                <div class="text">今日付款</div>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>
    <el-row :gutter="10">
      <el-col :span="24">
        <el-card shadow="never" class="card">
          <!--头部-->
          <div id="charts_one" style="width: 100%; min-height: 80vh">
            <div style="margin-bottom: 11px">
              <label class="el-form-item__label">订单状态：</label>
              <el-radio-group v-model="radio1" size="small" @change="orderType">
                <el-radio-button label="8">全部</el-radio-button>
                <el-radio-button label="0">待付款</el-radio-button>
                <el-radio-button label="1">待接单</el-radio-button>
                <el-radio-button label="2">服务中</el-radio-button>
                <el-radio-button label="3">订单完成</el-radio-button>
                <el-radio-button label="4">已评价</el-radio-button>
                <el-radio-button label="5">退款</el-radio-button>
                <el-radio-button label="6">取消</el-radio-button>
              </el-radio-group>
            </div>
            <div style="margin-bottom: 11px">
              <label class="el-form-item__label">支付方式：</label>
              <el-radio-group v-model="radio2" size="small" @change="payType">
                <el-radio-button label="8">全部</el-radio-button>
                <el-radio-button label="1">微信</el-radio-button>
                <el-radio-button label="2">支付宝</el-radio-button>
                <el-radio-button label="3">余额</el-radio-button>
              </el-radio-group>
            </div>
            <Search
              size="small"
              :searchVisible="searchVisible"
              v-model:searchData="searchData"
              :searchForm="searchForm"
              @refesh_list="index"
            ></Search>
            <div class="btn-group" style="margin-bottom: 11px">
              <div>
                <el-button
                  type="info"
                  :disabled="single"
                  size="small"
                 
                  @click="detail(ids)"
                  v-if="checkPermission('/admin/Order/detail.html')"
                  ><i class="el-icon-view"></i>查看详情</el-button
                >
                <el-button
                  type="warning"
                  size="small"
                 
                  @click="dumpdata(ids)"
                  v-if="checkPermission('/admin/Order/dumpdata.html')"
                  ><i class="el-icon-download"></i>导出</el-button
                >
              </div>
              <table-tool
                v-model:searchVisible="searchVisible"
                :fieldList="fieldList"
                @refesh_list="index"
                :searchTool="true"
              ></table-tool>
            </div>
            <!--end-->
            <el-row :gutter="0" class="table-head">
              <el-col :span="5">
                <div class="grid-content bg-purple">服务项目</div>
              </el-col>
              <el-col :span="3">
                <div class="grid-content bg-purple">价格</div>
              </el-col>
              <el-col :span="6">
                <div class="grid-content bg-purple">下单用户</div>
              </el-col>
              <el-col :span="2">
                <div class="grid-content bg-purple">订单金额</div>
              </el-col>
              <el-col :span="4">
                <div class="grid-content bg-purple">订单状态</div>
              </el-col>
              <el-col :span="4">
                <div class="grid-content bg-purple">操作</div>
              </el-col>
            </el-row>
            <div class="table-list" v-for="(item, index) in list">
              <div class="item-head">
                <div>下单编号：{{ item.order_sn }}</div>
                <div style="padding-left: 20px">
                  下单时间：{{ parseTime(item.create_time) }}
                </div>
                <div style="padding-left: 20px">
                  预约时间：<span style="color: #ffb400">{{
                    parseTime(item.appointment_time)
                  }}</span>
                </div>
                <div></div>
              </div>
              <div class="item-center">
                <el-row :gutter="0">
                  <el-col :span="5">
                    <div
                      class="grid-content bg-purple flex"
                      style="height: 100px"
                    >
                      <div class="flex" v-if="item.service">
                        <el-image
                          style="width: 80px; height: 80px"
                          :src="item.service.cover"
                          fit="cover"
                        >
                        </el-image>
                        <div style="padding-left: 10px">
                          {{ item.service.name }}
                        </div>
                      </div>
                    </div>
                  </el-col>
                  <el-col
                    :span="3"
                    style="text-align: center; line-height: 100px"
                  >
                    <div class="grid-content bg-purple">
                      ¥{{ item.total_money }}
                    </div>
                  </el-col>
                  <el-col :span="6">
                    <div
                      class="grid-content bg-purple center"
                      style="text-align: center"
                    >
                      <div>
                        <div>
                          <el-tag type="success" size="small">{{
                            item.user.nickname
                          }}</el-tag>
                        </div>
                        <div style="padding-top: 5px">{{ item.address }}</div>
                        <div style="padding-top: 5px">
                          {{ item.user.mobile }}
                        </div>
                      </div>
                    </div>
                  </el-col>
                  <el-col :span="2" style="text-align: center">
                    <div class="center">
                      <div>
                        <div class="bg-purple">¥{{ item.pay_money }}</div>
                        <div class="bg-purple" style="padding-top: 5px">
                          <el-tag
                            type="warning"
                            v-if="item.payment == '1'"
                            size="small"
                            effect="dark"
                            >余额支付</el-tag
                          >
                          <el-tag
                            type="success"
                            v-if="item.payment == '2'"
                            size="small"
                            effect="dark"
                            >微信支付</el-tag
                          >
                          <el-tag
                            type="primary"
                            v-if="item.payment == '3'"
                            size="small"
                            effect="dark"
                            >支付宝支付</el-tag
                          >
                        </div>
                      </div>
                    </div>
                  </el-col>
                  <el-col
                    :span="4"
                    style="text-align: center; line-height: 100px"
                  >
                    <div class="grid-content bg-purple">
                      <span v-if="item.order_status == '0'">待付款</span>
                      <span v-if="item.order_status == '1'">待接单</span>
                      <span v-if="item.order_status == '2'">待服务</span>
                      <span v-if="item.order_status == '3'">服务中</span>
                      <span v-if="item.order_status == '4'">完成</span>
                      <span v-if="item.order_status == '5'">已评价</span>
                      <span v-if="item.order_status == '6'">取消订单</span>
                    </div>
                  </el-col>
                  <el-col
                    :span="4"
                    style="text-align: center; line-height: 100px"
                  >
                    <div class="">
                      <el-button
                        type="success"
                        size="small"
                        @click="detail(item)"
                        >查看详情</el-button
                      >
                      <el-button
                        type="success"
                        size="small"
                        @click="handleOpenRefundDialog(item)"
                        >退款</el-button
                      >
                      <el-button type="danger" size="small" @click="del(item)"
                        >删除</el-button
                      >
                    </div>
                  </el-col>
                </el-row>
              </div>
            </div>

            <Page
              :total="page_data.total"
              v-model:page="page_data.page"
              v-model:limit="page_data.limit"
              @pagination="index"
            />
          </div>
        </el-card>
      </el-col>
    </el-row>
    <!--添加-->
    <Add
      v-model:show="dialog.addDialogStatus"
      size="small"
      @refesh_list="index"
    ></Add>
    <!--修改-->
    <Update
      :info="updateInfo"
      v-model:show="dialog.updateDialogStatus"
      size="small"
      @refesh_list="index"
    ></Update>
    <!--查看详情-->
    <Detail
      :info="detailInfo"
      v-model:show="dialog.detailDialogStatus"
      size="small"
      @refesh_list="index"
    ></Detail>
    <el-dialog
      title="导出进度条"
      :visible="dumpshow"
      :before-close="closedialog"
      width="500px"
    >
      <el-progress :percentage="percentage"></el-progress>
    </el-dialog>

    <!--自定义弹窗-->
    <el-dialog
      title="退款操作"
      center
      v-model="outVisible"
      :before-close="outClose"
      append-to-body
    >
      <el-form :model="formData">
        <el-form-item label="退款金额" prop="refund_money">
          <el-input v-model="formData.refund_money" size="small"></el-input>
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="Refund()" type="primary"
            >确认退款</el-button
          >
          <el-button @click="outVisible = false">返回</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import {
  index,
  updateExt,
  getUpdateInfo,
  operateRefund,
  del,
  dumpdata,
  count,
} from "@/api/admin/order";
import Search from "@/components/common/Search.vue";
import Page from "@/components/common/Page.vue";
import TableTool from "@/components/common/TableTool.vue";
import Add from "@/views/admin/order/add.vue";
import Update from "@/views/admin/order/update.vue";
import Detail from "@/views/admin/order/detail.vue";
import { confirm, param2Obj } from "@/utils/common";
import * as XLSX from 'xlsx';
export default {
  name: "order",
  components: {
    Search,
    Page,
    TableTool,
    Add,
    Update,
    Detail,
  },
  data() {
    return {
      radio1: "8",
      radio2: "8",
      // radio3: '上海',
      // radio4: '上海',
      outVisible: false,
      formData: { refund_money: "" },
      dialog: {
        addDialogStatus: false,
        updateDialogStatus: false,
        detailDialogStatus: false,
      },
      fieldList: [
        { lable: "订单号", checked: true },
        { lable: "支付金额", checked: true },
        { lable: "总金额", checked: true },
        { lable: "优惠金额", checked: true },
        { lable: "技师收入", checked: true },
        { lable: "订单状态", checked: true },
        { lable: "用户id", checked: true },
        { lable: "技师id", checked: true },
        { lable: "服务id", checked: true },
        { lable: "规格id", checked: true },
        { lable: "优惠券id", checked: true },
        { lable: "姓名", checked: true },
        { lable: "电话", checked: true },
        { lable: "省", checked: true },
        { lable: "市", checked: true },
        { lable: "区", checked: true },
        { lable: "详细地址", checked: true },
        { lable: "创建时间", checked: true },
        { lable: "预约时间", checked: true },
        { lable: "时长", checked: true },
        { lable: "车费", checked: true },
        { lable: "退款金额", checked: true },
        { lable: "支付方式", checked: true },
        { lable: "佣金", checked: true },
        { lable: "服务完成时间", checked: true },
        { lable: "取消类型", checked: true },
        { lable: "纬度", checked: true },
        { lable: "经度", checked: true },
        { lable: "取消原因", checked: true },
        { lable: "接单时间", checked: true },
        { lable: "开始服务时间", checked: true },
        { lable: "下单用户", checked: true },
        { lable: "服务技师", checked: true },
        { lable: "服务类型", checked: true },
        { lable: "服务项目", checked: true },
        { lable: "优惠券", checked: true },
      ],
      ids: [],
      single: true,
      multiple: true,
      list: [],
      updateInfo: {},
      detailInfo: {},
      exceldata: [],
      dumppage: 1,
      ws: {},
      dumpshow: false,
      percentage: "",
      filename: "",
      loading: false,
      page_data: {
        limit: 20,
        page: 1,
        total: 20,
      },
      searchVisible: true,
      searchForm: [],
      searchData: {},
      count: {
        num1: 0,
        num2: 0,
        num3: 0,
        num4: 0,
      },
    };
  },
  methods: {
    orderType(e) {
      console.log(e);
      if (e != 8) {
        this.searchData.order_status = e;
        console.log(this.searchData);
      } else {
        delete this.searchData.order_status;
      }
      this.index();
    },
    handleOpenRefundDialog(row) {
      this.formData = row;
      this.outVisible = true;
    },
    Refund() {
      operateRefund(this.formData).then((res) => {
        this.outVisible = false;
        if (res["code"] == 0)
          this.$message.error(res['message']);
        else this.$message({ message: "操作成功", type: "success" });
      });
    },
    payType(e) {
      if (e != 8) {
        this.searchData.payment = e;
        console.log(this.searchData);
      } else {
        delete this.searchData.payment;
      }
      this.index();
      console.log(e);
    },
    index() {
      let param = { limit: this.page_data.limit, page: this.page_data.page };
      Object.assign(param, this.searchData, param2Obj(this.$route.fullPath));
      console.log(this.searchData);
      this.loading = true;
      index(param).then((res) => {
        this.list = res.data.data;
        this.page_data.total = res.data.total;
        this.loading = false;
        if (this.page_data.page == 1) {
          this.searchForm = [
            {
              type: "Input",
              label: "订单号：",
              prop: "order_sn",
              width: "150px",
            },
            // { type: 'Select', label: '订单状态', prop: 'order_status', data: [{ "val": "0", "key": "待付款" }, { "val": "1", "key": "待接单" }, { "val": "2", "key": "服务中" }, { "val": "3", "key": "订单完成" }, { "val": "4", "key": "已评价" }, { "val": "5", "key": "退款" }, { "val": "6", "key": "取消" }], width: '150px' },
            {
              type: "datetime",
              label: "创建时间：",
              prop: "create_time",
              width: "150px",
            },
            // { type: 'Select', label: '支付方式', prop: 'payment', data: [{ "val": "1", "key": "余额" }, { "val": "2", "key": "微信" }, { "val": "3", "key": "支付宝" }], width: '150px' },
            {
              type: "datetime",
              label: "服务完成时间：",
              prop: "complete_time",
              width: "150px",
            },
          ];
        }
      });
      count().then((res) => {
        this.count = res.data;
      });
    },
    updateExt(row, field) {
      if (row.id) {
        updateExt({ id: row.id, [field]: row[field] }).then((res) => {
          this.$message({ message: "操作成功", type: "success" });
        });
      }
    },
    add() {
      this.dialog.addDialogStatus = true;
    },
    update(row) {
      let id = row.id ? row.id : this.ids.join(",");
      getUpdateInfo({ id: id }).then((res) => {
        if (res.status == 200) {
          this.dialog.updateDialogStatus = true;
          this.updateInfo = res.data;
        }
      });
    },
    del(row) {
      confirm({ content: "确定要操作吗" })
        .then(() => {
          let ids = row.id ? row.id : this.ids.join(",");
          del({ id: ids }).then((res) => {
            this.$message({ message: res.msg, type: "success" });
            this.index();
          });
        })
        .catch(() => {});
    },
    detail(row) {
      this.dialog.detailDialogStatus = true;
      this.detailInfo = { id: row.id ? row.id : this.ids.join(",") };
    },
    dumpdata() {
      confirm({ content: "确定要操作吗" })
        .then(() => {
          this.dumpshow = true;
          this.confirmdumpdata();
        })
        .catch(() => {});
    },
    confirmdumpdata() {
      dumpdata({ page: this.dumppage }).then((res) => {
        if (res.data.length > 0) {
          if (this.dumppage == 1) {
            this.exceldata.push(res.header);
          }
          res.data.forEach((item) => {
            this.exceldata.push(Object.values(item));
          });
          this.percentage = res.percentage;
          this.filename = res.filename;
          this.ws = XLSX.utils.aoa_to_sheet(this.exceldata);
          this.dumppage = this.dumppage + 1;
          this.confirmdumpdata();
        } else {
          let wb = XLSX.utils.book_new();
          XLSX.utils.book_append_sheet(wb, this.ws, "订单列表");
          XLSX.writeFile(wb, this.filename);
          this.dumpshow = false;
        }
      });
    },
    closedialog() {
      this.dumpshow = false;
    },
    selection(selection) {
      this.ids = selection.map((item) => item.id);
      this.single = selection.length != 1;
      this.multiple = !selection.length;
    },
    handleRowClick(row, rowIndex, event) {
      if (event.target.className !== "el-input__inner") {
        this.$refs.multipleTable.toggleRowSelection(row);
      }
    },
    rowClass({ row, rowIndex }) {
      for (let i = 0; i < this.ids.length; i++) {
        if (row.id === this.ids[i]) {
          return "rowLight";
        }
      }
    },
  },
  mounted() {
    this.index();
  },
};
</script>
<style  lang="scss">
@import "@/assets/scss/common.scss";
.card-wrpper {
  display: flex;
  .card-box {
    width: 80px;
    height: 80px;
    background-color: #e4ecff;
    border-radius: 50%;
    margin-right: 20px;
    .card-left {
      margin: auto;
      margin-top: 10px;
      width: 60px;
      height: 60px;
      background-color: #4d7cfe;
      border-radius: 50%;
    }
  }
  .card-right {
    margin: auto 0;
    .num {
      font-size: 22px;
      font-weight: 400;
    }
    .text {
      font-size: 14px;
      color: #98a9bc;
      padding-top: 10px;
    }
  }
}
.table-head {
  background: #f0faff;
  padding: 10px 10px;
  text-align: center;
  color: #797979;
}
.table-list {
  .item-head {
    margin-top: 12px;
    background: #f5f5f5;
    padding: 10px 10px;
    display: flex;
    color: #797979;
  }
}
.flex {
  display: flex;
  justify-content: center;
  align-items: center;
}
.item-center {
  height: 100px;
  color: #797979;
  border: #f5f5f5 1px solid;
}
.grid-content {
  border: #f5f5f5 1px solid;
  // line-height: 100px;
  align-items: center;
}
.center {
  display: flex;
  align-items: center;
  height: 100px;
  justify-content: center;
}
</style>