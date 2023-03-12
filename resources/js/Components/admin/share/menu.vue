<template>
  <v-list>
    <div class="text-left">
      <v-btn @click="loadMenus" depressed text fab>
        <v-icon>mdi-sync</v-icon>
      </v-btn>
    </div>
    <admin-share-menu-item v-for="(item, i) in menus" :key="i" :menu="item" />
  </v-list>
</template>
<script>
import managerService from "./manager.service";
export default {
  mixins: [managerService],
  mounted() {
    this.loadMenus();
  },
};
</script>