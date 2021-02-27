<template>

    <v-navigation-drawer permanent>
      <v-list-item>
        <v-list-item-content>
          <v-list-item-title class="title">
            Codexive
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>

      <v-divider></v-divider>

      <v-list
        dense
        nav
      >
        <v-list-item
          v-for="item in items"
          :key="item.title"
          link
        :to="item.link"
        active-class="blue--text"
        >
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
</template>

<script>
  export default {
    data () {
      return {
        items: [
            { title: 'Branch', icon: 'mdi-account-supervisor', link: '/branches'},
            { title: 'Accounts', icon: 'mdi-view-dashboard', link: '/accounts' },
            { title: 'Transactions', icon: 'mdi-image', link: '/transactions' },
            { title: 'About', icon: 'mdi-help-box', link: '/accounts2' },
        ],
        right: null,
      }
    },
  }
</script>
