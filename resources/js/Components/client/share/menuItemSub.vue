<template>
  <v-list-item small v-if="menu.items.length <= 0" :to="menu.to" router>
    <v-list-item-content>
      <v-list-item-title v-text="menu.title" />
    </v-list-item-content>
  </v-list-item>
  <v-list-group v-else no-action>
    <template v-slot:activator>
      <v-list-item-title small>{{ menu.title }}</v-list-item-title>
    </template>
    <v-list class="subs-menu" flat>
      <client-share-menu-item-sub v-for="(m, mindex) in menu.items" :menu="m" :key="mindex" />

    </v-list>
  </v-list-group>
</template>
<style>
.subs-menu{
  padding-left: 15px;
}
</style>
<script>
// import ClientShareMenuItemSub from "@/Components/client/share/menuItemSub.vue";

export default {
  name: "ClientShareMenuItemSub",
  components: {
    // ClientShareMenuItemSub
  },
  props: {
    menu: {
      type: Object,
      default: {}
    }
  }
}
</script>