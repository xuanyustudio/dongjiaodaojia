<template>
  <div class="home">
    <el-row :gutter="15">
      <el-col v-for="(car,index) in web_card_data"
              :key="index"
              :xs="12"
              :sm="12"
              :md="12"
              :lg="6">
        <el-card shadow="never">
          <div slot="header"
               class="clearfix">
            <span>
              <i :class="car.card_title_icon" />
              {{car.card_title}}
            </span>
            <div class="cycle"
                 :style="{backgroundColor:car.card_cycle_back_color}">{{car.card_cycle}}</div>
          </div>
          <div>
            <h1 style="font-size:150%;color:#909399">{{car.vist_num}}</h1>
            <br />
            <p style="float:left;color:#909399">{{car.bottom_title}}</p>
            <p style="float:right;color:#909399">
              {{car.vist_all_num}}
            </p>
          </div>
          <br />
        </el-card>
      </el-col>
    </el-row>
    <el-row v-if="menus"
            :gutter="15"
            style="margin-top:5px; text-align:center">
      <el-col :xs="12"
              :sm="12"
              :md="6"
              :lg="3"
              v-for="(item,index) in menus"
              :key="index">
        <router-link :to="item.url">
          <el-card shadow="hover">
            <el-image :src="item.menu_pic"
                      style="width: 32px; height: 32px"></el-image>
            <div class="icon_title">{{item.title}}</div>
          </el-card>
        </router-link>
      </el-col>
    </el-row>
    <el-row v-if="echat_data">
      <el-card shadow="never"
               style="margin-top:5px;">
        <div slot="header"
             class="clearfix"><span>数据统计</span></div>
        <div>
          <el-row :gutter="20">
            <el-col :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="16">
              <div id="myChart"
                   style="height:300px; margin-bottom:10px"></div>
            </el-col>
            <el-col :xs="24"
                    :sm="24"
                    :md="24"
                    :lg="8">
              <div id="label_vist"
                   style="height:300px"></div>
            </el-col>
          </el-row>
        </div>
      </el-card>
    </el-row>
  </div>
</template>
<script>
import { homeData } from '@/api/admin/base'
import "echarts/theme/macarons.js";
export default {
  data () {
    return {
      web_card_data: [],
      echat_data: [],
      menus: [],
    }
  },
  methods: {
    cardData () {
      homeData().then(res => {
        if (res.status == 200) {
          this.web_card_data = res.card_data
          this.menus = res.menus
          this.echat_data = res.echat_data
          if (this.echat_data) {
            this.drawLine(res.echat_data.day_count)
          }
          if (this.echat_data) {
            this.label_vist(res.echat_data.cycle_data)
          }
        }
      })
    },
    drawLine (data) {
      let myChart = this.$echarts.init(document.getElementById('myChart'), "macarons")
      myChart.setOption({
        title: {
          text: data.title
        },
        tooltip: {
          trigger: 'axis'
        },
        grid: {
          x: 40,
          x2: 40,
          y2: 24
        },
        calculable: true,
        xAxis: [
          {
            type: 'category',
            boundaryGap: false,
            data: data.day
          }
        ],
        yAxis: [
          {
            type: 'value',
          }
        ],
        series: [
          {
            name: '业绩',
            type: 'line',
            data: data.data,
            markPoint: {
              data: [
                { type: 'max', name: '最大值' }
              ]
            },
            markLine: {
              data: [
                { type: 'average', name: '平均值' }
              ]
            }
          }
        ]
      })
    },
    label_vist (data) {
      let myChart = this.$echarts.init(document.getElementById('label_vist'), "macarons")
      myChart.setOption({
        title: {
          text: data.title,
          left: "center",
        },
        tooltip: {
          trigger: "item",
          formatter: "{a} <br/>{b} : {c} ({d}%)",
        },
        legend: {
          orient: "vertical",
          left: "left",
          data: data.catagory,
        },
        series: [
          {
            name: "用户类别",
            type: "pie",
            radius: "55%",
            center: ["50%", "60%"],
            data: data.data,
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)",
              },
            },
          },
        ],
      });
    },
  },
  mounted () {
    this.cardData()
  },
};
</script>
<style scoped>
.home .el-card {
  margin-bottom: 10px;
}
.cycle {
  width: 30px;
  height: 25px;
  line-height: 25px;
  float: right;
  border-radius: 3px;
  color: white;
  text-align: center;
  margin-top: -5px;
}
.icon_title {
  margin-top: 10px;
  color: #909399;
}
</style>