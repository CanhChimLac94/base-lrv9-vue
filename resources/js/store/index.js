import { createStore } from 'vuex';

const defaultMenus = [
  {
    icon: "mdi-apps",
    title: "Top News",
    to: "/",
    items: [],
  }
];

export const getters = {
  
}

export const state = () => ({
  counter: 11,
  app: {},
  topics: [],
  tags: [],
  news: [],
  newss: [],
  higlightPost: [],
  menus: defaultMenus,
  
  
})

export const mutations = {
  increment(state) {
    state.counter++
  },
  setState(state, payload) {
    state.app = {
      ...state.app,
      ...payload
    }
  },
  updateTags(state, payload){
    state.tags = payload;
  },
  updateTopics(state, payload) {
    state.topics = payload;
  },
  updateNews(state, payload) {
    state.news = payload;
  },
  updateMenus(state, payload) {
    state.menus = [
      ...defaultMenus,
      ...payload
    ];
  },
  updateItemDetail(state, payload) {
    state.itemDetail = payload;
  },
  updateAdminData(state, adminNew){
    state.admin = {
      ...state.admin,
      ...adminNew
    }
  },
  updateNewss(state, payload){
    state.newss = payload;
  },
  higlightPost(state, payload){
    state.higlightPost = payload;
  },

}

export const store = createStore({
  getters,
  state,
  mutations
});

export default store;