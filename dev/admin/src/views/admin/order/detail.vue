<template>
  <div>
    <el-dialog title="查看详情"
               width="1000px"
               @open="open"
               class="icon-dialog"
               :model-value="show"
               :before-close="closeForm"
               append-to-body>

      <el-steps :active="form.order_status==6?0:form.order_status "
                finish-status="success"
                simple
                style="margin-top: 20px">
        <el-step title="买家付款"></el-step>
        <el-step title="技师接单"></el-step>
        <el-step title="上门服务"></el-step>
        <el-step title="服务完成"></el-step>
      </el-steps>
      <el-row style="margin-top: 20px">
        <el-col :span="8">
          <div class="grid-content-left">
            <div class="grid-content-left-a">
              <div class="span-head">订单信息</div>
              <div class="span-span"><span class="span">订单编号：</span>{{form.order_sn}}</div>
              <div class="span-span"><span class="span">付款方式：</span>
                <span v-if="form.payment == '1'">余额</span>
                <span v-if="form.payment == '2'">微信</span>
                <span v-if="form.payment == '3'">支付宝</span>
              </div>
              <div class="span-span"><span class="span">买家：</span>{{form.user?form.user.nickname:''}}</div>
            </div>
            <div class="grid-content-left-b">
              <div class="span-head">买家信息</div>
              <div class="span-span"><span class="span">联系人：</span>{{form.realname}}</div>
              <div class="span-span"><span class="span">预约时间：</span>{{parseTime(form.appointment_time)}}</div>
              <div class="span-span"><span class="span">服务地址：</span>{{form.province+form.city+form.district+form.address}}</div>
              <div class="span-span"><span class="span">买家留言：</span>{{form.remark}}</div>
              <div class="span-span"><span class="span">退款金额：</span>{{form.Refund}}</div>
              <div class="span-span"><span class="span">退款原因：</span>{{form.refreason}}</div>
            </div>
          </div>
        </el-col>
        <el-col :span="16">
          <div class="grid-content-right flex">
            <i class="el-icon-info"
               style="font-size:50px;color:#67C23A"></i>
            <div>
              <div class="grid-content-right-a">订单状态：
                <span v-if="form.order_status == '0'">待付款</span>
                <span v-if="form.order_status == '1'">待接单</span>
                <span v-if="form.order_status == '2'">待服务</span>
                <span v-if="form.order_status == '3'">进行中</span>
                <span v-if="form.order_status == '4'">订单完成</span>
                <span v-if="form.order_status == '5'">已评价</span>
                <span v-if="form.order_status == '6'">取消</span>
              </div>
              <div v-if="form.order_status == '1'"
                   class="grid-content-righ-b">
                请按时进行上门服务
              </div>
            </div>
          </div>
        </el-col>
      </el-row>
      <el-row style="margin-top: 20px">
        <el-col :span="24">
          <div class="fuwu-center">
            <div class="span-head">服务信息</div>
            <div class="fuwu-center-warpper">
              <div>
                <el-image style="width: 80px; height: 80px"
                          :src="form.service?form.service.cover:''"
                          fit="cover">
                </el-image>
              </div>
              <div class="fuwu-center-warppe-b">
                <div><span class="span">服务类型：</span>{{form.service?form.service.name:''}}</div>
                <div style="padding-top: 5px;"><span class="span">服务项目：</span>{{form.servicespec?form.servicespec.name:''}}</div>
              </div>
              <div class="fuwu-center-warppe-c">
                <div><span class="span">服务价格：</span>{{form.servicespec?form.servicespec.price:''}}元</div>

              </div>
            </div>
          </div>
        </el-col>
      </el-row>
      <el-row style="margin-top: 20px">
        <el-col :span="24">
          <div class="fuwu-center">
            <div class="span-head">支付信息</div>
            <div class="fuwu-center-warpper">
              <div class="fuwu-center-warppe-d">
                <div><span class="span">支付金额：</span>{{form.pay_money}}元</div>
                <div style="padding-top: 5px;"><span class="span">总金额：</span>{{form.total_money}}元</div>
              </div>

              <div class="fuwu-center-warppe-c">
                <div><span class="span">优惠券价格：</span>{{form.discount}}元</div>
                <div style="padding-top: 5px;"><span class="span"
                        style="padding-top: 5px;">车费：</span>{{form.fare}}元</div>
              </div>
            </div>
          </div>
        </el-col>
      </el-row>

      <el-row style="margin-top: 20px">
        <el-col :span="24">
          <div class="fuwu-center">
            <div class="span-head">技师信息</div>
            <div class="fuwu-center-warpper">
              <div class="fuwu-center-warppe-e">
                <div>{{form.technician?form.technician.realname:''}}</div>
                <div style="padding-top: 5px;">{{form.technician?form.technician.mobile:''}}</div>
              </div>
              <div class="flex">
                <div class="flex fuwu-center-warppe-k">
                  <div class="num "
                       :class="{'active':form.order_status == '2'||form.order_status == '3'||form.order_status == '4'||form.order_status == '5'?true:false}"> 1</div>
                  <div style="padding-top: 5px; "
                       class="text">开始服务
                    <p style="padding-top: 5px;color: #969696;"> {{parseTime(form.service_time)||'— —'}}</p>
                  </div>
                </div>
                <div class="flex fuwu-center-warppe-j">
                  <div class="num"
                       :class="{'active':form.order_status == '3'||form.order_status == '4'||form.order_status == '5'
                       ?true:false}">2</div>
                  <div style="padding-top: 5px;"
                       class="text">服务完成
                    <p style="padding-top: 5px;color: #969696;"> {{parseTime(form.complete_time)||'— —'}}</p>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </el-col>
      </el-row>

    </el-dialog>
  </div>
</template>
<script>
import { detail } from '@/api/admin/order'
export default {
  name: 'Orderdetail',
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
  data () {
    return {
      form: {
      },
    }
  },
  methods: {
    open () {
      detail(this.info).then(res => {
        this.form = res.data
        console.log(this.form)
      })
    },
    closeForm () {
      this.$emit('update:show', false)
    }
  }
}
</script>
<style  lang="scss">
@import "@/assets/scss/common.scss";
.grid-content-left {
  border: #f5f5f5 1px solid;
  padding: 30px 10px;
  height: 220px;
}
.grid-content-right {
  border: #f5f5f5 1px solid;
  padding: 30px;
  height: 220px;
}
.span {
  color: #969696;
}
.span-head {
  font-size: 20px;
}
.span-span {
  padding-top: 5px;
}
.grid-content-left-b {
  border-top: #f5f5f5 1px solid;
  padding: 20px 0;
}
.grid-content-left-a {
  padding-bottom: 20px;
}
.grid-content-right-a {
  padding-left: 20px;
  font-size: 25px;
  font-weight: 500;
}
.grid-content-righ-b {
  padding-top: 10px;
  padding-left: 20px;
  color: #969696;
}
.fuwu-center {
  border: #f5f5f5 1px solid;
  padding: 30px;
  height: 80px;
}

.fuwu-center-warpper {
  padding-top: 10px;
  display: flex;
  //justify-content: space-between;
}
.fuwu-center-warppe-b {
  padding-left: 120px;
  padding-top: 10px;
  font-size: 16px;
}
.fuwu-center-warppe-c {
  padding-left: 120px;
  padding-top: 15px;
  font-size: 16px;
}
.fuwu-center-warppe-d {
  padding-top: 10px;
  font-size: 16px;
}
.fuwu-center-warppe-e {
  width: 200px;
  // background: #969696;
  border-left: #f5f5f5 1px solid;
}
.fuwu-center-warppe-k {
  padding: 0 50px;
  .num {
    background: #e8e8e8;
    width: 50px;
    height: 50px;
    text-align: center;
    line-height: 50px;
    font-size: 25px;
    border-radius: 50%;
    &.active {
      background: #67c23a;
      color: #ffffff;
    }
  }
  .text {
    padding-left: 20px;
  }
}
.fuwu-center-warppe-j {
  padding: 0 50px;
  .num {
    background: #e8e8e8;
    width: 50px;
    height: 50px;
    text-align: center;
    line-height: 50px;
    font-size: 25px;
    border-radius: 50%;
    &.active {
      background: #67c23a;
      color: #ffffff;
    }
  }
  .text {
    padding-left: 20px;
  }
}
</style>
