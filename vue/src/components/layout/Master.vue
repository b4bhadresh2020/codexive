<template>
  <v-app id="inspire">
    <v-navigation-drawer v-model="drawer" app>
      <sidebar></sidebar>
    </v-navigation-drawer>

    <v-app-bar app>
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
      <v-toolbar-title></v-toolbar-title>
      <v-row class="text-left menu">
      <v-menu bottom min-width="200px" rounded offset-y>
        <template v-slot:activator="{ on }">
          <v-btn icon x-large v-on="on">
            <v-avatar color="brown" size="48">
              <span class="white--text headline">{{ user.initials }}</span>
            </v-avatar>
          </v-btn>
        </template>
        <v-card>
          <v-list-item-content class="justify-center">
            <div class="mx-auto text-center">
              <v-avatar color="brown">
                <span class="white--text headline">{{ user.initials }}</span>
              </v-avatar>
              <h3>{{ user.fullName }}</h3>
              <p class="caption mt-1">
                {{ user.email }}
              </p>
              <v-divider class="my-3"></v-divider>
              <v-btn depressed text> Edit Account </v-btn>
              <v-divider class="my-3"></v-divider>
              <v-btn depressed text @click="logout"> Logout </v-btn>
            </div>
          </v-list-item-content>
        </v-card>
      </v-menu>
      </v-row>
    </v-app-bar>
    <transition>
        <router-view></router-view>
    </transition>
  </v-app>
</template>

<script>
import sidebar from "./Sidebar.vue";
export default {
  data: () => ({
    drawer: null,
    user: {
      initials: "AD",
      fullName: "Admin",
      email: "admin@gmail.com",
    },
  }),
  components: {
    sidebar: sidebar,
  },
  methods: {
      logout(){
        localStorage.removeItem('access_token');
        this.$router.replace('/');
      }
  }
};
</script>
<style>
.menu{
    display: flex;
    place-content: flex-end;
}
</style>
