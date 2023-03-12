<template>
  <v-row>
    <v-col cols="12" class="contain-other-new">
      <h2 class="text-center" v-html="title"></h2>
      <br />
      <v-skeleton-loader
        v-if="!datas || datas.length <= 0"
        class="mx-auto"
        max-width="300"
        type="card"
      ></v-skeleton-loader>
      <v-tooltip
        v-for="(item, index) in datas"
        :key="index"
        allow-overflow
        bottom
      >
        <template v-slot:activator="{ on, attrs }">
          <v-row
            class="pointer orther-new-item grey--text"
            v-bind="attrs"
            v-on="on"
            :href="getToLink(item)"
            tag="a"
            dark
            router
          >
            <!-- :href="getToLink(item)" -->
            <v-col cols="5" lg="5" class="col-xxs-12 px-0">
              <v-img
                class="other-news-img"
                :lazy-src="getThumb(item.img_path)"
                :src="getThumb(item.img_path)"
              ></v-img>
            </v-col>
            <v-col cols="7" lg="7" class="col-xxs-12 pt-">
              <!-- <div class="my-auto fill-height"> -->
                <p v-html="item.title" class="text-body-2 my-auto"></p>
                <!-- <v-spacer /> -->
                <p class="text-caption my-auto">
                  <v-icon color="grey lighten-1" small>mdi-clock</v-icon>
                  &nbsp;&nbsp;{{ getDate(item.updated_at) }}
                </p>
              <!-- </div> -->
            </v-col>
          </v-row>
        </template>
        <span v-html="item.title"></span>
      </v-tooltip>
    </v-col>
  </v-row>
</template>
<script>
import defaultService from "../default.service";
export default {
  mixins: [defaultService],
  props: {
    title: {
      type: String,
      require: true,
    },
    datas: {
      type: Array,
      default: [],
    },
  },
  methods: {},
};
</script>