<template>
  <div class="row">
    <div class="col-12 text-left main-actions">
      <v-app-bar class="actions-head" app>
        <v-col cols="1" md="2">
          <v-select
            :items="[10, 25, 50, 100]"
            v-model="countDisplay"
            :hint="'records'"
            label="Hiển thị"
            single-line
            @input="getList"
          ></v-select>
        </v-col>
        <v-spacer />
        <v-btn v-if="!isEdit" @click="addItem()">Add</v-btn>
        <v-btn v-if="isEdit" @click="save()"> Save </v-btn>
        <v-btn v-if="isEdit" @click="closeEdit()"> X </v-btn>
      </v-app-bar>
    </div>
    <div class="col-12 list-data" v-if="!isEdit">
      <div class="text-center" v-if="isLoading">
        <v-progress-circular indeterminate color="green"></v-progress-circular>
      </div>
      <div>
        <slot name="data_table"></slot>
      </div>
      <div class="text-center">
        <v-app-bar>
          <p>
            Hiển thị {{ startItemView }} tới {{ endItemView }} của {{ totalItem }} mục
          </p>
          <v-spacer />
          <v-pagination
            v-model="curentPage"
            :length="totalPage"
            :total-visible="4"
            @input="nextPage"
          ></v-pagination>
        </v-app-bar>
      </div>
    </div>
    <div class="col-12 from-edit" :class="isEdit ? '' : 'd-none'">
      <slot name="from_edit"></slot>
      <v-row>
        <v-col>
          <v-app-bar class="actions-footer">
            <v-spacer />
            <v-btn @click="save()"> Save </v-btn>
          </v-app-bar>
        </v-col>
      </v-row>
    </div>
  </div>
</template>
<style >
.actions-head,
.actions-footer {
  background-color: rgba(0, 0, 0, 0) !important;
}
.actions-head {
  margin-left: 50px;
}
</style>
<script>
import managerService from "./manager.service";

export default {
  mixins: [managerService],
  computed: {},
  props: {
    columns: {
      type: Array,
      default: [],
    },
  },
  data() {
    return {};
  },
  methods: {
    init() {
      // this.load_topics();
      // this.getList(this.getListDone);
    },
  },
  mounted() {
    // this.init();
  },
};
</script>
