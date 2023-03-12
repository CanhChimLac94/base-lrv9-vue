<template>
  <v-menu :close-on-content-click="false" offset-y v-model="open">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <slot />
      </div>
    </template>
    <v-list>
      <admin-topic-menu-item
        v-for="(item, index) in topics"
        :key="index"
        :topic="item"
        @onSelect="selected($event)"
      />
    </v-list>
  </v-menu>
</template>
<script>
export default {
  props: {
    topics: {
      type: Array,
      default: [],
    },
  },
  data() {
    return {
      open: false,
    };
  },
  methods: {
    selected(topic) {
      this.open = false;
      this.$emit("onSelect", topic);
    },
    menuOpened() {
      return !this.open;
    },
  },
};
</script>