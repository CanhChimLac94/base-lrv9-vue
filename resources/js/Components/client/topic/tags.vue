<template>
  <v-row>
    <v-col class="flex">
      <v-chip-group
        column
        multiple
        center-active
        active-class="primary--text"
        class="topic-tags"
      >
        <v-chip
          class="text-center"
          v-for="(topic, index) in tags"
          :key="index"
          :to="`/danh-muc/${topic.id}/${urlFormat(topic.name)}`"
          router
          outlined
        >
          {{ topic.name }}
        </v-chip>
      </v-chip-group>
    </v-col>
  </v-row>
</template>
<style>
.topic-tags {
  padding-bottom: 25px;
  margin: auto;
}
</style>
<script>
import { mapState } from "vuex";
import defaultService from '../default.service';
export default {
  mixins: [defaultService],
  computed: {
    ...mapState({
      tags: (state) => state.tags || [],
    }),
  },
  methods: {
    init() {},
  },
  mounted() {
    this.init();
  },
};
</script>