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
            { title: 'Dashboard', icon: 'mdi-account-supervisor', link: '/analytics' },
            { title: 'Rojmel', icon: 'mdi-alarm-panel', link: '/rojmel' },
            { title: 'Branch', icon: 'mdi-account-supervisor', link: '/branches' },
            { title: 'Master Accounts', icon: 'mdi-view-dashboard', link: '/accounts' },
            { title: 'Asssign Account', icon: 'mdi-pencil', link: '/assign-account'},
            { title: 'Transactions', icon: 'mdi-bank-transfer', link: '/transactions' },
            { title: 'Expenses', icon: 'mdi-cash-multiple', link: '/expense' },
        ],
        right: null,
      }
    },
  }
</script>
