<template>
  <v-menu offset-y>
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">...</span>
    </template>
    <v-list>
      <v-list-item class="pointer" @click="editAt">
        <v-list-item-title>Edit</v-list-item-title>
      </v-list-item>
      <v-list-item class="pointer" @click="deleteAt">
        <v-list-item-title>Delete</v-list-item-title>
      </v-list-item>
      <slot></slot>
    </v-list>
  </v-menu>
</template>
<script>
export default {
  props: {},
  methods: {
    editAt(evt) {
      this.$emit("editAt", evt);
    },
    deleteAt(evt) {
      this.$emit("deleteAt", evt);
    },
  },
};
</script>