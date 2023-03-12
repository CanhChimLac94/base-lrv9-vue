<template>
  <v-row class="menus-head" v-scroll="onScroll">
    <v-col cols="12">
      <v-app-bar :fixed="isFixedAppBar" flat rounded="lg">
        <v-toolbar-items class="hidden-sm-and-down main-menu1" v-resize="onResize" flat>
          <client-share-menu-item v-for="(menu, im) in menus" :key="im" :menu="menu" />
          <v-spacer />
          <v-menu transition="scale-transition" offset-y>
            <template v-slot:activator="{ on }" text>
              <v-btn dark icon v-on="on" aria-label="account">
                <v-icon color="grey">mdi-account</v-icon>
              </v-btn>
            </template>
            <v-list>
              <v-list v-if="auth">
                <v-btn :to="'/admin/news'" router text>Đăng tin</v-btn>
              </v-list>
              <v-list v-if="auth">
                <v-btn :to="'/account/logout'" router text>Đăng xuất</v-btn>
              </v-list>
              <v-list v-else>
                <v-btn :to="'/account/login'" router text>Đăng nhập</v-btn>
              </v-list>
            </v-list>
          </v-menu>
        </v-toolbar-items>

        <v-toolbar-items class="hidden-md-and-up">
          <v-menu :close-on-content-click="false" offset-y>
            <template v-slot:activator="{ on }">
              <v-app-bar-nav-icon v-on="on"></v-app-bar-nav-icon>
            </template>
            <v-list>
              <client-share-menu-item-sub v-for="(menu, im) in menus" :key="im" :menu="menu" />

              <v-list-item v-if="auth" :to="'/admin/news'" text>Đăng tin</v-list-item>
              <v-list-item v-if="auth" :to="'/account/logout'" text>Đăng xuất</v-list-item>
              <v-list-item v-else :to="'/account/login'" text>Đăng nhập</v-list-item>
            </v-list>
          </v-menu>
          <v-spacer />
          <v-card class="toolbar-search" outlined>
            <client-share-search />
          </v-card>
        </v-toolbar-items>
      </v-app-bar>
    </v-col>
  </v-row>
</template>

<style>
.row.menus-head {
  margin-top: 0px;
}

.row.menus-head .v-toolbar__items {
  width: 100%;
}

.row.menus-head .toolbar-search,
.toolbar-search .search {
  margin-top: 0px;
  border: none;
}
</style>

<script>
import { mapState } from "vuex";
import defaultService from "../default.service";
import ClientShareMenuItem from "@/Components/client/share/menuItem.vue";
import ClientShareSearch from "@/Components/client/share/search.vue";
import ClientShareMenuItemSub from "@/Components/client/share/menuItemSub.vue";

export default {
  components: {
    ClientShareMenuItem,
    ClientShareSearch,
    ClientShareMenuItemSub,

  },
  mixins: [defaultService],
  data() {
    return {};
  },
  computed: {
    ...mapState({
      menus: (state) => state.menus || [],
      auth: (state) => state.auth.user || false,
    }),
  },
  methods: {
    onResize() {
      this.reloadMenu();
    },
  },
  mounted() {
    this.loadTopics();
    this.loadTags();
  },
};
</script>