<template>
  <v-app-bar class="actions-head" app flat>
    <v-col cols="1" md="2">
      <v-select
        v-if="!isEdit"
        :items="[10, 25, 50, 100]"
        :hint="'records'"
        label="Hiển thị"
        single-line
        :value="countDisplay"
        @input="getList"
      ></v-select>
    </v-col>
    <v-spacer />
    <span v-html="title"></span>
    <v-spacer />
    <slot></slot>
    <v-btn v-if="!isEdit" @click="addItem()" title="Add" text>
      <v-icon>mdi-plus-circle-outline</v-icon>
    </v-btn>
    <v-btn v-if="!isEdit" @click="getList(countDisplay)" text>
      <v-icon>mdi-refresh</v-icon>
    </v-btn>
    <v-btn v-if="!isEdit" router :to="'/'" text>
      <v-icon>mdi-home</v-icon>
    </v-btn>
    <v-btn v-if="isEdit" @click="save()" title="Save" text>
      <v-icon>mdi-content-save</v-icon>
    </v-btn>
    <v-btn v-if="isEdit" @click="closeEdit()" red text>
      <v-icon>mdi-close</v-icon>
    </v-btn>
  </v-app-bar>
</template>
<style>
.actions-head .v-btn {
  color: #888888;
}
</style>
<script>
export default {
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
    countDisplay: {
      type: Number,
      default: 10,
    },
    value: Number,
    title: {
      type: String,
      default: ''
    },
  },
  methods: {
    addItem() {
      this.$emit("addItem", {});
    },
    save() {
      this.$emit("save", {});
    },
    closeEdit() {
      this.$emit("closeEdit", {});
    },
    getList(evt) {
      this.$emit("countDisplay", evt);
      this.$emit("getList", evt);
    },
  },
};
</script>