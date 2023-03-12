<template>
  <v-list-item v-if="topic.has_sub <= 0" @click="selected(topic)">
    <v-list-item-title>{{ topic.name }}</v-list-item-title>
  </v-list-item>
  <v-list-group v-else no-action :sub-group="supGroup" :prepend-icon="''">
    <template v-slot:activator>
      <v-list-item-title>{{ topic.name }}</v-list-item-title>
    </template>
    <v-list class="subs-topic" flat>
      <admin-topic-menu-item
        v-for="(item, index) in topic.subs"
        :key="index"
        :topic="item"
        @onSelect="selected($event)"
      />
    </v-list>
  </v-list-group>
</template>
<style>
.subs-topic{
  padding-left: 15px;
}
</style>
<script>
export default {
  props: {
    topic: {
      type: Object,
      default: {},
    },
    supGroup: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    selected(topic) {
      this.$emit("onSelect", topic);
    },
  },
};
</script>