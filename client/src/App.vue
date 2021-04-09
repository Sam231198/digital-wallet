<template>
  <div id="app">
    <v-navigation-drawer v-model="drawer" absolute bottom temporary dark app>
      <v-list nav dense>
        <v-list-item-group v-model="group">
          <router-link class="link" dark to="/">
            <v-list-item>
              <v-list-item-title> Consultar conta </v-list-item-title>
            </v-list-item>
          </router-link>

          <router-link class="link" dark to="/about">
            <v-list-item>
              <v-list-item-title> Caadastrar conta </v-list-item-title>
            </v-list-item>
          </router-link>

          <router-link class="link" dark to="/about">
            <v-list-item>
              <v-list-item-title> Realizar transfência </v-list-item-title>
            </v-list-item>
          </router-link>
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar dark app>
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-app-bar-title>Digital Wallet</v-app-bar-title>
    </v-app-bar>

    <!-- Sizes your content based upon application components -->
    <v-main>
      <!-- Provides the application the proper gutter -->
      <v-container fluid>
        <!-- If using vue-router -->
        <router-view />
      </v-container>
    </v-main>

    <v-footer app>
      <!-- -->
    </v-footer>
  </div>
</template>


<script>
export default {
  name: "App",
  data: () => ({
    drawer: false,
    group: null,
  }),

  watch: {
    group() {
      this.drawer = false;
    },
  },
};
</script>


<style lang="scss">
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
}

.link {
  text-decoration-line: none;
  color: white;
}
</style>
