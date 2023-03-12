<template>
  <div class="d-flex text-caption">
    <v-btn text rel="noopener" disabled class="px-0" >
      <v-icon color="grey lighten-1" class="mx-2 ml-0 text-caption" small left >mdi-clock</v-icon>
      {{ date }}
    </v-btn>

    <v-btn text rel="noopener" disabled class="text-caption">
      <v-icon color="grey lighten-1" class="mx-2" small left>mdi-eye</v-icon>
      {{ viewed }}
    </v-btn>

    <v-spacer />
    <v-btn
      icon
      :href="`https://www.facebook.com/sharer/sharer.php?u=${url}`"
      target="_blank"
      rel="noopener"
      aria-label="share on facebook"
      small
    >
      <v-icon class="px-1" color="blue" medium>mdi-facebook</v-icon>
    </v-btn>
    <v-btn
      icon
      :href="`https://wa.me/?text=Checkout%20this%20page.%20${url}`"
      target="_blank"
      rel="noopener"
      aria-label="share on whatsapp"
      small
    >
      <v-icon class="px-1" color="green" medium>mdi-whatsapp</v-icon>
    </v-btn>
    <v-btn
      icon
      :href="`https://www.linkedin.com/shareArticle?mini=true&url=${url}`"
      target="_blank"
      rel="noopener"
      aria-label="share on linkedin"
      small
    >
      <v-icon class="px-1" color="blue darken-7" medium>mdi-linkedin</v-icon>
    </v-btn>
  </div>
</template>
<script>
export default {
  props: ["url", "date", "viewed"],
};
</script>