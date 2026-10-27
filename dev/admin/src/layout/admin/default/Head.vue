<template>
  <div class="navbar">
    <div>
        <div class="left-menu">
          <i @click="toggleSideBar" :class="isCollapse?'el-icon-s-fold':'el-icon-s-unfold'"  />
          <i @click="reload()"  class="el-icon-refresh hidden-sm-and-down"></i>
          <div class="breadcrumb  hidden-sm-and-down">
            <Breadcrumb></Breadcrumb>
          </div>
        </div>
        <div class="right-menu">
          <el-tooltip content="清除缓存" effect="dark" placement="bottom">
            <div class="right-menu-item" @click="clearCache()"> 
              <i class="icontool el-icon-delete"></i>
            </div>
          </el-tooltip>
          <el-tooltip :content="!fullscreen?'全屏' : '取消全屏'" effect="dark" placement="bottom">
            <div class="right-menu-item hidden-sm-and-down" @click="change_full_screen()"> 
              <i :class="!fullscreen ? 'icontool el-icon-alizoom' : 'icontool el-icon-alizoomout'"></i>
            </div>
          </el-tooltip>
          <div class="right-menu-item" v-if="show_notice">
            <el-badge is-dot>
              <el-dropdown placement="bottom-start"  @click="getNotice" trigger="click">
                <i class="icontool el-icon-bell" style="font-size:140%"></i>
                <template #dropdown><el-dropdown-menu>
                      <div style="width:250px; height:200px; padding:10px 0; text-indent:10px;">
                          <ul>
                            <li v-for="(item,i) in notice_list" :key="i">
                              <router-link :to="item.url">
                              <svg class="icon" aria-hidden="true">
                                <use xlink:href="#el-icon-alidaichuli"></use>
                              </svg>
                              您有<i>{{item.num}}</i>{{item.title}}
                              </router-link>
                            </li>
                          </ul>
                      </div>
                </el-dropdown-menu></template>
              </el-dropdown>
            </el-badge>
          </div>
          <el-avatar :src="headimg ? headimg : './static/images/head.jpg'"></el-avatar>
          <el-dropdown trigger="click" placement="bottom" style="cursor: pointer;margin-right:15px;">
            <span class="el-dropdown-link">
              {{username}}<i class="icontool el-icon-arrow-down"></i>
            </span>
            <template #dropdown><el-dropdown-menu>
              <el-dropdown-item icon="el-icon-lock" @click.prevent="updatePassword">修改密码</el-dropdown-item>
              <el-dropdown-item icon="el-icon-back" @click.prevent="logout">退出</el-dropdown-item>
            </el-dropdown-menu></template>
          </el-dropdown>
          <div class="right-menu-item"  @click="showdrawer"> 
              <i class="icontool el-icon-alimore"></i>
          </div>
        </div>
    </div>
    <Password v-model:show="passwordDialogStatus"  size="small"></Password>

    <Setting v-model:show="drawer"></Setting>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { confirm } from '@/utils/common'
import { clearCache ,getNotice} from '@/api/admin/base'
import Breadcrumb from "@/components/common/Breadcrumb"
import Password from "@/views/admin/base/password"
import Setting from "./Setting"

export default {
  name:'Head',
  components: {Breadcrumb,Password,Setting},
  computed: {
    ...mapGetters(['username','headimg','device','sidebar','show_notice']),
    isCollapse() {
      return !this.sidebar.opened
    }
  },
  data() {
    return {
      passwordDialogStatus : false,
      fullscreen: false,
      drawer: false,
      notice_list:[],
    };
  },
  methods: {
    toggleSideBar() {
      this.$store.dispatch('toggleSideBar')
    },
    logout(){
      confirm({content:'确定注销并且退出系统?'}).then(()=>{
          this.$store.dispatch('logout').then(()=>{
              this.$message({message: '退出成功', type: 'success'})
              location.href = '/'
          }).catch(() => {})
      })
    },
    clearCache(){
      confirm({content:'确定清除缓存吗?'}).then(()=>{
          clearCache().then(res => {
              if(res.status == '200'){
                this.$message({message: '操作成功', type: 'success'})
              }
          })
      })
    },
    getNotice(){
      getNotice().then(res => {
         this.notice_list = res.data
      })
    },
    reload(){
      this.$router.go(0)
    },
    updatePassword(){
      this.passwordDialogStatus = true
    },
    showdrawer(){
      this.drawer = true
    },
    change_full_screen() {
      //全屏切换函数
      let element = document.documentElement;
      if (this.fullscreen) {
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.webkitCancelFullScreen) {
          document.webkitCancelFullScreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
      } else {
        if (element.requestFullscreen) {
          element.requestFullscreen();
        } else if (element.webkitRequestFullScreen) {
          element.webkitRequestFullScreen();
        } else if (element.mozRequestFullScreen) {
          element.mozRequestFullScreen();
        } else if (element.msRequestFullscreen) {
          // IE11
          element.msRequestFullscreen();
        }
      }
      this.fullscreen = !this.fullscreen; //判断全屏状态
    },
  },
};
</script>

<style scoped lang="scss">
.el-dropdown-menu {
  top: 38px !important;
  li{
    line-height:30px; 
    padding-bottom: 5px; 
    padding-top: 5px; color:#515a6e; 
    font-size:14px; 
    font-weight: 400; 
    border-bottom: 1px solid #EBEEF5;
    cursor: pointer;
    i{font-weight: bold; color: #000;}
    &:hover {
      background: rgb(242, 246, 252);
    }
  
  }
}

.icon {
  width: 1.2em;
  height: 1.2em;
  vertical-align: -0.25em;
  fill: currentColor;
  overflow: hidden;
}


</style>
