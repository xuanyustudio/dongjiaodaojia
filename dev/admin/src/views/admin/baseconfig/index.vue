<template>
  <div>
    <el-card shadow="never" class="card">
      <el-form
        size="small"
        ref="form"
        :model="form"
        :rules="rules"
        label-width="90px"
      >
        <el-tabs v-model="activeName">
          <el-tab-pane
            style="padding-top: 10px"
            label="基本信息"
            name="基本信息"
          >
            <el-row>
              <el-col :span="24">
                <el-form-item label="站点名称" prop="site_title">
                  <el-input
                    v-model="form.site_title"
                    autoComplete="off"
                    clearable
                    placeholder="请输入站点名称"
                  ></el-input>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="站点logo" prop="logo">
                  <ImagesUpload
                    size="small"
                    fileType="image"
                    v-model:image="form.logo"
                  ></ImagesUpload>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="站点关键词" prop="keyword">
                  <Tag v-model:tagList="form.keyword"></Tag>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="站点版权" prop="copyright">
                  <el-input
                    v-model="form.copyright"
                    autoComplete="off"
                    clearable
                    placeholder="请输入站点版权"
                  ></el-input>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="客服电话" prop="copyright">
                  <el-input
                    v-model="mobile"
                    autoComplete="off"
                    clearable
                    placeholder="请输入客服号码"
                  ></el-input>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="二维码" prop="logo">
                  <ImagesUpload
                    size="small"
                    fileType="image"
                    v-model:image="qr_code"
                  ></ImagesUpload>
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>

          <el-tab-pane
            style="padding-top: 10px"
            label="订单设置"
            name="订单设置"
          >
            <el-row>
              <el-col :span="24">
                <el-form-item label="退款扣除" prop="tips">
                  <el-input placeholder="输入比例" type="number" v-model="rate">
                    <template #append>%百分比</template>
                  </el-input>
                  <div class="sub-title">*退款订单扣除用户支付费用的比例</div>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="24">
                <el-form-item label="退款技师收入比例" prop="tips">
                  <el-input
                    placeholder="输入比例"
                    type="number"
                    v-model="technician_rate"
                  >
                    <template #append>%百分比</template>
                  </el-input>
                  <div class="sub-title">*退款订单扣除用户支付费用的比例</div>
                </el-form-item>
              </el-col>
            </el-row>
            <el-divider content-position="left">提现设置</el-divider>
            <el-row>
              <el-col :span="24">
                <el-form-item label="最少提现" prop="tips">
                  <el-input
                    placeholder="最少提现额度"
                    type="number"
                    v-model="form.withdraw_min"
                  >
                    <template #append>元</template>
                  </el-input>
                  <div class="sub-title">*用户/技师傅提现最低金额</div>
                </el-form-item>
              </el-col>
            </el-row>
            <el-divider content-position="left">用户佣金设置</el-divider>
            <el-row>
              <el-col :span="24">
                <el-form-item label="一级佣金" prop="tips">
                  <el-input
                    placeholder="一级佣金"
                    type="number"
                    v-model="first"
                  >
                    <template #append>%</template>
                  </el-input>
                  <div class="sub-title">*用户推广用户获得佣金一级比例</div>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="二级佣金" prop="tips">
                  <el-input
                    placeholder="二级佣金"
                    type="number"
                    v-model="second"
                  >
                    <template #append>%</template>
                  </el-input>
                  <div class="sub-title">*用户推广用户获得佣金二级比例</div>
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>

          <el-tab-pane
            style="padding-top: 10px"
            label="技师等级配置"
            name="技师等级配置"
          >
            <el-row v-for="(item, index) in tglist" :key="index">
              <el-col :span="24">
                <el-form-item
                  :label="'等级' + (index + 1)"
                  :prop="'tg' + index"
                >
                  <el-input
                    placeholder="输入比例（%百分比）"
                    type="number"
                    v-model="item.value"
                  >
                    <template #append><el-button @click="addTg()">添加</el-button><el-button @click="removeTg(index)"
                      >删除</el-button></template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>

            <!-- <el-row>
              <el-col :span="24">
                <el-form-item label="退款技师收入比例" prop="tips">
                  <el-input
                    placeholder="输入比例"
                    type="number"
                    v-model="technician_rate"
                  >
                    <template slot="append">%百分比</template>
                  </el-input>
                  <div class="sub-title">*退款订单扣除用户支付费用的比例</div>
                </el-form-item>
              </el-col>
            </el-row> -->
          </el-tab-pane>

          <el-tab-pane
            style="padding-top: 10px"
            label="渠道设置"
            name="渠道设置"
          >
            <el-divider content-position="left">微信开放平台设置</el-divider>
            <el-row>
              <el-col :span="24">
                <el-form-item label="APPID" prop="tips">
                  <el-input placeholder="微信开放平台APPID" v-model="app_id">
                  </el-input>
                  <div class="sub-title">
                    *微信开放平台APPID，申请渠道https://open.weixin.qq.com
                  </div>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="24">
                <el-form-item label="AppSecret" prop="tips">
                  <el-input
                    placeholder="微信开放平台AppSecret"
                    v-model="app_secret"
                  >
                  </el-input>
                  <div class="sub-title">
                    *微信开放平台AppSecret，申请渠道https://open.weixin.qq.com
                  </div>
                </el-form-item>
              </el-col>
            </el-row>
            <el-divider content-position="left">微信支付设置</el-divider>
            <el-row>
              <el-col :span="24">
                <el-form-item label="MchId" prop="tips">
                  <el-input placeholder="微信商户ID" v-model="mch_id">
                  </el-input>
                  <div class="sub-title">
                    *微信商户ID，可以在微信商户平台或开通时的邮件中获取到。
                  </div>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="24">
                <el-form-item label="MchKey" prop="tips">
                  <el-input placeholder="微信商户KEY" v-model="mch_secret">
                  </el-input>
                  <div class="sub-title">
                    *微信商户ID，可以在微信商户平台或开通时的邮件中获取到。
                  </div>
                </el-form-item>
              </el-col>
            </el-row>
            <el-divider content-position="left">小程序设置</el-divider>
            <el-row>
              <el-col :span="24">
                <el-form-item label="APPID" prop="tips">
                  <el-input placeholder="公众号APPID" v-model="wx_app_id">
                  </el-input>
                  <div class="sub-title">
                    小程序APPID是所有接口必要参数，可以在小程序 [ 开发 >
                    基本配置 ] 页面获取。
                  </div>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="24">
                <el-form-item label="AppSecret" prop="tips">
                  <el-input placeholder="公众号Secret" v-model="wx_app_secret">
                  </el-input>
                  <div class="sub-title">
                    *小程序AppSecret是所有接口必要参数，可以在小程序 [ 开发 >
                    基本配置 ] 页面获取。
                  </div>
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>

          <!-- <el-tab-pane style="padding-top:10px"
                       label="短信设置"
                       name="短信设置">
            <el-divider content-position="left">阿里云短信设置</el-divider>
            <el-row>
              <el-col :span="24">
                <el-form-item label="短信模板"
                              prop="tips">
                  <el-input placeholder="阿里云短信模板"
                            v-model="templateCode">

                  </el-input>
                  <div class="sub-title">*阿里云申请的短信模板</div>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="24">
                <el-form-item label="AppSecret"
                              prop="tips">
                  <el-input placeholder="短信签名"
                            v-model="signName">

                  </el-input>
                  <div class="sub-title">*阿里云申请的短信签名</div>
                </el-form-item>
              </el-col>
            </el-row>
            <el-divider content-position="left">阿里云配置</el-divider>
            <el-row>
              <el-col :span="24">
                <el-form-item label="accessKeyId"
                              prop="tips">
                  <el-input placeholder="阿里云accessKeyId"
                            v-model="accessKeyId">

                  </el-input>
                  <div class="sub-title">*登录阿里云获取accessKeyId。</div>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="24">
                <el-form-item label="accessKeySecret"
                              prop="tips">
                  <el-input placeholder="阿里云accessKeySecret"
                            v-model="accessKeySecret">

                  </el-input>
                  <div class="sub-title">*登录阿里云获取accessKeySecret。</div>
                </el-form-item>
              </el-col>
            </el-row>

          </el-tab-pane> -->

          <el-tab-pane
            style="padding-top: 10px"
            label="紧急联系电话"
            name="紧急联系电话"
          >
            <el-divider content-position="left">紧急联系电话</el-divider>
            <el-row>
              <el-col :span="24">
                <el-form-item label="电话号码" prop="tips">
                  <el-input
                    placeholder="紧急联系电话号码"
                    v-model="urgent_mobile"
                  >
                  </el-input>
                  <div class="sub-title">多个电话间采用英文,分隔</div>
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>

          <!-- <el-tab-pane style="padding-top: 10px" label="云储存" name="云存储">
            <el-divider content-position="left">阿里云OSS设置</el-divider>
            <el-row>
              <el-col :span="24">
                <el-form-item label="bucket" prop="tips">
                  <el-input placeholder="阿里云bucket" v-model="bucket">
                  </el-input>
                  <div class="sub-title">*阿里云云存储bucket</div>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="24">
                <el-form-item label="endpoint" prop="tips">
                  <el-input placeholder="阿里云endpoint" v-model="endpoint">
                  </el-input>
                  <div class="sub-title">*阿里云云存储endpoint</div>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="24">
                <el-form-item label="accessKeyId" prop="tips">
                  <el-input
                    placeholder="阿里云accessKeyId"
                    v-model="ossaccessKeyId"
                  >
                  </el-input>
                  <div class="sub-title">*登录阿里云获取accessKeyId。</div>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="24">
                <el-form-item label="accessKeySecret" prop="tips">
                  <el-input
                    placeholder="阿里云accessKeySecret"
                    v-model="ossaccessKeySecret"
                  >
                  </el-input>
                  <div class="sub-title">*登录阿里云获取accessKeySecret。</div>
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane> -->

          <el-tab-pane
            style="padding-top: 10px"
            label="上传配置"
            name="上传配置"
          >
            <el-row>
              <el-col :span="24">
                <el-form-item label="上传配置" prop="filesize">
                  <el-input
                    v-model="form.filesize"
                    autoComplete="off"
                    clearable
                    placeholder="请输入上传配置"
                  ></el-input>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="文件类型" prop="filetype">
                  <el-input
                    v-model="form.filetype"
                    autoComplete="off"
                    clearable
                    placeholder="请输入文件类型"
                  ></el-input>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="水印状态" prop="water_status">
                  <el-radio-group v-model="form.water_status">
                    <el-radio :label="'1'">正常</el-radio>
                    <el-radio :label="'0'">禁用</el-radio>
                  </el-radio-group>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="水印位置" prop="type">
                  <el-select
                    style="width: 100%"
                    v-model="form.water_position"
                    filterable
                    clearable
                    placeholder="请选择"
                  >
                    <el-option
                      key="0"
                      label="左上角水印"
                      :value="'1'"
                    ></el-option>
                    <el-option
                      key="1"
                      label="上居中水印"
                      :value="'2'"
                    ></el-option>
                    <el-option
                      key="2"
                      label="右上角水印"
                      :value="'3'"
                    ></el-option>
                    <el-option
                      key="3"
                      label="左居中水印"
                      :value="'4'"
                    ></el-option>
                    <el-option
                      key="4"
                      label="居中水印"
                      :value="'5'"
                    ></el-option>
                    <el-option
                      key="5"
                      label="右居中水印"
                      :value="'6'"
                    ></el-option>
                    <el-option
                      key="6"
                      label="左下角水印"
                      :value="'7'"
                    ></el-option>
                    <el-option
                      key="7"
                      label="下居中水印"
                      :value="'8'"
                    ></el-option>
                    <el-option
                      key="8"
                      label="右下角水印"
                      :value="'9'"
                    ></el-option>
                  </el-select>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="水印透明度" prop="water_alpha">
                  <el-slider v-model="form.water_alpha"></el-slider>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="绑定域名" prop="domain">
                  <el-input
                    v-model="form.domain"
                    autoComplete="off"
                    clearable
                    placeholder="请输入绑定域名"
                  ></el-input>
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>
          <el-tab-pane
            style="padding-top: 10px"
            label="协议配置"
            name="协议配置"
          >
            <el-row>
              <el-col :span="24">
                <el-form-item label="服务协议" prop="service">
                  <TinymceEditor
                    tinymceId="service"
                    v-model:tinymceContent="form.service"
                  ></TinymceEditor>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="隐私政策" prop="privacy">
                  <TinymceEditor
                    tinymceId="privacy"
                    v-model:tinymceContent="form.privacy"
                  ></TinymceEditor>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="关于我们" prop="about">
                  <TinymceEditor
                    tinymceId="about"
                    v-model:tinymceContent="form.about"
                  ></TinymceEditor>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="技师服务协议" prop="technician_tcp">
                  <TinymceEditor
                    tinymceId="technician_tcp"
                    v-model:tinymceContent="form.technician_tcp"
                  ></TinymceEditor>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="用户使用说明" prop="user_instructions">
                  <TinymceEditor
                    tinymceId="user_instructions"
                    v-model:tinymceContent="form.user_instructions"
                  ></TinymceEditor>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="技师隐私协议" prop="tech_privacy">
                  <TinymceEditor
                    tinymceId="tech_privacy"
                    v-model:tinymceContent="form.tech_privacy"
                  ></TinymceEditor>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24">
                <el-form-item label="技师用户协议" prop="tech_instructions">
                  <TinymceEditor
                    tinymceId="tech_instructions"
                    v-model:tinymceContent="form.tech_instructions"
                  ></TinymceEditor>
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>
          <el-tab-pane style="padding-top: 10px" label="公告" name="公告">
            <el-row>
              <el-col :span="24">
                <el-form-item label="公告" prop="tips">
                  <TinymceEditor
                    tinymceId="tips"
                    v-model:tinymceContent="form.tips"
                  ></TinymceEditor>
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>
        </el-tabs>
        <el-form-item>
          <el-button size="small" type="primary" @click="submit"
            >保存设置</el-button
          >
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>
<script>
import { index, getInfo } from "@/api/admin/baseconfig";
import ImagesUpload from "@/components/common/ImagesUpload.vue";
import TinymceEditor from "@/components/editor/TinymceEditor.vue";
import Tag from "@/components/common/Tag.vue";
export default {
  name: "baseconfigindex",
  components: {
    ImagesUpload,
    TinymceEditor,
    Tag,
  },
  data() {
    return {
      form: {
        site_title: "",
        logo: "",
        keyword: [],
        description: "",
        copyright: "",
        filesize: "0",
        filetype: "",
        water_status: 1,
        domain: "",
        service: "",
        privacy: "",
        about: "",
        technician_tcp: "",
        tips: "",
        user_instructions: "",
        tech_privacy: "",
        tech_instructions: "",
      },
      loading: false,
      activeName: "基本信息",
      mobile: "",
      qr_code: "",
      rate: 0,
      technician_rate: 0,
      //微信有关
      app_id: "",
      app_secret: "",
      mch_id: "",
      mch_secret: "",
      wx_app_id: "",
      wx_app_secret: "",
      //短信配置
      templateCode: "",
      signName: "",
      accessKeyId: "",
      accessKeySecret: "",
      //oss
      bucket: "",
      endpoint: "",
      ossaccessKeyId: "",
      ossaccessKeySecret: "",
      first: "",
      second: "",
      //紧急联系电话
      urgent_mobile: "",
      //技师分成等级
      tglist: [],
      rules: {},
    };
  },
  mounted() {
    getInfo().then((res) => {
      if (res.status == 200) {
        this.form = JSON.stringify(res.data) == "[]" ? {} : res.data;
        var customer_service = JSON.parse(res.data.customer_service);
        var refund_config = JSON.parse(res.data.refund_config);
        var wx_config = JSON.parse(res.data.wx_config);
        var sms = JSON.parse(res.data.sms);
        var oss = JSON.parse(res.data.oss);
        var brokerage = JSON.parse(res.data.brokerage);
        var umobile = { urgent_mobile: "" };
        if (res.data.umobile) umobile = JSON.parse(res.data.umobile);
        //技师等级
        if (res.data.tglist != '' && res.data.tglist!='[]') {
          this.tglist = JSON.parse(res.data.tglist);
        } else {
          this.tglist = [{ value: "" }];
        }

        //佣金比例
        this.first = brokerage.first;
        this.second = brokerage.second;
        //oss设置
        this.bucket = oss.bucket;
        this.endpoint = oss.endpoint;
        this.ossaccessKeyId = oss.accessKeyId;
        this.ossaccessKeySecret = oss.accessKeySecret;

        this.mobile = customer_service.mobile;
        this.qr_code = customer_service.qr_code;
        this.rate = refund_config.rate;
        this.technician_rate = refund_config.technician_rate;
        //微信相关

        this.app_id = wx_config.app_id;
        this.app_secret = wx_config.app_secret;
        this.mch_id = wx_config.mch_id;
        this.mch_secret = wx_config.mch_secret;
        this.wx_app_id = wx_config.wx_app_id;
        this.wx_app_secret = wx_config.wx_app_secret;
        //短信
        this.templateCode = sms.templateCode;
        this.signName = sms.signName;
        this.accessKeyId = sms.accessKeyId;
        this.accessKeySecret = sms.accessKeySecret;

        //紧急联系电话
        this.urgent_mobile = umobile.urgent_mobile;

        this.setDefaultVal("keyword");
      }
    });
  },
  methods: {
    removeTg(index) {
      this.tglist.splice(index, 1);
    },
    addTg() {
      this.tglist.push({ value: "" });
    },
    submit() {
      this.$refs["form"].validate((valid) => {
        if (valid) {
          this.loading = true;

          //佣金比例
          var brokerage = {
            first: this.first,
            second: this.second,
          };
          this.form.brokerage = JSON.stringify(brokerage);
          //佣金比例
          var customer_service = {
            mobile: this.mobile,
            qr_code: this.qr_code,
          };
          this.form.customer_service = JSON.stringify(customer_service);

          //退款设置
          var refund_config = {
            time: 120,
            rate: this.rate,
            technician_rate: this.technician_rate,
          };
          this.form.refund_config = JSON.stringify(refund_config);
          //微信设置
          var wx_config = {
            status: 1,
            app_id: this.app_id,
            app_secret: this.app_secret,
            mch_id: this.mch_id,
            mch_secret: this.mch_secret,
            wx_app_id: this.wx_app_id,
            wx_app_secret: this.wx_app_secret,
          };
          this.form.wx_config = JSON.stringify(wx_config);
          //短信设置
          var sms = {
            templateCode: this.templateCode,
            signName: this.signName,
            accessKeyId: this.accessKeyId,
            accessKeySecret: this.accessKeySecret,
          };
          this.form.sms = JSON.stringify(sms);

          //短信设置
          var oss = {
            status: 1,
            bucket: this.bucket,
            endpoint: this.endpoint,
            accessKeyId: this.ossaccessKeyId,
            accessKeySecret: this.ossaccessKeySecret,
          };
          this.form.oss = JSON.stringify(oss);

          //短信设置
          var brokerage = {
            first: this.first,
            second: this.second,
          };
          this.form.brokerage = JSON.stringify(brokerage);

          var umobile = {
            urgent_mobile: this.urgent_mobile,
          };
          this.form.umobile = JSON.stringify(umobile);

          this.form.tglist = JSON.stringify(this.tglist);

          index(this.form)
            .then((res) => {
              if (res.status == 200) {
                this.$message({ message: "操作成功", type: "success" });
              }
            })
            .catch(() => {
              this.loading = false;
            });
        }
      });
    },
    setDefaultVal(key) {
      if (this.form[key] == null || this.form[key] == "") {
        this.form[key] = [];
      }
    },
  },
};
</script>
<style scoped lang="scss">
@import "@/assets/scss/common.scss";
.sub-title {
  color: #999;
}
.title-wrp {
  color: #f56c6c;
}
</style>
