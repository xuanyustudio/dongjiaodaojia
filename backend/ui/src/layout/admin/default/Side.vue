<template>
  <el-aside id="aside" style="overflow-x: hidden;overflow-y: hidden">
    <el-scrollbar class="scrollbar-wrapper" style="height:100%;">
      <div>
      <div style="height:60px;color:#8fa6bf;text-indent:20px;border-bottom:1px solid #192a3f;padding-top:10px;">
        <p style="line-height:60px;" v-if="!isCollapse">{{site_title}}</p>
        <el-image v-else src="./static/images/vue.png" style="width:30px; height:30px;margin-top:15px;"></el-image>
      </div>
      <el-menu class="el-menu-vertical-demo" router :default-active="activie_index" :unique-opened="true" :background-color="aside.aside_background_color"  text-color="#8fa6bf"  :active-text-color="aside.aside_active_text_color"  :collapse="isCollapse">
        <template v-for="item in menu">
          <el-submenu v-if="item.children && item.children.length" :index="item.path" :key="item.name">
            <template slot="title"><i :class="item.icon"></i><span>{{item.title}}</span></template>
            <!-- 二级菜单 -->
            <template v-for="itemChild in item.children">
              <el-submenu v-if="itemChild.children && itemChild.children.length" :index="itemChild.path" :key="itemChild.name">
                  <template slot="title"><i :class="itemChild.icon"></i><span>{{itemChild.title}}</span></template>
                  <!-- 三级菜单 -->
                  <el-menu-item v-for="itemChild_Child in itemChild.children" :index="itemChild_Child.path" :key="itemChild_Child.name">
                  <i :class="itemChild_Child.icon"></i><span slot="title">{{itemChild_Child.title}}</span></el-menu-item>
              </el-submenu>
              <el-menu-item v-else :index="itemChild.path" :key="itemChild.name"><i :class="itemChild.icon"></i><span slot="title">{{itemChild.title}}</span></el-menu-item>
            </template>
          </el-submenu>
          <el-menu-item v-else :index="item.path" :key="item.name"><i :class="item.icon"></i><span slot="title">{{item.title}}</span></el-menu-item>
        </template>
      </el-menu>
      </div>
    </el-scrollbar>
  </el-aside>
</template>
<script>
import{ mapGetters } from 'vuex'
export default {
  name:'Side',
  computed: {
    ...mapGetters(['menu','sidebar','site_title']),
    isCollapse() {
      return !this.sidebar.opened
    }
  },
  data() {
    return {
      activie_index: this.$route.path,
      aside: {
        aside_background_color: '#121e2d',
        aside_active_text_color: '#FFF',
      },
    }
  },
  watch:{
    $route(){
      this.activie_index = this.$route.path
    }
  },
};
</script>
<style lang="scss" scoped>
.horizontal-collapse-transition { transition: 0s width ease-in-out, 0s padding-left ease-in-out, 0s padding-right ease-in-out;}  
.el-menu {border-right: 0 !important;}
#aside::-webkit-scrollbar {display: none;}
#aside {height: 100vh; overflow-x: hidden;overflow-y: scroll;}
::v-deep.el-menu {
    background-color: transparent;
    .el-menu-item {
      color: #8fa6bf;
      height: 45px;
      line-height: 45px;
      i {
        color: #8fa6bf;
      }
    }
    .el-submenu__title {
      color: #8fa6bf;
      height: 45px;
      line-height: 45px;
      i {
        color: #8fa6bf;
      }
    }
}
.is-active{
  background-color: #0576b9!important;
  i {
        color: #FFF;
  }
}
</style>