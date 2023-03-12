<template>
  <v-flex class="float-right mt-4">
    <v-menu
      class="search-content-result ma-auto"
      v-model="isSearching"
      max-width="inherit"
      eager
      centered
      offset-y
      offset-overflow
      allow-overflow
      bottom
      left
    >
      <template v-slot:activator="{ on }">
        <v-text-field
          v-on="on"
          @click="startSearch"
          @input="searching"
          v-model="searchKey"
          placeholder="search..."
          append-icon="mdi-magnify"
          rounded
          outlined
          flat
          solo
          dense 
          background-color="rgba(255, 255, 255, 0.5)"
        ></v-text-field>
      </template>
      <v-list class="search-result d-none1">
        <v-list-item
          v-for="(item, index) in searchData"
          @click="endSearch"
          :key="index"
          :to="item.to"
          router
        >
          <v-list-item-content>
            <v-list-item-title
              class="wrap-text"
              v-text="item.title"
            ></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-flex>
</template>
<style>
.wrap-text {
  white-space: normal;
  -webkit-line-clamp: 2;
  display: -webkit-box;
}
</style>
<script>
import defaultService from "../default.service";
export default {
  mixins: [defaultService],
  data() {
    return {
      searchData: [],
      searchKey: "",
      isSearching: false,
    };
  },
  methods: {
    startSearch() {
      this.searchData = [];
    },
    async searching() {
      this.isSearching = true;
      this.searchData = await this.searchNews(this.searchKey);
    },
    endSearch() {
      this.searchKey = "";
    },
  },
};
</script>