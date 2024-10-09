<!-- eslint-disable -->
<template>
    <div>
        <div class="page-row">
            <div class="sidebar-navigation">
                <div class="sidebar-inner">
                    <div class="el-width-1-1">
                        <el-menu class="el-menu--transparent" :router="false" :collapse="isCollapsed">
                            <el-menu-item index="menu" @click="switchMenu">
                                <i class="el-icon-s-data el-rotate-90" />
                         <span slot="title">Menu</span>
                            </el-menu-item>
                        </el-menu>
                    </div>

                    <div class="el-margin">
                        <el-menu class="page-menu el-menu--transparent" :collapse="isCollapsed" :router="true" :default-active="$route.name">

                            <template v-for="item in sortedMenuItems" class="el-menu-item">
                                <el-submenu v-if="item.sub_menu" :index="item.index">
                                    <template slot="title">
                                        <i :class="item.icon"></i>
                                        <span>{{ item.title }}</span>
                                    </template>
                                    <el-menu-item v-for="menuItem in item.sub_menu" :key="menuItem.index" :index="menuItem.index" :route="{ name: menuItem.route}">
                                        {{ menuItem.title }}
                                    </el-menu-item>
                                </el-submenu>
                                <el-menu-item v-else :index="item.index" :route="{name: item.route}">
                                    <i :class="item.icon"></i>
                                    <span slot="title">{{ item.title }}</span>
                                </el-menu-item>
                            </template>
                            <!--
                          <el-menu-item index="orders" :route="{name: 'orders'}">
                              <i class="el-icon-shopping-bag-1"></i>
                              <span slot="title">Orders dashboard</span>
                          </el-menu-item>

                       <el-menu-item index="fb-stat" :route="{name: 'fb-stat'}">
                           <i class="el-icon-shopping-cart-1"></i>
                           <span slot="title">Facebook statistic</span>
                       </el-menu-item>

                    <el-menu-item index="company-overview" :route="{name: 'company-overview'}">
                        <i class="el-icon-pie-chart"></i>
                        <span slot="title">Company overview</span>
                    </el-menu-item>

                  <el-menu-item index="product-statistic" :route="{name: 'product-statistic'}">
                      <i class="el-icon-data-analysis" />
                      <span slot="title">Product Statistic</span>
                  </el-menu-item>

                  <el-menu-item index="kpi-overview" :route="{name: 'kpi-overview'}">
                    <i class="el-icon-data-line" />
                    <span slot="title">KPI</span>
                  </el-menu-item>
                  <el-menu-item index="store-today" :route="{name: 'store-today'}">
                      <i class="el-icon-s-marketing" />
                      <span slot="title">Store Today</span>
                  </el-menu-item>
-->
                        </el-menu>

                        <div class="el-width-1-1" style="margin-top: 5vh">
                            <el-menu class="el-menu--transparent" :collapse="isCollapsed">
                                <el-menu-item index="logout" @click="logout">
                                    <i class="el-icon-download el-rotate-90" />
                                    <span slot="title">Logout</span>
                                </el-menu-item>
                            </el-menu>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-content">
                <app-header/>
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<!-- eslint-disable -->
<script>
import AppHeader from './AppHeader';
import { mapActions, mapGetters } from 'vuex';

let dashboardMenus = [

];

/// Register menus from components/{name}/menus.js
let menus = require.context('@/element/components/', true, /menus.js/);
menus.keys().forEach((filename) => {
   dashboardMenus.push(require(`@/element/components/${filename.replace('./', '')}`).default);
});

export default {
    name: 'Main',

    components: {
        AppHeader,
    },

    data() {
        return {
            menuItems: dashboardMenus
        }
    },

    methods: {
        ...mapActions({
            switchMenu: 'settings/switchMenu',
        }),

        logout() {
            this.$auth.logout({
                redirect: '/login',
            });
        },
    },

    computed: {
        ...mapGetters({
            isCollapsed: 'settings/menuIsCollapsed',
            isMenuCollapsed: 'settings/menuIsCollapsed',
        }),

        sortedMenuItems() {
            return this.menuItems.sort((a,b) => (a.order > b.order) ? 1 : ((b.order > a.order) ? -1 : 0))
        }
    },
}
</script>

<style>

.el-submenu [class^="el-icon-"] {
    vertical-align: middle;
    margin-right: 5px;
    width: 24px;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    color: white;
}

</style>
