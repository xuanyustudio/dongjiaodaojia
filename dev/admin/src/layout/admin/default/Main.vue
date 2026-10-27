<template>
  <el-container :class="classObj" id="aside">
    <div v-if="device==='mobile'&&sidebar.opened" class="drawer-bg" @click="handleClickOutside"/>
    <Side class="sidebar-container" />
    <el-container class="main-container">
      <el-header v-if="!dialog_url" class="header-container">
        <Head/>
      </el-header>
      <el-main id="main" :style="{backgroundColor:main_background_color}">
        <TagView v-if="showtags && !dialog_url"/>
        <router-view></router-view>
      </el-main>
    </el-container>
  </el-container>
</template>
<script>
import { mapState } from 'vuex'
import Head from './Head.vue'
import Side from './Side.vue'
import TagView from './TagView.vue'
import ResizeMixin from './ResizeHandle.js'
export default {
  name:'Main',
  components: { Head,Side,TagView},
  computed: {
    ...mapState({
      device: state => state.app.device,
      sidebar: state => state.app.sidebar,
      showtags: state => state.setting.tagsView,
    }),
    classObj() {
      return {
        mobile: this.device === 'mobile',
        hideSidebar: !this.sidebar.opened,
        openSidebar: this.sidebar.opened,
        withoutAnimation: this.sidebar.withoutAnimation,
      }
    },
  },
  data(){
    return{
      main_background_color:'#F2F6FC',
      dialog_url:this.$route.query.dialog_url,
    }
  },
  mixins: [ResizeMixin],
  methods: {
    handleClickOutside() {
      this.$store.dispatch('closeSideBar', { withoutAnimation: false })
    }
  },
};
</script>
<style scoped>
.el-header{padding: 0 0 0 20px;}
.drawer-bg {
    background: #000;
    opacity: 0.3;
    width: 100%;
    top: 0;
    height: 100%;
    position: absolute;
    z-index: 999;
  }
#aside {
  height: 100vh;
}
#main {
  height: 100vh;
  overflow: auto;
}
/*滚动条的宽度*/
::-webkit-scrollbar {
  width: 9px;
  height: 9px;
}

/*外层轨道。可以用display:none让其不显示，也可以添加背景图片，颜色改变显示效果*/
::-webkit-scrollbar-track {
  width: 6px;
  background-color: #f2f6fc;
  -webkit-border-radius: 2em;
  -moz-border-radius: 2em;
  border-radius: 2em;
}

/*滚动条的设置*/

::-webkit-scrollbar-thumb {
  background-color: #999;
  background-clip: padding-box;
  min-height: 1px;
  -webkit-border-radius: 2em;
  -moz-border-radius: 2em;
  border-radius: 2em;
}
/*滚动条移上去的背景*/

::-webkit-scrollbar-thumb:hover {
  background-color: #fff;
}


.el-main{padding: 15px 15px 15px 15px}
</style>