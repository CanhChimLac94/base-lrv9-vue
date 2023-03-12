<template>
  <v-list-item small v-if="menu.items.length <= 0 && isSub" :to="menu.to">{{
    menu.title
  }}</v-list-item>
  <!-- <v-list-item small v-else-if="menu.items.length <= 0" :to="menu.to">
    <v-list-item-content>
      <v-list-item-title text>{{ menu.title }}</v-list-item-title>
    </v-list-item-content>
  </v-list-item> -->
  <v-btn small text v-else-if="menu.items.length <= 0" :to="menu.to">
    {{ menu.title }}
  </v-btn>
  <v-menu
    :close-on-content-click="false"
    transition="scale-transition"
    open-on-hover
    bottom
    offset-y
    v-else
  >
    <template v-slot:activator="{ on }">
      <v-btn v-on="on" small text :aria-label="menu.title">{{ menu.title }}</v-btn>
      <!-- <v-list-item small>
        <v-list-content>
        </v-list-content>
      </v-list-item> -->
    </template>
    <v-list>
      <client-share-menu-item-sub
        v-for="(m, mindex) in menu.items"
        :menu="m"
        :key="mindex"
      />
    </v-list>
  </v-menu>
</template>
<script>
import ClientShareMenuItemSub from "@/Components/client/share/menuItemSub.vue";

export default {
  components: {
    ClientShareMenuItemSub,
  },
  props: {
    menu: {
      type: Object,
      default: {},
      require: true,
    },
    isSub: {
      type: Boolean,
      default: false,
    },
  },
  methods: {},
  mounted() {},
};
</script>
