<template>
  <div>
    <el-drawer
      title=""
      :visible.sync="show"
      direction="rtl"
      @open="open"
      :before-close="closeForm"
      size="80%"
    >
      <div class="head">
        <div class="info" style="justify-content: space-between">
          <div class="info-l">
            <el-avatar :src="form.avatar"></el-avatar>
            <span class="span" style="margin-left: 10px">{{
              form.realname
            }}</span>
            <div class="tag" style="margin: auto">
              <el-tag
                type="success"
                effect="dark"
                size="small"
                class="el-tag"
                v-if="form.status == 1"
                >已认证</el-tag
              >
              <el-tag
                type="warning"
                effect="dark"
                size="small"
                class="el-tag"
                v-else
                >未认证</el-tag
              >
            </div>
          </div>

          <div style="margin-top: 0px">
            <el-radio-group
              v-model="form.work_status"
              size="small"
              @change="changeradio"
            >
              <el-radio-button label="0">未工作</el-radio-button>
              <el-radio-button label="1">工作中</el-radio-button>
              <el-radio-button label="2">禁用</el-radio-button>
            </el-radio-group>
          </div>
        </div>

        <div class="info-row">
          <div class="info-row-item">
            <div class="text">余额</div>
            <div class="num">{{ form.balance }}元</div>
          </div>
          <div class="info-row-item">
            <div class="text">好评</div>
            <div class="num">{{ form.praise }}</div>
          </div>
          <div class="info-row-item">
            <div class="text">信用</div>
            <div class="num">{{ form.credit }}</div>
          </div>
          <div class="info-row-item">
            <div class="text">评分</div>
            <div class="num">{{ form.composite }}</div>
          </div>
          <div class="info-row-item">
            <div class="text">订单数量</div>
            <div class="num">{{ form.sales }}</div>
          </div>
        </div>
      </div>
      <div class="tag-warpper">
        <el-tabs type="card" class="container-tab">
          <el-tab-pane>
            <span slot="label"><i class="el-icon-user"></i> 用户信息</span>
            <div class="card-info-item">
              <div class="card-info-head">
                <div><span class="span"></span> 基本信息</div>
                <div class="button">
                  <el-button
                    type="primary"
                    size="mini"
                    v-if="
                      form.techniciandata && form.techniciandata.status == 0
                    "
                    round
                    @click="refuse('techniciandata', form.id, 1)"
                    >认证通过</el-button
                  >
                  <el-button
                    type="warning"
                    size="mini"
                    v-if="
                      form.techniciandata && form.techniciandata.status == 0
                    "
                    round
                    @click="refuse('techniciandata', form.id, 2)"
                    >认证拒绝</el-button
                  >
                  <el-tag
                    type="success"
                    effect="dark"
                    size="mini"
                    v-if="
                      form.techniciandata && form.techniciandata.status == 1
                    "
                    >认证通过</el-tag
                  >
                  <el-tag
                    type="danger"
                    effect="dark"
                    v-if="
                      form.techniciandata && form.techniciandata.status == 2
                    "
                    size="mini"
                    >认证失败</el-tag
                  >
                </div>
              </div>
              <div class="foot">
                <div class="foot-item">
                  <div class="foot-item-text">
                    姓名：{{ form.realname || "--" }}
                  </div>
                  <div class="foot-item-text">
                    手机号：{{ form.mobile || "--" }}
                  </div>
                  <div class="foot-item-text">
                    身份证号：{{
                      form.technicianidentity
                        ? form.technicianidentity.cardsn
                        : "--"
                    }}
                  </div>
                </div>
                <div class="foot-item">
                  <div class="foot-item-text">
                    性别：<span v-if="form.techniciandata">{{
                      form.techniciandata.sex == 1
                        ? "男"
                        : form.techniciandata.sex == 2
                        ? "女"
                        : "保密"
                    }}</span>
                  </div>
                  <div class="foot-item-text">
                    身高：{{
                      form.techniciandata ? form.techniciandata.height : "--"
                    }}
                    CM
                  </div>
                </div>
                <div class="foot-item">
                  <div class="foot-item-text">
                    生日：{{
                      form.techniciandata ? form.techniciandata.birthday : "--"
                    }}
                  </div>
                  <div class="foot-item-text">
                    简介：{{
                      form.techniciandata ? form.techniciandata.profile : "--"
                    }}
                  </div>
                </div>
              </div>
            </div>
            <div class="card-info-item">
              <div class="card-info-head">
                <div><span class="span"></span>认证状态</div>
              </div>
              <div class="foot auto-wrapper">
                <div>
                  <div
                    class="auto-info success"
                    :class="[
                      !form.techniciandata
                        ? 'warning'
                        : form.techniciandata.status == 0
                        ? 'warning'
                        : form.techniciandata.status == 1
                        ? 'success'
                        : 'danger',
                    ]"
                  >
                    <el-button
                      :type="
                        !form.techniciandata
                          ? 'warning'
                          : form.techniciandata.status == 0
                          ? 'warning'
                          : form.techniciandata.status == 1
                          ? 'success'
                          : 'danger'
                      "
                      :icon="
                        !form.techniciandata
                          ? 'el-icon-warning-outline'
                          : form.techniciandata.status == 0
                          ? 'el-icon-warning-outline'
                          : form.techniciandata.status == 1
                          ? 'el-icon-check'
                          : 'el-icon-close'
                      "
                      class="auto-b"
                      circle
                    ></el-button>
                  </div>
                  <div class="auth-text">基本信息认证</div>
                  <div class="auth-text-2">
                    {{
                      !form.techniciandata
                        ? "待认证"
                        : form.techniciandata.status == 0
                        ? "待认证"
                        : form.techniciandata.status == 1
                        ? "认证通过"
                        : "认证失败"
                    }}
                  </div>
                </div>

                <div>
                  <div
                    class="auto-info success"
                    :class="[
                      !form.technicianidentity
                        ? 'warning'
                        : form.technicianidentity.status == 0
                        ? 'warning'
                        : form.technicianidentity.status == 1
                        ? 'success'
                        : 'danger',
                    ]"
                  >
                    <el-button
                      :type="
                        !form.technicianidentity
                          ? 'warning'
                          : form.technicianidentity.status == 0
                          ? 'warning'
                          : form.technicianidentity.status == 1
                          ? 'success'
                          : 'danger'
                      "
                      :icon="
                        !form.technicianidentity
                          ? 'el-icon-warning-outline'
                          : form.technicianidentity.status == 0
                          ? 'el-icon-warning-outline'
                          : form.technicianidentity.status == 1
                          ? 'el-icon-check'
                          : 'el-icon-close'
                      "
                      class="auto-b"
                      circle
                    ></el-button>
                  </div>
                  <div class="auth-text">身份认证</div>
                  <div class="auth-text-2">
                    {{
                      !form.technicianidentity
                        ? "待认证"
                        : form.technicianidentity.status == 0
                        ? "待认证"
                        : form.technicianidentity.status == 1
                        ? "认证通过"
                        : "认证失败"
                    }}
                  </div>
                </div>

                <div>
                  <div
                    class="auto-info success"
                    :class="[
                      !form.technicianqualifications
                        ? 'warning'
                        : form.technicianqualifications.status == 0
                        ? 'warning'
                        : form.technicianqualifications.status == 1
                        ? 'success'
                        : 'danger',
                    ]"
                  >
                    <el-button
                      :type="
                        !form.technicianqualifications
                          ? 'warning'
                          : form.technicianqualifications.status == 0
                          ? 'warning'
                          : form.technicianqualifications.status == 1
                          ? 'success'
                          : 'danger'
                      "
                      :icon="
                        !form.technicianqualifications
                          ? 'el-icon-warning-outline'
                          : form.technicianqualifications.status == 0
                          ? 'el-icon-warning-outline'
                          : form.technicianqualifications.status == 1
                          ? 'el-icon-check'
                          : 'el-icon-close'
                      "
                      class="auto-b"
                      circle
                    ></el-button>
                  </div>
                  <div class="auth-text">资质认证</div>
                  <div class="auth-text-2">
                    {{
                      !form.technicianqualifications
                        ? "待认证"
                        : form.technicianqualifications.status == 0
                        ? "待认证"
                        : form.technicianqualifications.status == 1
                        ? "认证通过"
                        : "认证失败"
                    }}
                  </div>
                </div>
                <div>
                  <div
                    class="auto-info success"
                    :class="[
                      !form.technicianhealthy
                        ? 'warning'
                        : form.technicianhealthy.status == 0
                        ? 'warning'
                        : form.technicianhealthy.status == 1
                        ? 'success'
                        : 'danger',
                    ]"
                  >
                    <el-button
                      :type="
                        !form.technicianhealthy
                          ? 'warning'
                          : form.technicianhealthy.status == 0
                          ? 'warning'
                          : form.technicianhealthy.status == 1
                          ? 'success'
                          : 'danger'
                      "
                      :icon="
                        !form.technicianhealthy
                          ? 'el-icon-warning-outline'
                          : form.technicianhealthy.status == 0
                          ? 'el-icon-warning-outline'
                          : form.technicianhealthy.status == 1
                          ? 'el-icon-check'
                          : 'el-icon-close'
                      "
                      class="auto-b"
                      circle
                    ></el-button>
                  </div>
                  <div class="auth-text">健康认证</div>
                  <div class="auth-text-2">
                    {{
                      !form.technicianhealthy
                        ? "待认证"
                        : form.technicianhealthy.status == 0
                        ? "待认证"
                        : form.technicianhealthy.status == 1
                        ? "认证通过"
                        : "认证失败"
                    }}
                  </div>
                </div>
                <div>
                  <div
                    class="auto-info success"
                    :class="[
                      !form.techniciantcp
                        ? 'warning'
                        : form.techniciantcp.status == 0
                        ? 'warning'
                        : form.techniciantcp.status == 1
                        ? 'success'
                        : 'danger',
                    ]"
                  >
                    <el-button
                      :type="
                        !form.techniciantcp
                          ? 'warning'
                          : form.techniciantcp.status == 0
                          ? 'warning'
                          : form.techniciantcp.status == 1
                          ? 'success'
                          : 'danger'
                      "
                      :icon="
                        !form.techniciantcp
                          ? 'el-icon-warning-outline'
                          : form.techniciantcp.status == 0
                          ? 'el-icon-warning-outline'
                          : form.techniciantcp.status == 1
                          ? 'el-icon-check'
                          : 'el-icon-close'
                      "
                      class="auto-b"
                      circle
                    ></el-button>
                  </div>
                  <div class="auth-text">协议签署</div>
                  <div class="auth-text-2">
                    {{
                      !form.techniciantcp
                        ? "待签署"
                        : form.techniciantcp.status == 0
                        ? "未签署"
                        : form.techniciantcp.status == 1
                        ? "已签署"
                        : "签署失败"
                    }}
                  </div>
                </div>
              </div>
            </div>
            <div
              class="flex"
              style="text-align: center; margin-top: 20px; width: 80%"
            >
              <el-button
                type="primary"
                round
                icon="el-icon-circle-check"
                @click="refuse('auth', form.id, 1)"
                >通过认证开始接单</el-button
              >
            </div>
          </el-tab-pane>
          <el-tab-pane class="container-tab">
            <span slot="label"><i class="el-icon-success"></i> 认证信息</span>
            <div class="item">
              <div class="card-info-item">
                <div class="card-info-head">
                  <div><span class="span"></span> 身份认证</div>
                  <div class="button">
                    <el-button
                      type="primary"
                      size="mini"
                      v-if="
                        form.technicianidentity &&
                        form.technicianidentity.status == 0
                      "
                      round
                      @click="refuse('technicianidentity', form.id, 1)"
                      >认证通过</el-button
                    >
                    <el-button
                      type="warning"
                      size="mini"
                      v-if="
                        form.technicianidentity &&
                        form.technicianidentity.status == 0
                      "
                      round
                      @click="refuse('technicianidentity', form.id, 2)"
                      >认证拒绝</el-button
                    >
                    <el-tag
                      type="success"
                      effect="dark"
                      size="mini"
                      v-if="
                        form.technicianidentity &&
                        form.technicianidentity.status == 1
                      "
                      >认证通过</el-tag
                    >
                    <el-tag
                      type="danger"
                      effect="dark"
                      v-if="
                        form.technicianidentity &&
                        form.technicianidentity.status == 2
                      "
                      size="mini"
                      >认证失败</el-tag
                    >
                  </div>
                </div>
                <div class="foot-img">
                  <div class="card-img">
                    <el-image
                      class="img"
                      :src="
                        form.technicianidentity
                          ? form.technicianidentity.front
                          : ''
                      "
                      :preview-src-list="[
                        form.technicianidentity
                          ? form.technicianidentity.front
                          : '',
                      ]"
                      fit="cover"
                    ></el-image>
                  </div>
                  <div class="card-img">
                    <el-image
                      class="img"
                      fit="cover"
                      :src="
                        form.technicianidentity
                          ? form.technicianidentity.back
                          : ''
                      "
                      :preview-src-list="[
                        form.technicianidentity
                          ? form.technicianidentity.back
                          : '',
                      ]"
                    ></el-image>
                  </div>
                </div>
              </div>
              <el-divider></el-divider>
              <div class="card-info-item">
                <div class="card-info-head">
                  <div><span class="span"></span> 资质认证</div>
                  <div class="button">
                    <el-button
                      type="primary"
                      size="mini"
                      v-if="
                        form.technicianqualifications &&
                        form.technicianqualifications.status == 0
                      "
                      round
                      @click="refuse('technicianqualifications', form.id, 1)"
                      >认证通过</el-button
                    >
                    <el-button
                      type="warning"
                      size="mini"
                      v-if="
                        form.technicianqualifications &&
                        form.technicianqualifications.status == 0
                      "
                      round
                      @click="refuse('technicianqualifications', form.id, 2)"
                      >认证拒绝</el-button
                    >
                    <el-tag
                      type="success"
                      effect="dark"
                      size="mini"
                      v-if="
                        form.technicianqualifications &&
                        form.technicianqualifications.status == 1
                      "
                      >认证通过</el-tag
                    >
                    <el-tag
                      type="danger"
                      effect="dark"
                      v-if="
                        form.technicianqualifications &&
                        form.technicianqualifications.status == 2
                      "
                      size="mini"
                      >认证失败</el-tag
                    >
                  </div>
                </div>
                <div class="foot-img">
                  <div class="card-img">
                    <el-image
                      class="img"
                      :src="
                        form.technicianqualifications
                          ? form.technicianqualifications.certificates
                          : ''
                      "
                      :preview-src-list="[
                        form.technicianqualifications
                          ? form.technicianqualifications.certificates
                          : '',
                      ]"
                      fit="cover"
                    ></el-image>
                  </div>
                  <!-- <div class="card-img">
                    <el-image class="img"
                              fit="cover"
                              :src="form.technicianidentity?form.technicianidentity.back:''"
                              :preview-src-list="[form.technicianidentity?form.technicianidentity.back:'']"></el-image>
                  </div> -->
                </div>
              </div>
              <el-divider></el-divider>

              <div class="card-info-item">
                <div class="card-info-head">
                  <div><span class="span"></span> 健康认证</div>
                  <div class="button">
                    <el-button
                      type="primary"
                      size="mini"
                      v-if="
                        form.technicianhealthy &&
                        form.technicianhealthy.status == 0
                      "
                      round
                      @click="refuse('technicianhealthy', form.id, 1)"
                      >认证通过</el-button
                    >
                    <el-button
                      type="warning"
                      size="mini"
                      v-if="
                        form.technicianhealthy &&
                        form.technicianhealthy.status == 0
                      "
                      @click="refuse('technicianhealthy', form.id, 2)"
                      round
                      >认证拒绝</el-button
                    >
                    <el-tag
                      type="success"
                      effect="dark"
                      size="mini"
                      v-if="
                        form.technicianhealthy &&
                        form.technicianhealthy.status == 1
                      "
                      >认证通过</el-tag
                    >
                    <el-tag
                      type="danger"
                      effect="dark"
                      v-if="
                        form.technicianhealthy &&
                        form.technicianhealthy.status == 2
                      "
                      size="mini"
                      >认证失败</el-tag
                    >
                  </div>
                </div>
                <div class="foot-img">
                  <div class="card-img">
                    <el-image
                      class="img"
                      :src="
                        form.technicianhealthy
                          ? form.technicianhealthy.certificates
                          : ''
                      "
                      :preview-src-list="[
                        form.technicianhealthy
                          ? form.technicianhealthy.certificates
                          : '',
                      ]"
                      fit="cover"
                    ></el-image>
                  </div>
                  <!-- <div class="card-img">
                    <el-image class="img"
                              fit="cover"
                              :src="form.technicianidentity?form.technicianidentity.back:''"
                              :preview-src-list="[form.technicianidentity?form.technicianidentity.back:'']"></el-image>
                  </div> -->
                </div>
              </div>
              <el-divider></el-divider>

              <div class="card-info-item">
                <div class="card-info-head">
                  <div><span class="span"></span> 协议签署</div>
                  <div class="button">
                    <el-button
                      type="primary"
                      size="mini"
                      v-if="
                        form.techniciantcp && form.techniciantcp.status == 0
                      "
                      @click="refuse('techniciantcp', form.id, 1)"
                      round
                      >签署通过</el-button
                    >
                    <el-button
                      type="warning"
                      size="mini"
                      v-if="
                        form.techniciantcp && form.techniciantcp.status == 0
                      "
                      @click="refuse('techniciantcp', form.id, 2)"
                      round
                      >签署拒绝</el-button
                    >
                    <el-tag
                      type="success"
                      effect="dark"
                      size="mini"
                      v-if="
                        form.techniciantcp && form.techniciantcp.status == 1
                      "
                      >签署通过</el-tag
                    >
                    <el-tag
                      type="danger"
                      effect="dark"
                      v-if="
                        form.techniciantcp && form.techniciantcp.status == 2
                      "
                      size="mini"
                      >签署失败</el-tag
                    >
                  </div>
                </div>
                <div class="foot-img">
                  <div class="card-img">
                    <el-image
                      class="img"
                      :src="form.techniciantcp ? form.techniciantcp.sign : ''"
                      :preview-src-list="[
                        form.techniciantcp ? form.techniciantcp.sign : '',
                      ]"
                      fit="cover"
                    ></el-image>
                  </div>
                </div>
              </div>
              <el-divider></el-divider>
              <div class="card-info-item">
                <div class="card-info-head">
                  <div><span class="span"></span> 头像/相册</div>
                  <div class="button">
                    <el-button
                      type="primary"
                      size="mini"
                      v-if="
                        form.technicianalbum && form.technicianalbum.status == 0
                      "
                      @click="refuse('technicianalbum', form.id, 1)"
                      round
                      >认证通过</el-button
                    >
                    <el-button
                      type="warning"
                      size="mini"
                      v-if="
                        form.technicianalbum && form.technicianalbum.status == 0
                      "
                      @click="refuse('technicianalbum', form.id, 2)"
                      round
                      >认证拒绝</el-button
                    >
                    <el-tag
                      type="success"
                      effect="dark"
                      size="mini"
                      v-if="
                        form.technicianalbum && form.technicianalbum.status == 1
                      "
                      >认证通过</el-tag
                    >
                    <el-tag
                      type="danger"
                      effect="dark"
                      v-if="
                        form.technicianalbum && form.technicianalbum.status == 2
                      "
                      size="mini"
                      >认证失败</el-tag
                    >
                  </div>
                </div>
                <div class="foot-img">
                  <div class="card-img avatar-img">
                    <el-image
                      class="img"
                      :src="
                        form.technicianalbum ? form.technicianalbum.avatar : ''
                      "
                      :preview-src-list="[
                        form.technicianalbum ? form.technicianalbum.avatar : '',
                      ]"
                      fit="cover"
                    ></el-image>
                    <div class="avatar-img-item">头像</div>
                  </div>
                  <div v-if="form.technicianalbum" style="display: flex">
                    <div
                      class="card-img avatar-img"
                      v-for="(item, index) in JSON.parse(
                        form.technicianalbum.album
                      )"
                    >
                      <el-image class="img" fit="cover" :src="item"></el-image>
                      <div class="avatar-img-item">相册</div>
                    </div>
                  </div>
                </div>
              </div>
              <el-divider></el-divider>
              <div class="tag-foot"></div>
            </div>
          </el-tab-pane>
          <el-tab-pane label="">
            <span slot="label"
              ><i class="el-icon-s-opportunity"></i> 服务状态</span
            >
            <div class="card-info-item">
              <div class="card-info-head">
                <div><span class="span"></span> 服务项目</div>
                <div class="button"></div>
              </div>
              <el-table :data="form.service" style="width: 100%">
                <el-table-column prop="id" label="ID" width="180">
                </el-table-column>
                <el-table-column prop="name" label="项目名称" width="">
                </el-table-column>
              </el-table>
            </div>

            <div class="card-info-item">
              <div class="card-info-head" v-if="form.week">
                <div><span class="span"></span> 服务周期</div>
                <div class="button"></div>
                <div v-for="(item, index) in form.week.split(',')">
                  <el-tag type="success" v-if="item == 1"> 星期一</el-tag>
                  <el-tag type="success" v-if="item == 2"> 星期二</el-tag>
                  <el-tag type="success" v-if="item == 3"> 星期三</el-tag>
                  <el-tag type="success" v-if="item == 4"> 星期四</el-tag>
                  <el-tag type="success" v-if="item == 5"> 星期五</el-tag>
                  <el-tag type="success" v-if="item == 6"> 星期六</el-tag>
                  <el-tag type="success" v-if="item == 0"> 星期日</el-tag>
                </div>
              </div>
            </div>

            <div class="card-info-item">
              <div class="card-info-head">
                <div><span class="span"></span> 服务时间</div>
                <div>
                  {{ getNewIndex(form.start_time) }}--{{
                    getNewIndex(form.end_time)
                  }}
                </div>
              </div>
            </div>
          </el-tab-pane>
        </el-tabs>
      </div>
    </el-drawer>

    <!--自定义弹窗-->
    <el-dialog
      title="技师等级绑定"
      center
      :visible.sync="outVisible"
      :before-close="outClose"
      append-to-body
    >
      <el-form :model="formData">
        <el-form-item label="技师等级" prop="tgrade">
          <el-select v-model="form.tgrade">
            <el-option
              v-for="index of form.tgradecount"
              :label="等级 + index"
              :value="index"
              :key="index"
            ></el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button
            @click="
              outVisible = false;
              this.form.tgrade = null;
              return;
            "
            >取消</el-button
          >
          <el-button @click="setTgrade" type="primary">绑定</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import {
  detail,
  updateAuth,
  updateWork,
  updateTgrade,
} from "@/api/admin/technician";
import { confirm, param2Obj } from "@/utils/common";
export default {
  name: "Techniciandetail",
  props: {
    show: {
      type: Boolean,
      default: true,
    },
    size: {
      type: String,
      default: "mini",
    },
    info: {
      type: Object,
    },
  },
  data() {
    return {
      outVisible: false,
      temp_model: "",
      temp_id: "",
      temp_status: "",
      form: {},
    };
  },
  computed: {},
  methods: {
    setTgrade() {
      updateTgrade({ id: this.form.id, tgrade: this.form.tgrade }).then(
        (res) => {
          this.outVisible = false;
          this.$message({ message: "操作成功", type: "success" });
          this.updateInfo();
        }
      );
    },
    changeradio(e) {
      updateWork({ id: this.form.id, work_status: e }).then((res) => {
        this.$message({ message: "操作成功", type: "success" });
      });
    },
    getNewIndex: function (e) {
      if (e) {
        var h = parseInt(e / 2);
        var m = parseInt(e % 2);
        if (m > 0) {
          m = 30;
        } else {
          m = "00";
        }
        return h + ":" + m;
      }
    },
    refuse(model, id, status) {
      this.temp_model = model;
      this.temp_id = id;
      this.temp_status = status;
      if (!this.form.tgrade) {
        this.outVisible = true;
      } else {
        this.updateInfo();
      }
    },
    updateInfo() {
      confirm({ content: "确定要认证操作吗" })
        .then(() => {
          updateAuth({
            model: this.temp_model,
            id: this.temp_id,
            status: this.temp_status,
          }).then((res) => {
            this.$notify({
              title: "操作成功",
              message: "改信息已经操作成功！",
              type: "success",
            });
            this.open();
          });
        })
        .catch(() => {});
    },
    open() {
      detail(this.info).then((res) => {
        this.form = res.data;
      });
      console.log(this.form);
    },
    closeForm() {
      console.log("关闭");
      this.$emit("closeForm");
      this.$emit("update:show", false);
    },
  },
};
</script>
<style  scoped>
.container-tab >>> .el-tabs__content {
  overflow-y: auto;
  position: absolute;
  width: 100%;
  height: 100%;
}
</style>
<style  lang="scss" scoped>
@import "@/assets/scss/common.scss";
.head {
  padding: 0px 30px;
  height: 100px;
  // background: #797979;
  .info {
    // background: #fce18d;
    display: flex;
    align-items: center;
    .info-l {
      display: flex;

      .span {
        // margin-left: 10px;
        line-height: 50px;
        color: #797979;
      }
    }
    .el-tag {
      margin-left: 20px;
    }
  }
  .info-row {
    padding-top: 20px;
    display: flex;
    width: 67%;
    justify-content: space-between;
    .info-row-item {
      text-align: center;
      font-size: 13px;
      .text {
        color: #797979;
      }
      .num {
        padding-top: 10px;
      }
    }
  }
}
.tag-warpper {
  margin-top: 20px;
  .card-info-item {
    height: 100%;
    padding: 10px 20px;
    .card-info-head {
      width: 80%;
      display: flex;
      justify-content: space-between;
      .span {
        border-left: 3px solid #1890ff;
        margin-right: 10px;
      }
      .button {
      }
    }
    .foot {
      padding-top: 20px;
      display: flex;
      .foot-item {
        width: 33.3%;
        .foot-item-text {
          color: #606266;
          padding-top: 14px;
          font-size: 13px;
        }
      }
    }
  }
}
.foot-img {
  display: flex;
  .card-img {
    width: 300px;
    height: 180px;
    margin: 20px 30px;
    background: #e7e7e7;
    border-radius: 10px;
    .img {
      width: 300px;
      height: 180px;
    }
  }
}

.tag-foot {
  padding-bottom: 500px;
}
.avatar-img {
  position: relative;

  .avatar-img-item {
    width: 100%;
    position: absolute;
    bottom: 0px;
    height: 30px;
    line-height: 30px;
    text-align: center;
    background-color: rgba(0, 0, 0, 0.5);
    color: #e7e7e7;
  }
}

.auto-wrapper {
  width: 80%;
  display: flex;
  justify-content: space-around;
  .auto-info {
    padding: 10px;
    width: 80px;
    border-radius: 50%;
    &.success {
      background: #eaffe7;
    }
    &.warning {
      background: #fff6d8;
    }
    &.danger {
      background: #ffe5e5;
    }
    .auto-b {
      width: 80px;
      height: 80px;
      font-size: 30px;
    }
  }
}
.auth-text {
  margin-top: 20px;
  text-align: center;
}
.auth-text-2 {
  margin-top: 10px;
  text-align: center;
  color: #b1b1b1;
}
</style>

