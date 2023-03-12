<template>
  <v-container>
    <v-row>
      <v-col cols="12 text-center" v-if="!toplist || toplist.length <= 0">
        <v-skeleton-loader
          class="mx-auto"
          max-width="300"
          type="card"
        ></v-skeleton-loader>
      </v-col>
      <v-col
        cols="6"
        lg="3"
        md="3"
        sm="4"
        v-for="(item, index) in toplist"
        :key="index"
      >
        <v-card
          class="mx-auto"
          max-width="400"
          router
          :to="`/toplist/${urlFormat(item.title)}.${item.id}.html`"
          tag="a"
        >
          <v-img class="white--text align-end" height="200px" :src="item.thumb">
            <v-card-title class="card-top-title">
              Top
              <span class="top-number">{{ item.top }}</span>
              <span class="top-title">{{ item.title }}</span>
            </v-card-title>
          </v-img>

          <v-card-subtitle class="pb-0">
            {{ getDate(item.updated_at) }}
          </v-card-subtitle>

          <v-card-text class="text--primary">
            <div v-html="item.description"></div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
<style>
.card-top-title {
  background-color: rgba(0, 0, 0, 0.6);
  color: #fafafa;
}
.card-top-title .top-number {
  padding: 0px 5px;
}
.card-top-title .top-title {
  color: #f5f5f5;
}
</style>
<script>
import defaultSevice from "../default.service";
export default {
  mixins: [defaultSevice],
  mounted() {
    this.loadTopList();
  },
};
</script>