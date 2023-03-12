<template>
  <v-col cols="12" md="9" lg="6" offset-lg="2">
    <v-row
      class="new-item pointer"
      v-for="(item, index) in datas"
      :key="index"
      @click="to(item)"
    >
      <v-col cols="12" sm="6" md="3" lg="3" class="new-sub-item new-thumbnail">
        <v-img
          :alt="item.title"
          class="thumbnail"
          :src="getStandardLink(item.img_path)"
          :lazy-src="getStandardLink(item.img_path)"
        ></v-img>
      </v-col>
      <v-col cols="12" sm="6" md="9" lg="9" class="new-sub-item new-content">
        <div class="new-title">
          <h4 class="text-title" v-html="item.title"></h4>
        </div>
        <div class="new-description">
          <p class="text-description" v-html="item.description"></p>
        </div>
        <div class="new-more-info">
          <span class="fa fa-clock-o"></span>&nbsp;&nbsp;{{ getDate(item.created_at) }}
          <span class="fa fa-eye"></span> &nbsp;{{ item.viewed }}
        </div>
      </v-col>
    </v-row>
  </v-col>
</template>
<script>
import themeService from './theme.service';
export default {
  mixins: [themeService],
};
</script>