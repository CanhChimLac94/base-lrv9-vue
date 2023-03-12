<template >
  <div v-if="!menu"></div>
  <v-list-item
    v-else-if="!menu.subs"
    :to="menu.subs ? '#' : menu.to"
    router
    exact
  >
    <v-list-item-action>
      <v-icon>{{ menu.icon }}</v-icon>
    </v-list-item-action>
    <v-list-item-content>
      <v-list-item-title v-text="menu.title" />
    </v-list-item-content>
  </v-list-item>

  <v-list-group v-else :value="false" no-action>
    <template v-slot:prependIcon>
      <v-icon v-text="menu.icon"></v-icon>
    </template>
    <template v-slot:activator>
      <v-list-item-title>{{ menu.title }}</v-list-item-title>
    </template>

    <admin-share-menu-item
      v-for="(item, i) in menu.subs"
      :key="i"
      :menu="item"
      :isSub="true"
    />
  </v-list-group>
</template>
<script>
export default {
  props: {
    menu: {
      type: Object,
      required: true,
    },
    isSub: {
      type: Boolean,
      default: false,
    },
  },
};
</script>