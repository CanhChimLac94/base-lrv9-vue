<template>
  <v-container>
    <v-row>
      <v-col cols="12 text-center" v-if="!toplistDetail.id">
        <v-skeleton-loader
          class="mx-auto"
          max-width="300"
          type="card"
        ></v-skeleton-loader>
      </v-col>

      <v-col cols="12" lg="6" offset-lg="3" v-else>
        <v-row class="post-header">
          <v-col cols="12" sm="6" md="5" lg="5">
            <div class="img-news img-post">
              <img
                :src="toplistDetail.thumb"
                :alt="`Top ${toplistDetail.top} ${toplistDetail.title}`"
              />
            </div>
          </v-col>
          <v-col cols="12" sm="6" md="7" lg="7">
            <h2 class="post-title title-new">
              Top
              <span class="top-number">{{ toplistDetail.top }}</span>
              <span class="top-title">{{ toplistDetail.title }}</span>
            </h2>
            <div class="post-info">
              <span class="post-date">
                <span class="fa fa-clock-o"></span>
                {{ getDate(toplistDetail.created_at) }}
              </span>
            </div>
            <div
              class="post-description"
              v-html="toplistDetail.description"
            ></div>
          </v-col>
        </v-row>
        <v-row class="list-contents">
          <v-col cols="12">
            <v-timeline dense reverse>
              <v-timeline-item
                v-for="(item, i) in listItemContent"
                :key="i"
                :color="item.color"
                :id="`tiem_top_${i}`"
                :dense="$vuetify.breakpoint.smAndDown"
                dark
              >
                <!-- left -->
                <template v-slot:icon>
                  <span class="top-number-white" v-html="i + 1"></span>
                </template>
                <v-card dark :color="item.color">
                  <v-card-title class="text-h6">
                    <h4>
                      Top
                      <span class="top-number" v-html="`${i + 1}: `"></span>
                      <span class="top-title" v-html="item.title"></span>
                    </h4>
                  </v-card-title>
                  <v-card-text class="white text--primary">
                    <div v-html="item.content"></div>
                  </v-card-text>
                </v-card>
              </v-timeline-item>
            </v-timeline>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <div class="pagination d-flex">
      <!-- <v-container>
        <v-row>
          <v-col
            cols="12"
            md="10"
            lg="8"
            offset-md="1"
            offset-lg="2"
            class="text-center d-flex"
          > -->
      <v-slide-group multiple show-arrows class="pagination-content">
        <v-slide-item v-for="(item, index) in listItemContent" :key="index">
          <v-btn
            :color="item.color"
            @click="goto(`#tiem_top_${index}`)"
            class="mx-2"
            dark
            fab
            x-small
          >
            <span v-html="index + 1"></span>
          </v-btn>
        </v-slide-item>
      </v-slide-group>
      <!-- </v-col>
        </v-row>
      </v-container> -->
    </div>
  </v-container>
</template>
<style lang="scss">
.list-contents .top-number {
  padding: 0px 5px;
}
.list-contents .top-number-white {
  color: #fafafa;
}
.list-contents .top-title {
  color: inherit;
}
.post-header .img-news img {
  max-width: 100%;
}
.pagination {
  z-index: 3;
}

.pagination-content {
  background-color: rgba(125, 125, 125, 0.7);
  transform: rotateZ(-90deg);
  transform-origin: 90% 100%;
  border-radius: 25px;
  position: fixed !important;
  margin: auto;
  max-width: 70vh !important;
  padding: 10px;
  z-index: 10;
  /* left: 10px; */
  top: 10vh;
  right: 0px;
}
.pagination-content button {
  transform: rotate(90deg);
}
.pagination-content .v-slide-group__next .v-icon,
.pagination-content .v-slide-group__prev .v-icon {
  color: #fff !important;
}
</style>
<script>
import defaultService from "../default.service";
export default {
  mixins: [defaultService],
  head: {
    title: null,
    meta: null,
  },
  data() {
    return {
      toplist_title: this.$route.params.title || "",
      listItemContent: [],
      dialShare: true,
    };
  },
  methods: {
    init() {
      let title = this.$route.params.title.replace(".html", "");
      this.toplist_id = title.substring(title.lastIndexOf(".") + 1);
      this.loadToplistDetail(this.toplist_id).then(() => {
        this.listItemContent = this.formatToplistContent();
      });
    },
    goto(target) {
      this.$vuetify.goTo(target, {
        duration: 500,
        offset: 50,
        easing: "linear",
      });
    },
    clickOutSide() {
      this.dialShare = true;
    },
  },
  mounted() {
    this.init();
  },
};
</script>